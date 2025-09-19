<?php
/**
 * Classe Database
 * Gerencia a conexão com o banco de dados MySQL e criação automática de tabelas
 */
class Database {
    private $host = '127.0.0.1';
    private $db_name = 'product_management';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    /**
     * Construtor - Inicializa a conexão e verifica/cria estrutura do banco
     */
    public function __construct() {
        $this->connect();
        $this->createDatabaseIfNotExists();
        $this->createTablesIfNotExist();
    }
    

    private function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host}",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch(PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
    
    /**
     * Cria o banco de dados se não existir
     */
    private function createDatabaseIfNotExists() {
        try {
            $sql = "CREATE DATABASE IF NOT EXISTS `{$this->db_name}` 
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $this->conn->exec($sql);
            
            // Seleciona o banco de dados
            $this->conn->exec("USE `{$this->db_name}`");
            
            echo "<!-- Banco de dados verificado/criado com sucesso -->\n";
        } catch(PDOException $e) {
            die("Erro ao criar banco de dados: " . $e->getMessage());
        }
    }
    
    /**
     * Cria as tabelas necessárias se não existirem
     */
    private function createTablesIfNotExist() {
        try {
            // Tabela de produtos
            $sql = "CREATE TABLE IF NOT EXISTS `products` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `sku` VARCHAR(100) NOT NULL,
                `description` TEXT DEFAULT NULL,
                `price` DECIMAL(10,2) DEFAULT 0.00,
                `supplier` VARCHAR(255) DEFAULT NULL,
                `category` VARCHAR(100) DEFAULT NULL,
                `stock_quantity` INT(11) DEFAULT 0,
                `image` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `sku_unique` (`sku`),
                KEY `idx_name` (`name`),
                KEY `idx_category` (`category`),
                KEY `idx_supplier` (`supplier`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->conn->exec($sql);
            
            // Verifica se a coluna description existe, se não, adiciona
            $checkColumn = "SELECT COLUMN_NAME 
                           FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_SCHEMA = '{$this->db_name}' 
                           AND TABLE_NAME = 'products' 
                           AND COLUMN_NAME = 'description'";
            
            $result = $this->conn->query($checkColumn);
            if ($result->rowCount() == 0) {
                $alterTable = "ALTER TABLE `products` 
                              ADD COLUMN `description` TEXT DEFAULT NULL 
                              AFTER `sku`";
                $this->conn->exec($alterTable);
                echo "<!-- Coluna 'description' adicionada à tabela -->\n";
            }
            
            // Tabela de logs (opcional - para auditoria)
            $sql = "CREATE TABLE IF NOT EXISTS `product_logs` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `product_id` INT(11) DEFAULT NULL,
                `action` VARCHAR(50) NOT NULL,
                `user` VARCHAR(100) DEFAULT NULL,
                `details` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_product_id` (`product_id`),
                KEY `idx_action` (`action`),
                KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->conn->exec($sql);
            
            echo "<!-- Tabelas verificadas/criadas com sucesso -->\n";
            
        } catch(PDOException $e) {
            die("Erro ao criar tabelas: " . $e->getMessage());
        }
    }
    
    /**
     * Retorna a conexão PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Verifica o status da conexão
     * @return array
     */
    public function checkConnectionStatus() {
        try {
            $this->conn->query("SELECT 1");
            return [
                'status' => 'connected',
                'database' => $this->db_name,
                'host' => $this->host
            ];
        } catch(PDOException $e) {
            return [
                'status' => 'disconnected',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Fecha a conexão
     */
    public function closeConnection() {
        $this->conn = null;
    }
    
    /**
     * Executa backup do banco (método simplificado)
     * @return bool
     */
    public function backupDatabase() {
        try {
            $backupFile = __DIR__ . '/backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupDir = dirname($backupFile);
            
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0777, true);
            }
            
            // Query para obter todas as tabelas
            $tables = $this->conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            $output = "-- Backup do banco de dados: {$this->db_name}\n";
            $output .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                // Estrutura da tabela
                $createTable = $this->conn->query("SHOW CREATE TABLE `$table`")->fetch();
                $output .= "\n-- Estrutura da tabela `$table`\n";
                $output .= "DROP TABLE IF EXISTS `$table`;\n";
                $output .= $createTable['Create Table'] . ";\n\n";
                
                // Dados da tabela
                $rows = $this->conn->query("SELECT * FROM `$table`")->fetchAll();
                if (!empty($rows)) {
                    $output .= "-- Dados da tabela `$table`\n";
                    foreach ($rows as $row) {
                        $values = array_map([$this->conn, 'quote'], array_values($row));
                        $output .= "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n";
                    }
                }
            }
            
            file_put_contents($backupFile, $output);
            return true;
            
        } catch(Exception $e) {
            error_log("Erro no backup: " . $e->getMessage());
            return false;
        }
    }
}

// Inicialização automática ao incluir o arquivo
$pdo = null;
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch(Exception $e) {
    die("Erro fatal na inicialização do banco de dados: " . $e->getMessage());
}
?>
