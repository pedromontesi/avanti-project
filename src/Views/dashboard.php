<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/style.css">
    <link rel="icon" href="../../storage/img/productCube.svg" type="image/svg+xml">
    <title>Dashboard - Avanti Inventory management </title>
</head>

<body>
    <!-- HEADER -->
    <?php include __DIR__ . '/header.php'; ?>

    <div class="container">
        <!-- CAIXA DE BUSCA -->
        <?php include __DIR__ . '/search.php'; ?>

        <!-- TABELA DE PRODUTOS -->
        <?php include __DIR__ . '/productTable.php'; ?>
    </div>

    <!-- MODALS -->
    <?php include __DIR__ . '/modals/productModal.php'; ?>
    <?php include __DIR__ . '/modals/deleteModal.php'; ?>

    <script src="../../public/assets/js/productManager.js"></script>
</body>

</html>