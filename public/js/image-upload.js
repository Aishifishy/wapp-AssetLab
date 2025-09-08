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
        // Validate file size
        if (file.size > this.maxFileSize) {
            alert(`File size must be less than ${this.maxFileSize / (1024 * 1024)}MB`);
            return false;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return false;
        }

        return true;
    }

    showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.previewImage.src = e.target.result;
            this.imagePreview.classList.remove('hidden');
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
