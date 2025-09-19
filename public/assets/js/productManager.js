/**
 * ProductManager - Gerenciador de produtos com modais
 */
const ProductManager = {
    /**
     * Abre modal para adicionar novo produto
     */
    openAddModal() {
        const modal = document.getElementById('productModal');
        if (!modal) {
            console.error('Modal productModal não encontrado');
            return;
        }

        // Limpar campos
        this.clearForm();
        
        // Configurar para criação (apenas se o elemento existir)
        const modalTitle = document.getElementById('modalTitle');
        if (modalTitle) {
            modalTitle.textContent = 'Adicionar Produto';
        }
        
        const formAction = document.getElementById('formAction');
        if (formAction) {
            formAction.value = 'create';
        }
        
        // Mostrar modal
        modal.classList.add('active');
        
        // Focus no primeiro campo (apenas se existir)
        const productName = document.getElementById('productName');
        if (productName) {
            productName.focus();
        }
    },

    /**
     * Abre modal para editar produto existente
     * @param {Object} product - Dados do produto
     */
    openEditModal(product) {
        const modal = document.getElementById('productModal');
        if (!modal) {
            console.error('Modal productModal não encontrado');
            return;
        }

        if (!product) {
            console.error('Dados do produto não fornecidos');
            return;
        }

        // Configurar campos (apenas se os elementos existirem)
        const modalTitle = document.getElementById('modalTitle');
        if (modalTitle) {
            modalTitle.textContent = 'Editar Produto';
        }
        
        const formAction = document.getElementById('formAction');
        if (formAction) {
            formAction.value = 'update';
        }
        
        const productId = document.getElementById('productId');
        if (productId) {
            productId.value = product.id || '';
        }
        
        const productName = document.getElementById('productName');
        if (productName) {
            productName.value = product.name || '';
        }
        
        const productSku = document.getElementById('productSku');
        if (productSku) {
            productSku.value = product.sku || '';
        }
        
        const productDescription = document.getElementById('productDescription');
        if (productDescription) {
            productDescription.value = product.description || '';
        }
        
        const productPrice = document.getElementById('productPrice');
        if (productPrice) {
            productPrice.value = product.price || '';
        }
        
        const productSupplier = document.getElementById('productSupplier');
        if (productSupplier) {
            productSupplier.value = product.supplier || '';
        }
        
        const productCategory = document.getElementById('productCategory');
        if (productCategory) {
            productCategory.value = product.category || '';
        }
        
        const productStock = document.getElementById('productStock');
        if (productStock) {
            productStock.value = product.stock_quantity || '';
        }
        
        const currentImage = document.getElementById('currentImage');
        if (currentImage) {
            currentImage.value = product.image || '';
        }
        
        // Preview da imagem atual (apenas se o elemento existir)
        const imagePreview = document.getElementById('imagePreview');
        if (imagePreview) {
            if (product.image) {
                imagePreview.innerHTML = `<img src="uploads/${product.image}" style="max-width: 100px; height: auto; border-radius: 4px;" alt="Imagem atual">`;
            } else {
                imagePreview.innerHTML = '';
            }
        }
        
        // Mostrar modal
        modal.classList.add('active');
        
        // Focus no primeiro campo (apenas se existir)
        if (productName) {
            productName.focus();
        }
    },

    /**
     * Abre modal de confirmação de exclusão
     * @param {number} id - ID do produto
     * @param {string} name - Nome do produto
     */
    openDeleteModal(id, name) {
        const modal = document.getElementById('deleteModal');
        if (!modal) {
            console.error('Modal deleteModal não encontrado');
            return;
        }

        const deleteProductId = document.getElementById('deleteProductId');
        if (deleteProductId) {
            deleteProductId.value = id;
        }
        
        const deleteProductName = document.getElementById('deleteProductName');
        if (deleteProductName) {
            deleteProductName.textContent = name;
        }
        
        modal.classList.add('active');
    },

    /**
     * Fecha modal específico
     * @param {string} modalId - ID do modal a ser fechado
     */
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    },

    /**
     * Preview de imagem selecionada
     * @param {HTMLInputElement} input - Input de arquivo
     */
    previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (!preview) {
            console.error('Elemento imagePreview não encontrado');
            return;
        }

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; height: auto; border-radius: 4px;" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = '';
        }
    },

    /**
     * Limpa todos os campos do formulário
     */
    clearForm() {
        const fields = [
            'productId', 'productName', 'productSku', 'productDescription',
            'productPrice', 'productSupplier', 'productCategory', 
            'productStock', 'currentImage'
        ];

        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = '';
            }
        });

        const imagePreview = document.getElementById('imagePreview');
        if (imagePreview) {
            imagePreview.innerHTML = '';
        }

        // Limpar input de arquivo
        const fileInput = document.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.value = '';
        }
    },

    /**
     * Inicializa eventos do gerenciador
     */
    init() {
        // Fechar modal ao clicar fora
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    activeModal.classList.remove('active');
                }
            }
        });

        console.log('ProductManager inicializado com sucesso');
    }
};

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    ProductManager.init();
});

// Tornar disponível globalmente (compatibilidade com onclick)
window.ProductManager = ProductManager;