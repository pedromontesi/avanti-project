<?php
/**
 * Classe ProductService
 * Gerencia todas as operações CRUD de produtos com proteção contra SQL Injection
 */
class ProductService {
    private $pdo;
    private $table = 'products';
    
    /**
     * Construtor
     * @param PDO $pdo Conexão com o banco de dados
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Cria um novo produto
     * @param array $data Dados do produto
     * @return int|false ID do produto inserido ou false em caso de erro
     */
    public function create(array $data) {
        try {
            // Validação de dados
            $validatedData = $this->validateProductData($data);
            
            $sql = "INSERT INTO {$this->table} 
                    (name, sku, description, price, supplier, category, stock_quantity, image) 
                    VALUES 
                    (:name, :sku, :description, :price, :supplier, :category, :stock_quantity, :image)";
            
            $stmt = $this->pdo->prepare($sql);
            
            // Bind dos parâmetros com proteção contra SQL Injection
            $stmt->bindParam(':name', $validatedData['name'], PDO::PARAM_STR);
            $stmt->bindParam(':sku', $validatedData['sku'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $validatedData['description'], PDO::PARAM_STR);
            $stmt->bindParam(':price', $validatedData['price'], PDO::PARAM_STR);
            $stmt->bindParam(':supplier', $validatedData['supplier'], PDO::PARAM_STR);
            $stmt->bindParam(':category', $validatedData['category'], PDO::PARAM_STR);
            $stmt->bindParam(':stock_quantity', $validatedData['stock_quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':image', $validatedData['image'], PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $this->logAction('CREATE', $this->pdo->lastInsertId(), $validatedData);
                return $this->pdo->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Erro ao criar produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca todos os produtos
     * @param int $limit Limite de resultados
     * @param int $offset Offset para paginação
     * @return array Lista de produtos
     */
    public function getAll($limit = null, $offset = 0) {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit OFFSET :offset";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->query($sql);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca um produto por ID
     * @param int $id ID do produto
     * @return array|false Produto ou false se não encontrado
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca produtos por termo de pesquisa
     * @param string $searchTerm Termo de busca
     * @return array Lista de produtos encontrados
     */
    public function search($searchTerm) {
        try {
            // Sanitiza o termo de busca
            $searchTerm = trim($searchTerm);
            if (empty($searchTerm)) {
                return $this->getAll();
            }
            
            // Prepara o termo para busca LIKE
            $likeTerm = '%' . $searchTerm . '%';
            
            $sql = "SELECT * FROM {$this->table} 
                    WHERE name LIKE :term 
                    OR sku LIKE :term 
                    OR description LIKE :term 
                    OR supplier LIKE :term 
                    OR category LIKE :term 
                    ORDER BY 
                        CASE 
                            WHEN name LIKE :exactTerm THEN 1
                            WHEN sku = :searchTerm THEN 2
                            WHEN name LIKE :term THEN 3
                            ELSE 4
                        END,
                        id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':term', $likeTerm, PDO::PARAM_STR);
            $stmt->bindParam(':exactTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro na busca: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Atualiza um produto
     * @param int $id ID do produto
     * @param array $data Novos dados do produto
     * @return bool Sucesso ou falha
     */
    public function update($id, array $data) {
        try {
            // Validação de dados
            $validatedData = $this->validateProductData($data);
            
            // Busca produto atual para log
            $currentProduct = $this->getById($id);
            
            $sql = "UPDATE {$this->table} SET 
                    name = :name,
                    sku = :sku,
                    description = :description,
                    price = :price,
                    supplier = :supplier,
                    category = :category,
                    stock_quantity = :stock_quantity,
                    image = :image
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            // Bind dos parâmetros
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $validatedData['name'], PDO::PARAM_STR);
            $stmt->bindParam(':sku', $validatedData['sku'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $validatedData['description'], PDO::PARAM_STR);
            $stmt->bindParam(':price', $validatedData['price'], PDO::PARAM_STR);
            $stmt->bindParam(':supplier', $validatedData['supplier'], PDO::PARAM_STR);
            $stmt->bindParam(':category', $validatedData['category'], PDO::PARAM_STR);
            $stmt->bindParam(':stock_quantity', $validatedData['stock_quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':image', $validatedData['image'], PDO::PARAM_STR);
            
            $success = $stmt->execute();
            
            if ($success) {
                $this->logAction('UPDATE', $id, [
                    'before' => $currentProduct,
                    'after' => $validatedData
                ]);
            }
            
            return $success;
            
        } catch (PDOException $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deleta um produto
     * @param int $id ID do produto
     * @return bool Sucesso ou falha
     */
    public function delete($id) {
        try {
            // Busca produto para log antes de deletar
            $product = $this->getById($id);
            
            // Deleta a imagem se existir
            if ($product && !empty($product['image'])) {
                $imagePath = __DIR__ . '/../../public/uploads/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $success = $stmt->execute();
            
            if ($success && $product) {
                $this->logAction('DELETE', $id, $product);
            }
            
            return $success;
            
        } catch (PDOException $e) {
            error_log("Erro ao deletar produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valida e sanitiza dados do produto
     * @param array $data Dados a serem validados
     * @return array Dados validados e sanitizados
     */
  private function validateProductData(array $data) {
    $validated = [];

    // helper para sanitizar strings
    $sanitize = fn($value) => $value !== null ? trim(strip_tags($value)) : null;

    // Nome - obrigatório
    $validated['name'] = $sanitize($data['name'] ?? null);
    if (empty($validated['name'])) {
        throw new InvalidArgumentException("Nome do produto é obrigatório");
    }

    // SKU - obrigatório
    $validated['sku'] = $sanitize($data['sku'] ?? null);
    if (empty($validated['sku'])) {
        throw new InvalidArgumentException("SKU é obrigatório");
    }

    // Descrição - opcional
    $validated['description'] = $sanitize($data['description'] ?? null);

    // Preço - opcional, padrão 0
    $validated['price'] = (isset($data['price']) && is_numeric($data['price']))
        ? floatval($data['price'])
        : 0.00;

    // Fornecedor - opcional
    $validated['supplier'] = $sanitize($data['supplier'] ?? null);

    // Categoria - opcional
    $validated['category'] = $sanitize($data['category'] ?? null);

    // Quantidade em estoque - opcional, padrão 0
    $validated['stock_quantity'] = (isset($data['stock_quantity']) && is_numeric($data['stock_quantity']))
        ? intval($data['stock_quantity'])
        : 0;

    // Imagem - opcional
    $validated['image'] = $sanitize($data['image'] ?? null);

    return $validated;
}
    
    /**
     * Registra ações no log
     * @param string $action Ação realizada
     * @param int $productId ID do produto
     * @param mixed $details Detalhes da ação
     */
    private function logAction($action, $productId, $details = null) {
        try {
            $user = $_SESSION['user'] ?? 'system';
            $detailsJson = $details ? json_encode($details) : null;
            
            $sql = "INSERT INTO product_logs (product_id, action, user, details) 
                    VALUES (:product_id, :action, :user, :details)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':user', $user, PDO::PARAM_STR);
            $stmt->bindParam(':details', $detailsJson, PDO::PARAM_STR);
            
            $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }
    
    /**
     * Conta total de produtos
     * @return int Total de produtos
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return intval($result['total']);
            
        } catch (PDOException $e) {
            error_log("Erro ao contar produtos: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Busca produtos com estoque baixo
     * @param int $threshold Limite de estoque
     * @return array Produtos com estoque baixo
     */
    public function getLowStock($threshold = 10) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE stock_quantity <= :threshold 
                    ORDER BY stock_quantity ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos com estoque baixo: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém estatísticas dos produtos
     * @return array Estatísticas
     */
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total de produtos
            $stats['total_products'] = $this->count();
            
            // Valor total do estoque
            $sql = "SELECT SUM(price * stock_quantity) as total_value FROM {$this->table}";
            $result = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            $stats['total_stock_value'] = floatval($result['total_value'] ?? 0);
            
            // Produtos por categoria
            $sql = "SELECT category, COUNT(*) as count FROM {$this->table} 
                    GROUP BY category ORDER BY count DESC";
            $stats['by_category'] = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            // Produtos com estoque baixo
            $stats['low_stock_count'] = count($this->getLowStock());
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Erro ao obter estatísticas: " . $e->getMessage());
            return [];
        }
    }
}
?>