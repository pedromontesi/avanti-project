<div class="search-box">
    <h2 class="text-tittle">Produtos</h2>
    <form method="GET" class="search-form">
        <div class="search-container">
            <button type="submit" class="search-icon">
                <img src="../../storage/img/search.svg" alt="">
            </button>
            <input type="text" class="search" name="search" placeholder="Buscar por nome..."
                value="<?= htmlspecialchars($searchTerm ?? '') ?>">
        </div>
        <?php if ($searchTerm): ?>
            <a href="index.php" class="btn btn-primary">Limpar</a>
        <?php endif; ?>
    </form>

    <!-- BOTÃO ADICIONAR PRODUTO -->
    <div>
        <button onclick="ProductManager.openAddModal()" class="btn btn-primary">
            <img src="../../storage/img/plus.svg" alt="">
            Adicionar Produto
        </button>
    </div>
</div>
