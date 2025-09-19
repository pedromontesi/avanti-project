<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Login -  Avanti Inventory management </title>
</head>

<body>
    <div class="login-container">
        <h2 class="text-tittle">
            <img src="../storage/img/loginCube.svg" alt="">
            Avanti Inventory<br> Management
        </h2>
        <h3>Acesse sua conta para gerenciar o estoque</h3>
        <form method="POST">
            <div class="form-group form-username">
                <label class="text-secundary">Usuário</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group form-password">
                <label class="text-secundary">Senha</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-login">
                <img src="../storage/img/loginEnter.svg" alt="">
                Entrar
            </button>
            <?php if (isset($loginError)): ?>
                <div class="alert alert-error"><?= $loginError ?></div>
            <?php endif; ?>
            <h4 class="text-secundary">Esqueceu sua senha? Contate o administrador.</h4>
        </form>
    </div>
</body>

</html>