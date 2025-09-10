/**
 * Image Upload Handler
 * Handles image upload functionality including drag-drop, preview, and validation
 */
class ImageUploadHandler {
    constructor(options = {}) {
        this.imageInputId = options.imageInputId || 'form_image';
        this.imagePreviewId = options.imagePreviewId || 'image-preview';
        this.previewImageId = options.previewImageId || 'preview-image';
        this.removeImageBtnId = options.removeImageBtnId || 'remove-image';
        this.maxFileSize = options.maxFileSize || 10 * 1024 * 1024; // 10MB default
        
        this.init();
    }

    init() {
        this.imageInput = document.getElementById(this.imageInputId);
        this.imagePreview = document.getElementById(this.imagePreviewId);
        this.previewImage = document.getElementById(this.previewImageId);
        this.removeImageBtn = document.getElementById(this.removeImageBtnId);

        if (this.imageInput) {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        // File input change event
        this.imageInput.addEventListener('change', (e) => this.handleFileChange(e));

        // Remove image functionality
        if (this.removeImageBtn) {
            this.removeImageBtn.addEventListener('click', () => this.removeImage());
        }

        // Drag and drop functionality
        const dropZone = this.imageInput.closest('.border-dashed');
        if (dropZone) {
            this.setupDragAndDrop(dropZone);
        }
    }

    handleFileChange(e) {
        const file = e.target.files[0];
        if (file) {
            if (this.validateFile(file)) {
                this.showPreview(file);
            } else {
                this.clearInput();
            }
        }
    }

    validateFile(file) {
        // Validate file size (10MB max)
        if (file.size > this.maxFileSize) {
            this.showError(`File size must be less than ${this.maxFileSize / (1024 * 1024)}MB`);
            return false;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            this.showError('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
            return false;
        }

        // Validate file extension
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        const extension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(extension)) {
            this.showError('Invalid file extension. Only JPG, PNG, GIF, and WebP files are allowed.');
            return false;
        }

        return true;
    }

    showError(message) {
        // Create or update error message display
        let errorDiv = document.getElementById('image-upload-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'image-upload-error';
            errorDiv.className = 'mt-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded p-2';
            this.imageInput.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        
        // Hide error after 5 seconds
        setTimeout(() => {
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        }, 5000);
    }

    hideError() {
        const errorDiv = document.getElementById('image-upload-error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.previewImage.src = e.target.result;
            this.imagePreview.classList.remove('hidden');
            this.hideError(); // Hide any previous errors
        };
        reader.readAsDataURL(file);
    }

    removeImage() {
        this.clearInput();
        this.imagePreview.classList.add('hidden');
        this.previewImage.src = '';
    }

    clearInput() {
        this.imageInput.value = '';
    }

    setupDragAndDrop(dropZone) {
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.imageInput.files = files;
                this.imageInput.dispatchEvent(new Event('change'));
            }
        });
    }
}

// Export for module usage or make globally available
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ImageUploadHandler;
} else {
    window.ImageUploadHandler = ImageUploadHandler;
}
