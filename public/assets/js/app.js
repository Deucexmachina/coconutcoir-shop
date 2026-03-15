document.addEventListener('DOMContentLoaded', () => {
    const modalDismissButtons = document.querySelectorAll('[data-dismiss-modal]');
    modalDismissButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const selector = button.getAttribute('data-dismiss-modal');
            if (!selector) {
                return;
            }
            const modal = document.querySelector(selector);
            if (modal) {
                modal.classList.remove('is-visible');
            }
        });
    });

    const cartQtyInputs = document.querySelectorAll('input[data-auto-submit="change"]');
    cartQtyInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const form = input.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    const previewInputs = document.querySelectorAll('input[data-image-preview-input]');
    previewInputs.forEach((input) => {
        const targetSelector = input.getAttribute('data-image-preview-input');
        if (!targetSelector) {
            return;
        }

        const previewImage = document.querySelector(targetSelector);
        if (!previewImage) {
            return;
        }

        const fallbackSrc = previewImage.getAttribute('src') || '';
        const syncPreview = () => {
            const value = input.value.trim();
            previewImage.src = value || fallbackSrc;
        };

        input.addEventListener('input', syncPreview);
        input.addEventListener('change', syncPreview);

        previewImage.addEventListener('error', () => {
            previewImage.src = fallbackSrc;
        });
    });
});
