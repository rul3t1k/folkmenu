document.addEventListener('DOMContentLoaded', () => {


    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('translate-y-0');
            
            if (isOpen) {
                // Закрываем меню
                mobileMenu.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                mobileMenu.classList.add('-translate-y-full', 'opacity-0', 'pointer-events-none');
                menuIcon.textContent = 'menu';
            } else {
                // Открываем меню
                mobileMenu.classList.remove('-translate-y-full', 'opacity-0', 'pointer-events-none');
                mobileMenu.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                menuIcon.textContent = 'close';
            }
        });
    }
    
    // === 1. ИНТЕРАКТИВ ДЛЯ КНОПКИ "НРАВИТСЯ" ===
    document.querySelectorAll('button[aria-label="Нравится"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('.material-symbols-outlined');
            if (icon) {
                if (icon.textContent === 'favorite') {
                    icon.textContent = 'favorite_border';
                    this.classList.remove('text-terracotta');
                    this.classList.add('text-warm-muted');
                } else {
                    icon.textContent = 'favorite';
                    this.classList.add('text-terracotta');
                    this.classList.remove('text-warm-muted');
                }
            }
        });
    });

    // === 2. УПРАВЛЕНИЕ МОДАЛКОЙ И БЛОКИРОВКОЙ СКРОЛЛА ФОНА ===
    const modal = document.getElementById('add-recipe-modal');
    const openModalBtn = document.getElementById('open-add-recipe-modal-btn');
    const closeModalX = document.getElementById('close-modal-x-btn');
    const closeModalCancel = document.getElementById('close-modal-cancel-btn');
    const addRecipeForm = document.getElementById('add-recipe-form');
    if (openModalBtn && modal) {
        openModalBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    }
    const closeModal = () => {
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    };
    if (closeModalX) closeModalX.addEventListener('click', closeModal);
    if (closeModalCancel) closeModalCancel.addEventListener('click', closeModal);
    if (addRecipeForm) {
        addRecipeForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Рецепт успешно записан в кулинарную книгу!');
            closeModal();
            addRecipeForm.reset();
        });
    }

    // === 3. ДИНАМИЧЕСКОЕ ДОБАВЛЕНИЕ ИНГРЕДИЕНТОВ ===
    document.getElementById('add-ingredient-button')?.addEventListener('click', function() {
        const container = document.getElementById('ingredients-list-container');
        if (!container) return;
        const count = container.querySelectorAll('.ingredient-item').length + 1;
        const num = count < 10 ? '0' + count : count;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 bg-white border border-[#E5D2BE] p-2 shadow-xs ingredient-item rounded-xl';
        row.innerHTML = `
            <span class="font-mono text-xs text-warm-muted px-1 font-bold">${num}.</span>
            <input class="flex-1 bg-transparent border-0 p-0 text-sm text-warm-ink focus:outline-none" placeholder="Ингредиент" type="text" />
            <div class="w-36 border-l border-[#E5D2BE] pl-2">
                <input class="w-full bg-[#FFF9F4] border border-[#D4B996] px-2 py-1 text-xs font-mono font-bold text-right focus:outline-none rounded-md" placeholder="Количество" type="text" />
            </div>
            <button class="p-1 text-warm-muted hover:text-terracotta rounded-md" onclick="this.closest('.ingredient-item').remove()" type="button">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        `;
        container.appendChild(row);
    });

    // === 4. ДИНАМИЧЕСКОЕ ДОБАВЛЕНИЕ ШАГОВ ===
    function updateStepNumbers() {
        const container = document.getElementById('steps-list-container');
        if (!container) return;
        container.querySelectorAll('.step-item').forEach((step, idx) => {
            const label = step.querySelector('.step-number-label');
            if (label) label.textContent = `ШАГ ${idx + 1} •`;
        });
    }
    document.getElementById('steps-list-container')?.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-step-btn');
        if (deleteBtn) {
            deleteBtn.closest('.step-item')?.remove();
            updateStepNumbers();
        }
    });
    document.getElementById('add-step-button')?.addEventListener('click', function() {
        const container = document.getElementById('steps-list-container');
        if (!container) return;
        const count = container.querySelectorAll('.step-item').length + 1;
        const stepDiv = document.createElement('div');
        stepDiv.className = 'bg-[#FAF4EB] border border-[#D4B996] p-3.5 flex flex-col gap-2.5 step-item rounded-2xl';
        stepDiv.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <span class="font-mono text-xs font-bold text-terracotta uppercase step-number-label">ШАГ ${count} •</span>
                <input class="flex-1 bg-white border border-[#E5D2BE] px-2.5 py-1 text-xs font-mono font-bold text-warm-ink focus:outline-none rounded-lg" placeholder="Наименование шага" type="text">
                <button class="p-1 text-warm-muted hover:text-terracotta delete-step-btn rounded-md" type="button">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            <textarea class="w-full bg-white border border-[#E5D2BE] p-2.5 text-sm text-warm-ink focus:outline-none rounded-xl" placeholder="Опишите этап..." rows="2"></textarea>
        `;
        container.appendChild(stepDiv);
    });

    // === ПРЕВЬЮ ЗАГРУЖЕННОЙ ФОТОГРАФИИ ===
    const photoInput = document.getElementById('recipe-photo-input');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview-img');
    const removePhotoBtn = document.getElementById('remove-photo-btn');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    uploadPlaceholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                    previewContainer.classList.add('flex');
                };
                reader.readAsDataURL(file);
            }
        });
    }
    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (photoInput) photoInput.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            previewContainer.classList.remove('flex');
            uploadPlaceholder.classList.remove('hidden');
        });
    }

    // === ЛОГИКА СТРАНИЦЫ АВТОРИЗАЦИИ ===
    const tabLogin = document.getElementById('tab-auth-login');
    const tabRegister = document.getElementById('tab-auth-register');
    const loginSection = document.getElementById('auth-login-section');
    const registerSection = document.getElementById('auth-register-section');
    const gotoRegisterBtn = document.getElementById('goto-register-btn');
    const gotoLoginBtn = document.getElementById('goto-login-btn');
    function switchAuthTab(tab) {
        if (!loginSection || !registerSection || !tabLogin || !tabRegister) return;
        if (tab === 'login') {
            loginSection.classList.remove('hidden');
            registerSection.classList.add('hidden');
            tabLogin.className = 'flex-1 pb-3 text-center font-serif text-xl font-bold text-terracotta border-b-2 border-terracotta transition-all duration-200 cursor-pointer';
            tabRegister.className = 'flex-1 pb-3 text-center font-serif text-xl font-medium text-warm-muted hover:text-warm-ink border-b-2 border-transparent transition-all duration-200 cursor-pointer';
        } else {
            registerSection.classList.remove('hidden');
            loginSection.classList.add('hidden');
            tabRegister.className = 'flex-1 pb-3 text-center font-serif text-xl font-bold text-terracotta border-b-2 border-terracotta transition-all duration-200 cursor-pointer';
            tabLogin.className = 'flex-1 pb-3 text-center font-serif text-xl font-medium text-warm-muted hover:text-warm-ink border-b-2 border-transparent transition-all duration-200 cursor-pointer';
        }
    }
    if (tabLogin) tabLogin.addEventListener('click', () => switchAuthTab('login'));
    if (tabRegister) tabRegister.addEventListener('click', () => switchAuthTab('register'));
    if (gotoRegisterBtn) gotoRegisterBtn.addEventListener('click', () => switchAuthTab('register'));
    if (gotoLoginBtn) gotoLoginBtn.addEventListener('click', () => switchAuthTab('login'));

    document.querySelectorAll('.toggle-pass-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('.material-symbols-outlined');
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });

    function showAuthNotice(text) {
        const toast = document.getElementById('auth-toast');
        const toastMsg = document.getElementById('toast-message');
        if (!toast || !toastMsg) return;
        toastMsg.textContent = text;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 4000);
    }
    const forgotPassBtn = document.getElementById('forgot-pass-btn');
    if (forgotPassBtn) {
        forgotPassBtn.addEventListener('click', () => {
            showAuthNotice('Ссылка для восстановления отправлена на указанную почту');
        });
    }
    
    // === СЧЕТЧИК ПОРЦИЙ (СТРАНИЦА РЕЦЕПТА) ===
    const servingsInput = document.getElementById('servings-input');
    const incBtn = document.getElementById('servings-inc');
    const decBtn = document.getElementById('servings-dec');
    const qtyNodes = document.querySelectorAll('.ingredient-qty');
    const baseServings = 4;
    function updateIngredients(newServings) {
        if (isNaN(newServings) || newServings < 1) newServings = 1;
        if (newServings > 50) newServings = 50;
        servingsInput.value = newServings;
        const ratio = newServings / baseServings;
        qtyNodes.forEach(node => {
            const baseQty = parseFloat(node.dataset.baseQty);
            const unit = node.dataset.unit;
            if (!isNaN(baseQty)) {
                let updatedQty = baseQty * ratio;
                if (unit.includes('шт. (800 г)')) {
                    const apples = Math.round(updatedQty);
                    const grams = Math.round(800 * ratio);
                    node.textContent = `${apples} шт. (${grams} г)`;
                } else if (unit === 'г') {
                    node.textContent = `${Math.round(updatedQty)} г`;
                } else if (unit === 'шт.') {
                    node.textContent = `${Math.round(updatedQty)} шт.`;
                } else if (unit === 'ч. л.') {
                    node.textContent = `${(updatedQty).toFixed(1).replace('.0', '')} ч. л.`;
                } else if (unit === 'щепотка') {
                    node.textContent = newServings > 6 ? '2 щепотки' : 'щепотка';
                } else {
                    node.textContent = `${Math.round(updatedQty)} ${unit}`;
                }
            }
        });
    }
    if (incBtn && decBtn && servingsInput) {
        incBtn.addEventListener('click', () => {
            let current = parseInt(servingsInput.value) || 4;
            updateIngredients(current + 1);
        });
        decBtn.addEventListener('click', () => {
            let current = parseInt(servingsInput.value) || 4;
            updateIngredients(current - 1);
        });
        servingsInput.addEventListener('input', (e) => {
            let val = parseInt(e.target.value);
            if (val > 0) updateIngredients(val);
        });
    }

    const cartBtn = document.getElementById('add-to-cart-btn');
if (cartBtn) {
    cartBtn.addEventListener('click', () => {
        // Ищем все чекбоксы внутри #ingredients-container, которые НЕ отмечены
        const uncheckedItems = document.querySelectorAll('#ingredients-container input[type="checkbox"]:not(:checked)');
        const count = uncheckedItems.length;
        if (count === 0) {
            showAuthNotice('Все ингредиенты уже отмечены как имеющиеся!');
        } else {
            showAuthNotice(`В корзину добавлено недостающих позиций: ${count}`);
        }
    });
}

    const favBtn = document.getElementById('favorite-btn');
    const favIcon = document.getElementById('favorite-icon');
    const favText = document.getElementById('favorite-text');
    let isFav = true;
    if (favBtn && favIcon && favText) {
        favBtn.addEventListener('click', () => {
            isFav = !isFav;
            if (isFav) {
                favText.textContent = 'В избранном';
                favIcon.style.fontVariationSettings = "'FILL' 1";
                favBtn.classList.add('bg-terracotta', 'text-white');
                favBtn.classList.remove('bg-white', 'text-warm-ink', 'border');
                showAuthNotice('Рецепт добавлен в избранное');
            } else {
                favText.textContent = 'В избранное';
                favIcon.style.fontVariationSettings = "'FILL' 0";
                favBtn.classList.remove('bg-terracotta', 'text-white');
                favBtn.classList.add('bg-white', 'text-warm-ink', 'border');
                showAuthNotice('Рецепт удален из избранного');
            }
        });
    }

    const stepChecks = document.querySelectorAll('.step-check');
    stepChecks.forEach(cb => {
        cb.addEventListener('change', (e) => {
            const card = e.target.closest('.step-card');
            if (card) {
                if (e.target.checked) {
                    card.classList.add('opacity-60', 'bg-[#FAF4EB]/60');
                } else {
                    card.classList.remove('opacity-60', 'bg-[#FAF4EB]/60');
                }
            }
        });
    });

    const shareBtn = document.getElementById('share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href);
                showAuthNotice('Ссылка на рецепт скопирована в буфер обмена!');
            }
        });
    }


    // ==========================================
    // === ЛОГИКА СТРАНИЦЫ "СПИСОК ПОКУПОК" (SHOPPING LIST) ===
    // ==========================================
    const shoppingContainer = document.getElementById('shopping-list-container');
    if (shoppingContainer) {
        let pendingDeleteRow = null;

        function updateShoppingStats() {
            const rows = document.querySelectorAll('.item-row');
            let total = rows.length;
            let checked = 0;
            rows.forEach(r => {
                const cb = r.querySelector('.toggle-check');
                if (cb && cb.checked) {
                    checked++;
                }
            });
            const remaining = total - checked;
            const summaryText = document.getElementById('summary-text');
            const headerCounter = document.getElementById('header-counter');
            const navCartCount = document.getElementById('nav-cart-count');

            if (summaryText) {
                summaryText.textContent = `Всего: ${total} • Куплено: ${checked} • Осталось: ${remaining}`;
            }
            if (headerCounter) {
                headerCounter.textContent = `${total} позиций`;
            }
            if (navCartCount) {
                navCartCount.textContent = total;
            }
        }

        // Модалка удаления
        const modal = document.getElementById('delete-modal');
        const modalPanel = document.getElementById('delete-modal-panel');
        const modalItemName = document.getElementById('modal-item-name');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const cancelDeleteX = document.getElementById('cancel-delete-x');

        function openDeleteModal(row) {
            pendingDeleteRow = row;
            const textElement = row.querySelector('.item-title');
            const itemName = textElement ? textElement.textContent.trim() : 'ингредиент';
            if (modalItemName) {
                modalItemName.textContent = `«${itemName}»`;
            }
            if (modal) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100');
            }
            if (modalPanel) {
                modalPanel.classList.remove('scale-95');
                modalPanel.classList.add('scale-100');
            }
        }

        function closeDeleteModal() {
            pendingDeleteRow = null;
            if (modal) {
                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0', 'pointer-events-none');
            }
            if (modalPanel) {
                modalPanel.classList.remove('scale-100');
                modalPanel.classList.add('scale-95');
            }
        }

        if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        if (cancelDeleteX) cancelDeleteX.addEventListener('click', closeDeleteModal);
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeDeleteModal();
            });
        }

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => {
                if (pendingDeleteRow) {
                    pendingDeleteRow.style.transition = 'all 0.2s ease';
                    pendingDeleteRow.style.opacity = '0';
                    pendingDeleteRow.style.transform = 'translateX(10px)';
                    const targetRow = pendingDeleteRow;
                    setTimeout(() => {
                        targetRow.remove();
                        updateShoppingStats();
                        showAuthNotice('Позиция удалена из списка');
                    }, 200);
                }
                closeDeleteModal();
            });
        }

        // К кликам внутри списка покупок
        shoppingContainer.addEventListener('click', (e) => {
            const toggleCb = e.target.closest('.toggle-check');
            if (toggleCb) {
                const row = toggleCb.closest('.item-row');
                if (!row) return;
                const textP = row.querySelector('.item-title');
                
                if (toggleCb.checked) {
                    row.classList.add('bg-[#FFF9F4]');
                    row.classList.remove('bg-white');
                    if (textP) textP.classList.add('line-through', 'opacity-60');
                } else {
                    row.classList.remove('bg-[#FFF9F4]');
                    row.classList.add('bg-white');
                    if (textP) textP.classList.remove('line-through', 'opacity-60');
                }
                updateShoppingStats();
                return;
            }

            const deleteBtn = e.target.closest('.delete-item');
            if (deleteBtn) {
                const row = deleteBtn.closest('.item-row');
                if (row) openDeleteModal(row);
                return;
            }

            const plusBtn = e.target.closest('.qty-btn-plus');
            if (plusBtn) {
                const input = plusBtn.parentElement.querySelector('.qty-val');
                if (input) {
                    let val = parseInt(input.value) || 1;
                    input.value = val + 1;
                }
                return;
            }

            const minusBtn = e.target.closest('.qty-btn-minus');
            if (minusBtn) {
                const input = minusBtn.parentElement.querySelector('.qty-val');
                if (input) {
                    let val = parseInt(input.value) || 1;
                    if (val > 1) input.value = val - 1;
                }
                return;
            }
        });

        // Очистить купленное
        const clearBtn = document.getElementById('clear-bought');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                const rows = document.querySelectorAll('.item-row');
                let removedCount = 0;
                rows.forEach(r => {
                    const cb = r.querySelector('.toggle-check');
                    if (cb && cb.checked) {
                        r.remove();
                        removedCount++;
                    }
                });
                updateShoppingStats();
                if (removedCount > 0) {
                    showAuthNotice(`Убрано купленных позиций: ${removedCount}`);
                } else {
                    showAuthNotice('Нет купленных позиций для очистки');
                }
            });
        }

        // Добавление кастомного ингредиента вручную
        const addForm = document.getElementById('add-item-form');
        const input = document.getElementById('new-item-input');

        if (addForm && input) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const title = input.value.trim();
                if (!title) return;

                const newRow = document.createElement('div');
                newRow.className = 'item-row group flex items-center justify-between p-3 bg-white hover:bg-[#FFF9F4] transition-colors border border-[#D4B996]/50 rounded-xl';
                newRow.innerHTML = `
                    <div class="flex items-center gap-3 min-w-0">
                        <input class="custom-checkbox toggle-check" type="checkbox">
                        <div class="min-w-0">
                            <p class="item-title font-sans text-sm text-warm-ink font-medium truncate">${title}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="flex items-center gap-1 bg-[#FFF9F4] border border-[#D4B996] rounded-lg px-1 py-0.5 shadow-xs">
                            <button class="qty-btn-minus text-xs text-warm-ink hover:text-terracotta px-1.5 font-bold transition-colors select-none" type="button">−</button>
                            <input class="qty-val w-10 text-center font-mono text-xs font-bold text-terracotta bg-transparent border-0 focus:ring-0 p-0" min="1" max="99" type="number" value="1">
                            <button class="qty-btn-plus text-xs text-warm-ink hover:text-terracotta px-1.5 font-bold transition-colors select-none" type="button">+</button>
                        </div>
                        <button class="delete-item text-warm-muted hover:text-terracotta p-1 transition-colors cursor-pointer" title="Удалить" type="button">
                            <span class="material-symbols-outlined text-lg leading-none">close</span>
                        </button>
                    </div>
                `;
                shoppingContainer.prepend(newRow);
                input.value = '';
                updateShoppingStats();
                showAuthNotice('Ингредиент добавлен в список');
            });
        }

        updateShoppingStats();
    }

    // === 1. ПЕРЕКЛЮЧЕНИЕ ВКЛАДОК ПРОФИЛЯ ===
    window.switchProfileTab = function(tab) {
        const recipesSec = document.getElementById('section-recipes');
        const favoritesSec = document.getElementById('section-favorites');
        const shoppingSec = document.getElementById('section-shopping');
        
        const recipesBtn = document.getElementById('tab-recipes-btn');
        const favoritesBtn = document.getElementById('tab-favorites-btn');
        const shoppingBtn = document.getElementById('tab-shopping-btn');

        if (!recipesSec || !favoritesSec || !shoppingSec) return;

        // Скрываем все секции
        recipesSec.classList.add('hidden'); recipesSec.classList.remove('flex');
        favoritesSec.classList.add('hidden'); favoritesSec.classList.remove('flex');
        shoppingSec.classList.add('hidden'); shoppingSec.classList.remove('flex');

        const inactiveTabClass = "px-5 py-3 bg-white/80 hover:bg-white text-warm-muted hover:text-warm-ink border border-b-0 border-[#dec0b7] font-sans text-xs sm:text-sm font-semibold transition-colors rounded-t-2xl flex items-center gap-2 cursor-pointer";
        const activeTabClass = "px-5 py-3 bg-terracotta text-white font-sans text-xs sm:text-sm font-bold tracking-wide rounded-t-2xl shadow-xs flex items-center gap-2 cursor-pointer";

        if (recipesBtn) recipesBtn.className = inactiveTabClass;
        if (favoritesBtn) favoritesBtn.className = inactiveTabClass;
        if (shoppingBtn) shoppingBtn.className = inactiveTabClass;

        if (tab === 'recipes') {
            recipesSec.classList.remove('hidden'); recipesSec.classList.add('flex');
            if (recipesBtn) recipesBtn.className = activeTabClass;
        } else if (tab === 'favorites') {
            favoritesSec.classList.remove('hidden'); favoritesSec.classList.add('flex');
            if (favoritesBtn) favoritesBtn.className = activeTabClass;
        } else if (tab === 'shopping') {
            shoppingSec.classList.remove('hidden'); shoppingSec.classList.add('flex');
            if (shoppingBtn) shoppingBtn.className = activeTabClass;
        }
    };

    // === 2. ИНТЕРАКТИВ И СТАТУСЫ СПИСКА ПОКУПОК ===
    window.toggleShoppingItem = function(checkbox) {
        const parentRow = checkbox.closest('label')?.parentElement;
        if (!parentRow) return;
        const textSpan = parentRow.querySelector('span.font-sans');
        const statusSpan = parentRow.querySelector('.status-label');

        if (checkbox.checked) {
            if (textSpan) {
                textSpan.classList.add('line-through', 'opacity-60');
                textSpan.classList.remove('font-medium');
            }
            if (statusSpan) {
                statusSpan.textContent = 'Куплено';
                statusSpan.className = 'text-xs font-mono text-sage font-bold status-label';
            }
        } else {
            if (textSpan) {
                textSpan.classList.remove('line-through', 'opacity-60');
                textSpan.classList.add('font-medium');
            }
            if (statusSpan) {
                statusSpan.textContent = 'Нужно купить';
                statusSpan.className = 'text-xs font-mono text-terracotta font-bold status-label';
            }
        }
    };

    // === 3. УВЕДОМЛЕНИЯ (ТОСТЫ) ===
    window.showToast = function(message, title = 'Успешно!') {
        const toast = document.getElementById('site-toast');
        const titleEl = document.getElementById('toast-title');
        const msgEl = document.getElementById('toast-message');
        if (!toast || !titleEl || !msgEl) return;

        titleEl.textContent = title;
        msgEl.textContent = message;
        
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3500);
    };

    // === 4. МОДАЛКА РЕДАКТИРОВАНИЯ ПРОФИЛЯ ===
    window.openEditProfileModal = function() {
        // поля формы уже заполнены сервером (см. views/site/profile.php)
        const modal = document.getElementById('edit-profile-modal');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeEditProfileModal = function() {
        const modal = document.getElementById('edit-profile-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    };

    window.saveProfileChanges = function(e) {
        e.preventDefault();
        const name = document.getElementById('edit-name-input')?.value;
        const email = document.getElementById('edit-email-input')?.value;
        const phone = document.getElementById('edit-phone-input')?.value;
        const bio = document.getElementById('edit-bio-input')?.value;

        const nameHeading = document.getElementById('profile-name-heading');
        const emailText = document.getElementById('profile-email-text');
        const phoneText = document.getElementById('profile-phone-text');
        const bioText = document.getElementById('profile-bio-text');

        if (name && nameHeading) nameHeading.textContent = name;
        if (email && emailText) emailText.innerHTML = `<span class="material-symbols-outlined text-sm text-terracotta">mail</span> ${email}`;
        if (phone && phoneText) phoneText.innerHTML = `<span class="material-symbols-outlined text-sm text-sage">call</span> ${phone}`;
        if (bio && bioText) bioText.textContent = bio;

        closeEditProfileModal();
        showToast('Изменения профиля успешно сохранены!', 'Профиль обновлен');
    };

    // === 5. МОДАЛКА ДОБАВЛЕНИЯ РЕЦЕПТА (ФОРМУЛЯР) ===
    window.openAddRecipeModal = function() {
        const modal = document.getElementById('add-recipe-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeAddRecipeModal = function() {
        const modal = document.getElementById('add-recipe-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    };

    window.submitNewRecipe = function(e) {
        e.preventDefault();
        closeAddRecipeModal();
        showToast('Новый рецепт успешно добавлен в вашу кулинарную книгу!');
    };

    // Динамические ингредиенты в модалке
    window.addRecipeIngredient = function() {
        const container = document.getElementById('ingredients-list-container');
        if (!container) return;
        const count = container.querySelectorAll('.ingredient-item').length + 1;
        const num = count < 10 ? '0' + count : count;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 bg-white border border-[#E5D2BE] p-2 shadow-xs ingredient-item rounded-xl';
        row.innerHTML = `
            <span class="font-mono text-xs text-warm-muted px-1 font-bold">${num}.</span>
            <input class="flex-1 bg-transparent border-0 p-0 text-sm font-sans text-warm-ink focus:outline-none" placeholder="Ингредиент" type="text">
            <div class="w-36 border-l border-[#E5D2BE] pl-2">
                <input class="w-full bg-[#FFF9F4] border border-[#D4B996] px-2 py-1 text-xs font-mono font-bold text-right focus:outline-none rounded-md" placeholder="Количество" type="text">
            </div>
            <button class="p-1 text-warm-muted hover:text-terracotta rounded-md" onclick="this.closest('.ingredient-item').remove()" type="button">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        `;
        container.appendChild(row);
    };

    // Динамические шаги в модалке
    window.updateStepNumbers = function() {
        const container = document.getElementById('steps-list-container');
        if (!container) return;
        container.querySelectorAll('.step-item').forEach((step, idx) => {
            const label = step.querySelector('.step-number-label');
            if (label) label.textContent = `ШАГ ${idx + 1} •`;
        });
    };

    window.addRecipeStep = function() {
        const container = document.getElementById('steps-list-container');
        if (!container) return;
        const count = container.querySelectorAll('.step-item').length + 1;
        const stepDiv = document.createElement('div');
        stepDiv.className = 'bg-[#FAF4EB] border border-[#D4B996] p-3.5 flex flex-col gap-2.5 step-item rounded-2xl';
        stepDiv.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <span class="font-mono text-xs font-bold text-terracotta uppercase step-number-label">ШАГ ${count} •</span>
                <input class="flex-1 bg-white border border-[#E5D2BE] px-2.5 py-1 text-xs font-mono font-bold text-warm-ink focus:outline-none rounded-lg" placeholder="Наименование шага" type="text">
                <button class="p-1 text-warm-muted hover:text-terracotta delete-step-btn rounded-md" type="button" onclick="this.closest('.step-item').remove(); updateStepNumbers();">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            <textarea class="w-full bg-white border border-[#E5D2BE] p-2.5 text-sm text-warm-ink focus:outline-none rounded-xl" placeholder="Опишите этап..." rows="2"></textarea>
        `;
        container.appendChild(stepDiv);
    };

    // Загрузка фото рецепта в модалке
    const recipePhotoInput = document.getElementById('recipe-photo-input');
    if (recipePhotoInput) {
        recipePhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewImg = document.getElementById('image-preview-img');
                    const uploadPlaceholder = document.getElementById('upload-placeholder');
                    const previewContainer = document.getElementById('image-preview-container');

                    if (previewImg) previewImg.src = event.target.result;
                    if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                        previewContainer.classList.add('flex');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    window.removeRecipePhoto = function(e) {
        e.preventDefault();
        e.stopPropagation();
        const recipePhotoInput = document.getElementById('recipe-photo-input');
        const previewImg = document.getElementById('image-preview-img');
        const previewContainer = document.getElementById('image-preview-container');
        const uploadPlaceholder = document.getElementById('upload-placeholder');

        if (recipePhotoInput) recipePhotoInput.value = '';
        if (previewImg) previewImg.src = '';
        if (previewContainer) {
            previewContainer.classList.add('hidden');
            previewContainer.classList.remove('flex');
        }
        if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
    };


});

