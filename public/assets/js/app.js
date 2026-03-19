document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const savedTheme = localStorage.getItem('coircraft_theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('coircraft_theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
        });
    }

    const settingsMenu = document.querySelector('[data-settings-menu]');
    const settingsTrigger = document.querySelector('[data-settings-trigger]');
    const settingsDropdown = document.querySelector('[data-settings-dropdown]');
    if (settingsMenu && settingsTrigger && settingsDropdown) {
        settingsTrigger.addEventListener('click', () => {
            settingsDropdown.classList.toggle('is-open');
        });
        document.addEventListener('click', (event) => {
            if (!settingsMenu.contains(event.target)) {
                settingsDropdown.classList.remove('is-open');
            }
        });
    }

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

    const showCartToast = (message, itemName = '') => {
        const existing = document.getElementById('cartToast');
        if (existing) {
            existing.remove();
        }

        const toast = document.createElement('div');
        toast.className = 'cart-toast';
        toast.id = 'cartToast';

        const content = document.createElement('div');
        content.className = 'cart-toast-content';

        const messageWrap = document.createElement('div');
        messageWrap.className = 'cart-toast-message';

        const strong = document.createElement('strong');
        strong.textContent = message;
        messageWrap.appendChild(strong);

        if (itemName) {
            const muted = document.createElement('div');
            muted.className = 'muted';
            muted.textContent = itemName;
            messageWrap.appendChild(muted);
        }

        const actions = document.createElement('div');
        actions.className = 'cart-toast-actions';
        const cartLink = document.createElement('a');
        cartLink.className = 'btn';
        cartLink.href = '/cart';
        cartLink.textContent = 'View Cart';
        actions.appendChild(cartLink);

        content.appendChild(messageWrap);
        content.appendChild(actions);
        toast.appendChild(content);

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    const updateCsrf = (csrf) => {
        if (!csrf || !csrf.tokenName || !csrf.hash) {
            return;
        }

        const csrfInputs = document.querySelectorAll(`input[name="${csrf.tokenName}"]`);
        csrfInputs.forEach((input) => {
            input.value = csrf.hash;
        });
    };

    const addToCartForms = document.querySelectorAll('form[action*="/cart/add/"]');
    addToCartForms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (!window.fetch || !window.FormData) {
                return;
            }

            event.preventDefault();

            const submitControl = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitControl) {
                submitControl.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });

                const payload = await response.json();
                updateCsrf(payload.csrf || null);

                if (!response.ok || payload.success === false) {
                    showCartToast(payload.message || 'Unable to add item to cart right now.');
                    return;
                }

                const cartBubble = document.querySelector('.cart-bubble');
                if (cartBubble && Number.isFinite(Number(payload.cart_count))) {
                    cartBubble.textContent = String(payload.cart_count);
                }

                showCartToast(payload.message || 'Item added to cart!', payload.name || '');
            } catch (error) {
                showCartToast('Unable to add item to cart right now.');
            } finally {
                if (submitControl) {
                    submitControl.disabled = false;
                }
            }
        });
    });

    const qtyStepButtons = document.querySelectorAll('[data-qty-step]');
    qtyStepButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const step = Number(button.getAttribute('data-qty-step'));
            const stepper = button.closest('.qty-stepper');
            if (!stepper || Number.isNaN(step)) {
                return;
            }

            const input = stepper.querySelector('input[type="number"]');
            if (!input) {
                return;
            }

            const min = Number(input.getAttribute('min') || 1);
            const max = Number(input.getAttribute('max') || 999999);
            const current = Number(input.value || min);
            const next = Math.max(min, Math.min(max, current + step));

            input.value = String(next);
            input.dispatchEvent(new Event('change', { bubbles: true }));
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

    const savedAddressSelect = document.getElementById('savedAddressSelect');
    const shippingAddressInput = document.getElementById('shippingAddressInput');
    if (savedAddressSelect && shippingAddressInput) {
        savedAddressSelect.addEventListener('change', () => {
            if (savedAddressSelect.value.trim() !== '') {
                shippingAddressInput.value = savedAddressSelect.value;
            }
        });
    }

    const voucherCodeInput = document.getElementById('voucherCodeInput');
    const voucherDetails = document.getElementById('voucherDetails');
    const deliveryMethodInput = document.getElementById('deliveryMethodInput');
    const shippingFeeValue = document.getElementById('shippingFeeValue');
    const discountValue = document.getElementById('discountValue');
    const totalValue = document.getElementById('totalValue');
    const subtotal = Number(window.checkoutSubtotal || 0);
    const shippingEstimate = Number(window.checkoutShippingEstimate || 0);
    const vouchers = Array.isArray(window.checkoutVouchers) ? window.checkoutVouchers : [];

    const formatMoney = (amount) => `PHP ${amount.toFixed(2)}`;
    const recalculateCheckout = () => {
        if (!deliveryMethodInput || !shippingFeeValue || !discountValue || !totalValue) {
            return;
        }

        const deliveryMode = deliveryMethodInput.value;
        const shippingFee = deliveryMode === 'Delivery' ? shippingEstimate : 0;
        let discount = 0;
        let detailText = 'No voucher applied';

        if (voucherCodeInput && voucherCodeInput.value.trim() !== '') {
            const code = voucherCodeInput.value.trim().toUpperCase();
            const voucher = vouchers.find((v) => String(v.code).toUpperCase() === code && Number(v.is_active) === 1);
            if (!voucher) {
                detailText = 'Voucher not found';
            } else if (voucher.type === 'free_shipping') {
                discount = shippingFee;
                detailText = `${voucher.code}: Free shipping voucher`;
            } else if (voucher.type === 'percent_discount') {
                const pct = Number(voucher.value || 0);
                discount = +(subtotal * (pct / 100)).toFixed(2);
                detailText = `${voucher.code}: ${pct}% off voucher`;
            }
        }

        const total = Math.max(0, subtotal + shippingFee - discount);
        shippingFeeValue.textContent = formatMoney(shippingFee);
        discountValue.textContent = `- ${formatMoney(discount)}`;
        totalValue.textContent = formatMoney(total);
        if (voucherDetails) {
            voucherDetails.textContent = detailText;
        }
    };

    if (deliveryMethodInput) {
        deliveryMethodInput.addEventListener('change', recalculateCheckout);
    }
    if (voucherCodeInput) {
        voucherCodeInput.addEventListener('input', recalculateCheckout);
    }
    recalculateCheckout();

    const starGroups = document.querySelectorAll('[data-star-pick]');
    starGroups.forEach((group) => {
        const hiddenInput = group.querySelector('input[type="hidden"]');
        const buttons = Array.from(group.querySelectorAll('button[data-star-value]'));
        if (!hiddenInput || buttons.length === 0) {
            return;
        }

        const syncStars = (score) => {
            buttons.forEach((btn) => {
                const value = Number(btn.getAttribute('data-star-value'));
                btn.classList.toggle('is-active', value <= score);
            });
        };

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const value = Number(btn.getAttribute('data-star-value'));
                hiddenInput.value = String(value);
                syncStars(value);
            });
        });

        syncStars(Number(hiddenInput.value || 0));
    });

    const revealItems = document.querySelectorAll('.reveal-on-scroll');
    if (revealItems.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealItems.forEach((item, index) => {
            item.style.transitionDelay = `${Math.min(index * 40, 240)}ms`;
            observer.observe(item);
        });
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }

    const carousels = document.querySelectorAll('[data-carousel]');
    carousels.forEach((carousel) => {
        const key = carousel.getAttribute('data-carousel');
        const track = carousel.querySelector('[data-carousel-track]');
        if (!key || !track) {
            return;
        }

        const cards = Array.from(track.children);
        if (cards.length <= 3) {
            return;
        }

        const prevBtn = document.querySelector(`[data-carousel-prev="${key}"]`);
        const nextBtn = document.querySelector(`[data-carousel-next="${key}"]`);
        let startIndex = 0;

        const getPerView = () => {
            if (window.innerWidth <= 700) {
                return 1;
            }
            if (window.innerWidth <= 1024) {
                return 2;
            }
            return 3;
        };

        const render = () => {
            const perView = getPerView();
            const maxStart = Math.max(0, cards.length - perView);
            if (startIndex > maxStart) {
                startIndex = 0;
            }
            const basis = cards[0].getBoundingClientRect().width;
            const gap = Number.parseFloat(window.getComputedStyle(track).gap || '0');
            const offset = startIndex * (basis + gap);
            track.style.transform = `translateX(-${offset}px)`;
        };

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                const perView = getPerView();
                const maxStart = Math.max(0, cards.length - perView);
                startIndex = startIndex <= 0 ? maxStart : startIndex - 1;
                render();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const perView = getPerView();
                const maxStart = Math.max(0, cards.length - perView);
                startIndex = startIndex >= maxStart ? 0 : startIndex + 1;
                render();
            });
        }

        window.addEventListener('resize', render);
        render();
    });
});
