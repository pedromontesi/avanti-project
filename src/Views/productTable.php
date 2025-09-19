<div class="product-table">
    <div class="grid-table">
        <!-- Cabeçalho -->
        <div class="grid-header">Nome</div>
        <div class="grid-header">Preço</div>
        <div class="grid-header">Estoque</div>
        <div class="grid-header">Ações</div>

        <?php if (empty($products)): ?>
            <div class="grid-cell" style="grid-column: 1 / -1; text-align: center; padding: 20px;">
                <?php if ($searchTerm): ?>
                    Nenhum produto encontrado para "<?= htmlspecialchars($searchTerm) ?>"
                <?php else: ?>
                    Nenhum produto cadastrado ainda
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="grid-row <?= $index % 2 === 0 ? 'odd' : '' ?>">
                    <div class="grid-cell"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="grid-cell">R$ <?= number_format($product['price'], 2, ',', '.') ?></div>
                    <div class="grid-cell">
                        <span>
                            <?= htmlspecialchars($product['stock_quantity']) ?>
                        </span>
                    </div>
                    <div class="grid-cell">
                        <button onclick="ProductManager.openEditModal(<?= htmlspecialchars(json_encode($product)) ?>)"
                            class="btn btn-edit">
                            <img src="../../storage/img/pencil.svg" alt="">
                            Editar</button>
                        <button
                            onclick="ProductManager.openDeleteModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>')"
                            class="btn btn-danger">
                            <img src="../../storage/img/delete.svg" alt="">
                            Excluir
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>