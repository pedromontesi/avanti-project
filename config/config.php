<?php
/**
 * Arquivo de configuração do sistema
 * Define constantes, inicia sessão e configura usuários
 */

// Inicia a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurações de erro (desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações do sistema
define('APP_NAME', 'Sistema de Gestão de Produtos');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');

// Configurações de upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Usuários do sistema (sem registro público)
// Use password_hash('sua_senha', PASSWORD_DEFAULT) para gerar novos hashes
$USERS = [
    'admin' => password_hash('avanti', PASSWORD_DEFAULT),
    'manager' => password_hash('password123', PASSWORD_DEFAULT),
    'user' => password_hash('user123', PASSWORD_DEFAULT),
];

// Permissões por tipo de usuário (opcional - para implementação futura)
$PERMISSIONS = [
    'admin' => ['create', 'read', 'update', 'delete', 'export', 'backup'],
    'manager' => ['create', 'read', 'update', 'delete'],
    'user' => ['read']
];

/**
 * Função para verificar se o usuário está logado
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Função para obter o usuário atual
 * @return string|null
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Função para verificar permissão (implementação futura)
 * @param string $action Ação a ser verificada
 * @return bool
 */
function hasPermission($action) {
    global $PERMISSIONS;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $user = getCurrentUser();
    
    // Por enquanto, todos os usuários logados têm todas as permissões
    // Você pode implementar lógica de permissões aqui
    return true;
}

/**
 * Função para sanitizar entrada de dados
 * @param mixed $data Dados a serem sanitizados
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Função para validar upload de arquivo
 * @param array $file Arquivo do $_FILES
 * @return array [success => bool, message => string, filename => string|null]
 */
function validateFileUpload($file) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Parâmetros inválidos'];
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => true, 'message' => 'Nenhum arquivo enviado', 'filename' => null];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'message' => 'Arquivo excede o tamanho máximo permitido'];
        default:
            return ['success' => false, 'message' => 'Erro desconhecido no upload'];
    }
    
    // Verifica o tamanho
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Arquivo muito grande (máximo: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)'];
    }
    
    // Verifica a extensão
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }
    
    // Verifica se é realmente uma imagem
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'message' => 'Arquivo inválido'];
    }
    
    // Gera nome único para o arquivo
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    
    return [
        'success' => true,
        'message' => 'Arquivo válido',
        'filename' => $filename
    ];
}

/**
 * Função para formatar moeda brasileira
 * @param float $value Valor a ser formatado
 * @return string
 */
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Função para gerar token CSRF (implementações futuras)
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Função para validar token CSRF (implementação futura)
 * @param string $token Token a ser validado
 * @return bool
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}



// Headers de segurança
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Charset UTF-8
header('Content-Type: text/html; charset=UTF-8');
?>