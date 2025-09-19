<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/Services/ProductService.php';

$database = new Database();
$pdo = $database->getConnection();
$productService = new ProductService($pdo);

// LOGIN
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (isset($USERS[$username]) && password_verify($password, $USERS[$username])) {
        $_SESSION['user'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $loginError = "Credenciais inválidas. Verifique usuário e senha.";
    }
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// VERIFICAR LOGIN
$isLoggedIn = isset($_SESSION['user']);

// PROCESSAR CRUD (apenas se logado)
if ($isLoggedIn) {
    // CREATE/UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['login'])) {
        if (isset($_POST['action'])) {
            $data = [
                'name' => trim($_POST['name']),
                'sku' => trim($_POST['sku']),
                'description' => trim($_POST['description']),
                'price' => filter_var($_POST['price'], FILTER_VALIDATE_FLOAT),
                'supplier' => trim($_POST['supplier']),
                'category' => trim($_POST['category']),
                'stock_quantity' => filter_var($_POST['stock_quantity'], FILTER_VALIDATE_INT),
            ];
            
            // Upload de imagem
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Validar arquivo
                $validation = validateFileUpload($_FILES['image']);
                if ($validation['success'] && $validation['filename']) {
                    $targetFile = $uploadDir . $validation['filename'];
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $data['image'] = $validation['filename'];
                    } else {
                        $data['image'] = $_POST['current_image'] ?? null;
                    }
                } else {
                    $data['image'] = $_POST['current_image'] ?? null;
                }
            } else {
                $data['image'] = $_POST['current_image'] ?? null;
            }

            if ($_POST['action'] === 'update' && !empty($_POST['id'])) {
                $productService->update($_POST['id'], $data);
            } elseif ($_POST['action'] === 'create') {
                $productService->create($data);
            }

            header("Location: index.php");
            exit;
        }
    }

    // DELETE
    if (isset($_POST['delete_confirm'])) {
        $productService->delete($_POST['delete_id']);
        header("Location: index.php");
        exit;
    }

    // BUSCA
    $searchTerm = filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW);
    $searchTerm = $searchTerm ? trim($searchTerm) : null;

    if ($searchTerm) {
        $products = $productService->search($searchTerm);
    } else {
        $products = $productService->getAll();
    }
    
    $totalProducts = $productService->count();
}

// Incluir a view apropriada
if ($isLoggedIn) {
    include '../src/Views/dashboard.php';
} else {
    include '../src/Views/login.php';
}
?>