// Recipes database with required ingredients, servings, time and calories
    const recipesData = [
        {
            id: 1,
            title: "Деревенский картофельный гратен с чесночным маслом",
            desc: "Ароматное запеченное блюдо с хрустящей корочкой из картофеля, лука, чеснока и масла.",
            time: 40,
            servings: 4,
            calories: 320,
            image: "images/поиск2.jpg",
            required: ["Картофель", "Лук репчатый", "Чеснок", "Сливочное масло", "Томаты спелые"]
        },
        {
            id: 2,
            title: "Итальянская паста Карбонара по-домашнему",
            desc: "Классика трапезы: яйца, бекон, паста и сливочное масло. Добавьте пармезан для аутентичности.",
            time: 20,
            servings: 2,
            calories: 510,
            image: "images/поиск4.jpg",
            required: ["Спагетти", "Яйца куриные", "Бекон", "Сыр пармезан", "Сливочное масло"]
        },
        {
            id: 3,
            title: "Нежный омлет с томатами и зеленью",
            desc: "Быстрый и пышный завтрак всего за 15 минут. Томаты, свежие яйца, масло и лук.",
            time: 15,
            servings: 2,
            calories: 240,
            image: "images/поиск1.jpg",
            required: ["Яйца куриные", "Томаты спелые", "Лук репчатый", "Сливочное масло"]
        },
        {
            id: 4,
            title: "Быстрый яблочный пирог на сливочном масле",
            desc: "Тающее во рту песочное тесто из муки, отборных яиц и сливочного масла.",
            time: 50,
            servings: 6,
            calories: 380,
            image: "images/поиск3.jpg",
            required: ["Мука пшеничная", "Яйца куриные", "Сливочное масло"]
        }
    ];

    let currentSortMode = 'match'; // 'match' or 'time'
    let pendingDeleteRow = null;

    document.addEventListener('DOMContentLoaded', () => {
        updateSearch();
    });

    function showNotice(text) {
        const toast = document.getElementById('auth-toast');
        const toastMsg = document.getElementById('toast-message');
        if (!toast || !toastMsg) return;

        toastMsg.textContent = text;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3500);
    }

    // Modal Controls for Deleting Inventory Item
    function promptDelete(btn) {
        const row = btn.closest('.ingredient-item');
        if (!row) return;
        pendingDeleteRow = row;
        const name = row.getAttribute('data-name') || 'продукт';
        const modalItemName = document.getElementById('modal-item-name');
        if (modalItemName) modalItemName.textContent = `«${name}»`;

        const modal = document.getElementById('delete-modal');
        const panel = document.getElementById('delete-modal-panel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        }
    }

    function closeDeleteModal() {
        pendingDeleteRow = null;
        const modal = document.getElementById('delete-modal');
        const panel = document.getElementById('delete-modal-panel');
        if (modal && panel) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');
        }
    }

    function confirmDelete() {
        if (pendingDeleteRow) {
            pendingDeleteRow.style.transition = 'all 0.2s ease';
            pendingDeleteRow.style.opacity = '0';
            pendingDeleteRow.style.transform = 'translateX(10px)';
            const target = pendingDeleteRow;
            setTimeout(() => {
                target.remove();
                updateSearch();
                showNotice('Продукт удален из кладовой');
            }, 200);
        }
        closeDeleteModal();
    }

    function updateSearch() {
        // Collect checked pantry items
        const checkedItems = [];
        const itemRows = document.querySelectorAll('.ingredient-item');
        
        itemRows.forEach(item => {
            const cb = item.querySelector('input[type="checkbox"]');
            const name = item.getAttribute('data-name');
            const qtyInput = item.querySelector('.qty-input');
            const unitSelect = item.querySelector('.unit-select');

            if (cb && cb.checked && name) {
                checkedItems.push(name.toLowerCase());
                item.classList.add('bg-[#FFF9F4]');
                item.classList.remove('bg-white');
            } else {
                item.classList.remove('bg-[#FFF9F4]');
                item.classList.add('bg-white');
            }
        });

        // Update counter badge
        const badge = document.getElementById('selectedCounterBadge');
        if (badge) {
            badge.textContent = `${checkedItems.length} позиций`;
        }

        // Calculate match score for each recipe
        const evaluatedRecipes = recipesData.map(recipe => {
            const required = recipe.required;
            let matchedCount = 0;
            const missing = [];

            required.forEach(req => {
                const found = checkedItems.some(ci => ci.includes(req.toLowerCase()) || req.toLowerCase().includes(ci));
                if (found) {
                    matchedCount++;
                } else {
                    missing.push(req);
                }
            });

            const matchPercent = Math.round((matchedCount / required.length) * 100);
            return {
                ...recipe,
                matchedCount,
                totalRequired: required.length,
                matchPercent,
                missing
            };
        });

        // Sort recipes based on current mode
        if (currentSortMode === 'match') {
            evaluatedRecipes.sort((a, b) => b.matchPercent - a.matchPercent);
        } else {
            evaluatedRecipes.sort((a, b) => a.time - b.time);
        }

        // Render recipe cards
        renderRecipes(evaluatedRecipes);
    }

    function renderRecipes(recipes) {
        const container = document.getElementById('recipeCardsContainer');
        const titleEl = document.getElementById('resultsTitle');
        if (!container) return;

        if (titleEl) {
            titleEl.textContent = `Найдено ${recipes.length} рецепта под ваши продукты`;
        }

        container.innerHTML = '';

        recipes.forEach(r => {
            const isFullMatch = r.missing.length === 0;
            const badgeBg = isFullMatch ? 'bg-[#EDF4EF] text-sage border-sage/40' : 'bg-[#FDF0EB] text-terracotta border-terracotta/40';
            const washiClass = isFullMatch ? 'washi-sage' : 'washi-terracotta';
            const badgeText = isFullMatch ? `✓ Все ${r.totalRequired} ингредиента в наличии (100%)` : `${r.matchedCount} из ${r.totalRequired} (Не хватает: ${r.missing.join(', ')})`;

            const card = document.createElement('div');
            card.className = 'relative bg-white border border-[#D4B996] shadow-xs flex flex-col justify-between rounded-3xl overflow-visible group';
            card.innerHTML = `
                <div class="washi-tape ${washiClass} -top-3 left-8 w-32 h-6 flex items-center justify-center">
                    <span class="font-mono text-[10px] ${isFullMatch ? 'text-white' : 'text-white'} font-bold tracking-widest uppercase">${isFullMatch ? 'Идеально' : 'Докупить'}</span>
                </div>
                <!-- Добавили rounded-t-3xl вот сюда 👇 -->
                <div class="relative h-56 w-full overflow-hidden bg-[#FFF9F4] rounded-t-3xl">
                    <img alt="${r.title}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="${r.image}">
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between gap-4">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 ${badgeBg} font-mono text-xs font-bold rounded-md mb-2 border">${badgeText}</span>
                        <h3 class="text-xl font-serif font-bold text-warm-ink">${r.title}</h3>
                        <p class="font-sans text-xs text-warm-muted mt-1 leading-relaxed">${r.desc}</p>
                        
                        <div class="flex items-center gap-4 text-warm-muted font-mono text-xs mt-3">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-terracotta text-sm">schedule</span>${r.time} мин</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sage text-sm">room_service</span>${r.servings} порции</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-honey text-sm">local_fire_department</span>${r.calories} ккал</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-dashed border-[#D4B996] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
                        ${!isFullMatch ? `
                        <button class="px-3 py-2 bg-white hover:bg-paper text-warm-ink border border-[#D4B996] font-sans text-xs font-medium transition-colors flex items-center justify-center gap-1.5 rounded-xl cursor-pointer" onclick="showCartNotification('${r.missing.join(', ')}')" type="button">
                            <span class="material-symbols-outlined text-sm text-terracotta">shopping_cart</span>
                            <span>Добавить недостающие</span>
                        </button>` : '<div></div>'}
                        <a href="recipe.html" class="px-5 py-2.5 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-all rounded-xl shadow-xs text-center flex items-center justify-center gap-1.5">
                            <span>Смотреть рецепт</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function setSortMode(mode) {
        currentSortMode = mode;
        const btnMatch = document.getElementById('sortByMatch');
        const btnTime = document.getElementById('sortByTime');

        if (mode === 'match') {
            btnMatch.className = 'px-3.5 py-2 bg-terracotta text-white font-sans text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer rounded-xl';
            btnTime.className = 'px-3.5 py-2 bg-white hover:bg-paper text-warm-muted hover:text-warm-ink border border-[#D4B996] font-sans text-xs font-semibold transition-colors flex items-center gap-1.5 cursor-pointer rounded-xl';
        } else {
            btnTime.className = 'px-3.5 py-2 bg-terracotta text-white font-sans text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer rounded-xl';
            btnMatch.className = 'px-3.5 py-2 bg-white hover:bg-paper text-warm-muted hover:text-warm-ink border border-[#D4B996] font-sans text-xs font-semibold transition-colors flex items-center gap-1.5 cursor-pointer rounded-xl';
        }
        updateSearch();
    }

    function clearAllSelections() {
        document.querySelectorAll('.ingredient-item input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });
        updateSearch();
        showNotice('Выбор ингредиентов очищен');
    }

    function changeQty(btn, delta, customStep) {
        const container = btn.closest('div');
        if (!container) return;
        const input = container.querySelector('.qty-input');
        const unitSelect = container.querySelector('.unit-select');
        if (!input) return;
        
        // Для штук ('шт') шаг всегда строго целое число (1), для остальных — по настройке
        const currentUnit = unitSelect ? unitSelect.value : 'шт';
        let step = (currentUnit === 'шт') ? 1 : (customStep || parseFloat(input.getAttribute('step')) || 1);
        
        let val = parseFloat(input.value) || 0;
        val = Math.max(0, val + delta * step);
        if (step < 1) val = Math.round(val * 10) / 10;
        input.value = val;

        const item = btn.closest('.ingredient-item');
        if (item) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                if (val > 0 && !checkbox.checked) {
                    checkbox.checked = true;
                } else if (val === 0 && checkbox.checked) {
                    checkbox.checked = false;
                }
            }
        }
        updateSearch();
    }

    function addCustomIngredient(e) {
        e.preventDefault();
        const input = document.getElementById('customIngredientInput');
        const val = input.value.trim();
        if (!val) return;

        const grid = document.getElementById('ingredientsGrid');
        if (grid) {
            const newItem = document.createElement('div');
            newItem.className = 'ingredient-item flex items-center justify-between p-3 bg-[#FFF9F4] hover:bg-[#FAF4EB] border border-[#D4B996]/50 transition-colors rounded-xl';
            newItem.setAttribute('data-name', val);
            newItem.setAttribute('data-checked', 'true');
            newItem.innerHTML = `
                <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                    <input checked class="custom-checkbox" type="checkbox" onchange="updateSearch()">
                    <span class="font-sans text-sm font-medium text-warm-ink truncate">${val}</span>
                </label>
                <div class="flex items-center gap-1.5 shrink-0 bg-white border border-[#D4B996] rounded-lg px-1.5 py-1 shadow-xs">
                    <button type="button" onclick="changeQty(this, -1)" class="w-5 h-5 flex items-center justify-center text-warm-muted hover:text-terracotta font-mono text-sm leading-none transition-colors select-none">−</button>
                    <input type="number" min="0" max="99" value="1" oninput="updateSearch()" class="qty-input w-12 text-center text-xs font-mono font-bold text-terracotta border-0 bg-transparent p-0 focus:ring-0">
                    <select class="unit-select text-xs font-mono text-warm-muted bg-transparent border-0 p-0 focus:ring-0 cursor-pointer">
                        <option value="шт" selected>шт</option>
                        <option value="кг">кг</option>
                        <option value="г">г</option>
                        <option value="мл">мл</option>
                        <option value="л">л</option>
                    </select>
                    <button type="button" onclick="changeQty(this, 1)" class="w-5 h-5 flex items-center justify-center text-warm-muted hover:text-terracotta font-mono text-sm leading-none transition-colors select-none">+</button>
                </div>
                <button type="button" onclick="promptDelete(this)" class="ml-2 text-warm-muted hover:text-terracotta p-1 transition-colors cursor-pointer" title="Удалить продукт">
                    <span class="material-symbols-outlined text-base leading-none">close</span>
                </button>
            `;
            grid.prepend(newItem);
            input.value = '';
            updateSearch();
            showNotice(`Продукт «${val}» добавлен в кладовую`);
        }
    }

    function showCartNotification(missingItems) {
        showNotice(`Недостающие (${missingItems}) добавлены в список покупок!`);
    }

    function scrollToResults() {
        const target = document.getElementById('resultsSection');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function scrollToTop() {
        const topEl = document.getElementById('inventory-section');
        if (topEl) {
            topEl.scrollIntoView({ behavior: 'smooth' });
        }
    }

/* ==========================================================================
   Каталог рецептов (recipes.html): поиск, фильтры, сортировка, сброс
   Работает с готовыми карточками .recipe-card и их data-атрибутами:
   data-title, data-category (slug), data-difficulty, data-time, data-created, data-favs
   ========================================================================== */
(function () {
    const grid = document.getElementById('recipes-grid');
    if (!grid) return; // не страница каталога

    const els = {
        search: document.getElementById('filter-search'),
        category: document.getElementById('filter-category'),
        difficulty: document.getElementById('filter-difficulty'),
        time: document.getElementById('filter-time'),
        sort: document.getElementById('filter-sort'),
        count: document.getElementById('recipes-count'),
        empty: document.getElementById('recipes-empty'),
        resetBtns: [document.getElementById('filters-reset'), document.getElementById('empty-reset')]
    };
    const cards = Array.from(grid.querySelectorAll('.recipe-card'));

    // «ё» → «е» и нижний регистр, чтобы поиск не зависел от написания
    const norm = (s) => (s || '').toLowerCase().replace(/ё/g, 'е').trim();

    // Склонение: 1 рецепт, 2 рецепта, 5 рецептов
    function pluralRecipes(n) {
        const m10 = n % 10, m100 = n % 100;
        if (m10 === 1 && m100 !== 11) return 'рецепт';
        if (m10 >= 2 && m10 <= 4 && (m100 < 12 || m100 > 14)) return 'рецепта';
        return 'рецептов';
    }

    const sorters = {
        new: (a, b) => (parseInt(b.dataset.created, 10) || 0) - (parseInt(a.dataset.created, 10) || 0),
        popular: (a, b) => ((parseInt(b.dataset.favs, 10) || 0) - (parseInt(a.dataset.favs, 10) || 0)) ||
            ((parseInt(b.dataset.created, 10) || 0) - (parseInt(a.dataset.created, 10) || 0)),
        time: (a, b) => (parseInt(a.dataset.time, 10) || 1e9) - (parseInt(b.dataset.time, 10) || 1e9),
        title: (a, b) => a.dataset.title.localeCompare(b.dataset.title, 'ru')
    };

    function apply() {
        const q = norm(els.search.value);
        const cat = els.category.value;
        const diff = els.difficulty.value;
        const maxTime = els.time.value === 'all' ? Infinity : parseInt(els.time.value, 10);

        let visible = 0;
        cards.forEach((card) => {
            const ok =
                (!q || norm(card.dataset.title).includes(q)) &&
                (cat === 'all' || card.dataset.category === cat) &&
                (diff === 'all' || card.dataset.difficulty === diff) &&
                (maxTime === Infinity || parseInt(card.dataset.time, 10) <= maxTime);
            card.classList.toggle('hidden', !ok);
            if (ok) visible++;
        });

        // Сортировка: переставляем карточки в DOM (сортировка стабильная)
        const sorter = sorters[els.sort.value] || sorters.new;
        cards.slice().sort(sorter).forEach((card) => grid.appendChild(card));

        els.count.textContent = 'Найдено рецептов: ' + visible;
        els.empty.classList.toggle('hidden', visible !== 0);
        grid.classList.toggle('hidden', visible === 0);
    }

    function resetFilters() {
        els.search.value = '';
        els.category.value = 'all';
        els.difficulty.value = 'all';
        els.time.value = 'all';
        els.sort.value = 'new';
        // убираем ?category=... из адреса, чтобы после перезагрузки фильтр не вернулся
        if (window.history && history.replaceState && location.search) {
            history.replaceState(null, '', location.pathname);
        }
        apply();
    }

    els.search.addEventListener('input', apply);
    [els.category, els.difficulty, els.time, els.sort].forEach((el) => el.addEventListener('change', apply));
    els.resetBtns.forEach((btn) => btn && btn.addEventListener('click', resetFilters));

    // Переход с главной: recipes.html?category=soups
    const presetCat = new URLSearchParams(location.search).get('category');
    if (presetCat && Array.from(els.category.options).some((o) => o.value === presetCat)) {
        els.category.value = presetCat;
    }

    apply();
})();