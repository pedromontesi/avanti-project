<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <img src="../storage/img/productAdd.svg" alt="">
                <span id="modalTitle">Adicionar Produto</span>
            </h2>
            <span class="close-modal" onclick="ProductManager.closeModal('productModal')">&times;</span>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="productId">
            <input type="hidden" name="current_image" id="currentImage">

            <div class="form-row">
                <div class="form-group">
                    <label class="text-secundary">Nome do Produto</label>
                    <input type="text" name="name" id="productName" placeholder="Ex.: Camiseta Básica Avanti" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="text-secundary">SKU</label>
                    <input type="text" name="sku" id="productSku" required placeholder="AVT-001">
                </div>
                <div class="form-group">
                    <label class="text-secundary">Categoria</label>
                    <input type="text" name="category" id="productCategory" placeholder="Ex.: Vestuário">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="text-secundary">Preço (R$)</label>
                    <input type="number" step="0.01" name="price" id="productPrice" placeholder="0,00">
                </div>
                <div class="form-group">
                    <label class="text-secundary">Quantidade em Estoque</label>
                    <input type="number" name="stock_quantity" id="productStock" placeholder="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="text-secundary">Fornecedor</label>
                    <input type="text" name="supplier" id="productSupplier" placeholder="Nome do fornecedor">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label class="text-secundary">Descrição</label>
                    <textarea name="description" id="productDescription" rows="4" placeholder="Inclua informações como material, dimensões ou cuidados."></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label class="text-secundary">Imagem do Produto</label>
                    <input type="file" name="image" accept="image/*" onchange="ProductManager.previewImage(this)">
                    <div id="imagePreview"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="ProductManager.closeModal('productModal')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="submitButton">Salvar Produto</button>
            </div>
        </form>
    </div>
</div>