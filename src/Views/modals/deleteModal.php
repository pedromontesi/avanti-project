<link rel="stylesheet" href="./assets/css/product/productDelete.css">
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Excluir Produto</h2>
            <span class="close-modal" onclick="ProductManager.closeModal('deleteModal')">&times;</span>
        </div>
        <div>
            <p class="alert-box">Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.</p>
            <form method="POST">
                <input type="hidden" name="delete_id" id="deleteProductId">
                <input type="hidden" name="delete_confirm" value="1">
                <div class="danger-box">
                    <td class="actions"></td>
                    <span>
                        ⚠ Ação permanente</span>
                </div>
        </div>
        <div class="btn-delete-container">
            <button type="button" onclick="ProductManager.closeModal('deleteModal')"
                class="btn btn-edit">Cancelar</button>
            <button type="submit" class="btn btn-danger">
                <img src="" alt="">
                Excluir Produto</button>
        </div>
        </form>
    </div>
</div>
</div>