/**
 * ANEFARM Admin Panel JavaScript
 * Advanced interactions for admin dashboard
 * Version: 2.0
 */

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initNotifications();
    initFormValidation();
    initImagePreview();
    initDataTables();
    initCharts();
    initAutoSave();
    initBulkActions();
    initKeyboardShortcuts();
    initActivityLogger();
    initThemeDetection();
});

/**
 * ============================================
 * SIDEBAR & NAVIGATION
 * ============================================
 */
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const menuToggle = document.querySelector('.menu-toggle');
    const overlay = document.querySelector('.sidebar-overlay');
    
    // Create overlay if not exists
    if(!overlay && window.innerWidth <= 768) {
        const newOverlay = document.createElement('div');
        newOverlay.className = 'sidebar-overlay';
        document.body.appendChild(newOverlay);
    }
    
    // Toggle sidebar on mobile
    if(menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('active');
            document.querySelector('.sidebar-overlay')?.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking overlay
    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('sidebar-overlay')) {
            sidebar.classList.remove('active');
            e.target.classList.remove('active');
        }
    });
    
    // Active link highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if(href && currentPath.includes(href)) {
            link.classList.add('active');
            
            // Scroll to active link
            setTimeout(() => {
                link.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    });
    
    // Collapsible submenus
    const menuItems = document.querySelectorAll('.sidebar-nav .has-submenu');
    menuItems.forEach(item => {
        const link = item.querySelector('a');
        const submenu = item.querySelector('.submenu');
        
        if(link && submenu) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                item.classList.toggle('expanded');
                
                // Animate height
                if(item.classList.contains('expanded')) {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                } else {
                    submenu.style.maxHeight = '0';
                }
            });
        }
    });
}

/**
 * ============================================
 * NOTIFICATIONS & ALERTS
 * ============================================
 */
function initNotifications() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.persistent)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            fadeOut(alert, 300, function() {
                alert.remove();
            });
        }, 5000);
        
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'alert-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', function() {
            alert.remove();
        });
        
        if(!alert.querySelector('.alert-close')) {
            alert.appendChild(closeBtn);
        }
    });
    
    // Toast notification system
    window.showToast = function(message, type = 'success', duration = 4000) {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Icons based on type
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${icons[type] || 'fa-bell'}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close">&times;</button>
            <div class="toast-progress"></div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto remove after duration
        const timeout = setTimeout(() => {
            removeToast(toast);
        }, duration);
        
        // Close button
        toast.querySelector('.toast-close').addEventListener('click', function() {
            clearTimeout(timeout);
            removeToast(toast);
        });
        
        // Pause on hover
        toast.addEventListener('mouseenter', () => clearTimeout(timeout));
        toast.addEventListener('mouseleave', () => {
            setTimeout(() => {
                removeToast(toast);
            }, duration);
        });
    };
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

function removeToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => {
        if(toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

function fadeOut(element, duration, callback) {
    element.style.transition = `opacity ${duration}ms ease`;
    element.style.opacity = '0';
    
    setTimeout(() => {
        if(callback) callback();
    }, duration);
}

/**
 * ============================================
 * FORM VALIDATION
 * ============================================
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        // Remove existing listeners
        form.removeEventListener('submit', validateForm);
        form.addEventListener('submit', validateForm);
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.removeEventListener('blur', validateField);
            input.removeEventListener('input', validateField);
            input.removeEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if(input.classList.contains('error')) {
                    validateField(input);
                }
            });
        });
    });
}

function validateForm(e) {
    const form = e.target;
    let isValid = true;
    let firstError = null;
    
    // Validate all fields
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required], input[data-validate]');
    
    inputs.forEach(input => {
        if(!validateField(input)) {
            isValid = false;
            if(!firstError) firstError = input;
        }
    });
    
    // Check password match if applicable
    const password = form.querySelector('input[type="password"][name="password"]');
    const confirmPassword = form.querySelector('input[type="password"][name="confirm_password"]');
    
    if(password && confirmPassword && password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Passwords do not match');
        isValid = false;
        if(!firstError) firstError = confirmPassword;
    }
    
    if(!isValid) {
        e.preventDefault();
        
        // Scroll to first error
        if(firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
        
        // Show summary error
        showFormError(form, 'Please correct the errors above');
    }
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Skip if disabled or readonly
    if(field.disabled || field.readOnly) return true;
    
    // Required validation
    if(field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    else if(field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Phone validation
    else if(field.type === 'tel' && value) {
        const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
        if(!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // Number validation
    else if(field.type === 'number' && value) {
        const min = parseFloat(field.min);
        const max = parseFloat(field.max);
        const numValue = parseFloat(value);
        
        if(!isNaN(min) && numValue < min) {
            isValid = false;
            errorMessage = `Value must be at least ${min}`;
        }
        else if(!isNaN(max) && numValue > max) {
            isValid = false;
            errorMessage = `Value must be at most ${max}`;
        }
    }
    
    // URL validation
    else if(field.type === 'url' && value) {
        try {
            new URL(value);
        } catch {
            isValid = false;
            errorMessage = 'Please enter a valid URL';
        }
    }
    
    // Custom pattern validation
    else if(field.pattern && value) {
        const pattern = new RegExp(field.pattern);
        if(!pattern.test(value)) {
            isValid = false;
            errorMessage = field.title || 'Please match the requested format';
        }
    }
    
    // Update UI
    updateFieldValidation(field, isValid, errorMessage);
    
    return isValid;
}

function updateFieldValidation(field, isValid, errorMessage) {
    // Remove existing error message
    const existingError = field.parentElement.querySelector('.field-error');
    if(existingError) {
        existingError.remove();
    }
    
    // Update classes
    field.classList.toggle('error', !isValid);
    field.classList.toggle('success', isValid && field.value.trim() !== '');
    
    // Add error message if invalid
    if(!isValid && errorMessage) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
        field.parentElement.appendChild(errorDiv);
    }
}

function showFieldError(field, message) {
    updateFieldValidation(field, false, message);
}

function showFormError(form, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error form-summary';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        ${message}
        <button class="alert-close">&times;</button>
    `;
    
    form.prepend(errorDiv);
    
    errorDiv.querySelector('.alert-close').addEventListener('click', function() {
        errorDiv.remove();
    });
}

/**
 * ============================================
 * IMAGE PREVIEW
 * ============================================
 */
function initImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][data-preview="true"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(!file) return;
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if(!allowedTypes.includes(file.type)) {
                showToast('Please select a valid image file (JPEG, PNG, GIF, WEBP)', 'error');
                input.value = '';
                return;
            }
            
            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if(file.size > maxSize) {
                showToast('Image size should be less than 5MB', 'error');
                input.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = input.parentElement.querySelector('.image-preview') || createImagePreview(input);
                const img = previewContainer.querySelector('img');
                img.src = e.target.result;
                previewContainer.classList.add('show');
                
                // Update image info
                const info = previewContainer.querySelector('.image-info');
                if(info) {
                    info.innerHTML = `
                        <span>${file.name}</span>
                        <span>${(file.size / 1024).toFixed(1)} KB</span>
                    `;
                }
            };
            reader.readAsDataURL(file);
        });
    });
}

function createImagePreview(input) {
    const container = document.createElement('div');
    container.className = 'image-preview';
    container.innerHTML = `
        <div class="preview-header">
            <h4>Image Preview</h4>
            <button type="button" class="remove-preview">&times;</button>
        </div>
        <div class="preview-content">
            <img src="#" alt="Preview">
            <div class="image-info"></div>
        </div>
    `;
    
    // Add remove functionality
    container.querySelector('.remove-preview').addEventListener('click', function() {
        container.remove();
        input.value = '';
    });
    
    input.parentElement.appendChild(container);
    return container;
}

/**
 * ============================================
 * DATA TABLES
 * ============================================
 */
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        // Search functionality
        const searchInput = table.parentElement.querySelector('.table-search');
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                filterTable(table, e.target.value);
            });
        }
        
        // Sort functionality
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.sort;
                const order = this.dataset.order === 'asc' ? 'desc' : 'asc';
                
                // Update header styles
                headers.forEach(h => {
                    h.classList.remove('sort-asc', 'sort-desc');
                });
                this.classList.add(`sort-${order}`);
                this.dataset.order = order;
                
                sortTable(table, column, order);
            });
        });
        
        // Pagination
        const pagination = table.parentElement.querySelector('.table-pagination');
        if(pagination) {
            initPagination(table, pagination);
        }
        
        // Row click
        const rows = table.querySelectorAll('tbody tr[data-href]');
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if(!e.target.closest('a, button')) {
                    window.location.href = this.dataset.href;
                }
            });
            row.style.cursor = 'pointer';
        });
        
        // Checkbox selection
        const checkboxAll = table.querySelector('th input[type="checkbox"]');
        const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
        
        if(checkboxAll) {
            checkboxAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('tr').classList.toggle('selected', this.checked);
                });
                
                // Update bulk actions
                updateBulkActions(table);
            });
        }
        
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                row.classList.toggle('selected', this.checked);
                
                // Update "select all" checkbox
                if(checkboxAll) {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    const someChecked = Array.from(checkboxes).some(c => c.checked);
                    checkboxAll.checked = allChecked;
                    checkboxAll.indeterminate = someChecked && !allChecked;
                }
                
                // Update bulk actions
                updateBulkActions(table);
            });
        });
    });
}

function filterTable(table, searchTerm) {
    const rows = table.querySelectorAll('tbody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        let found = false;
        const cells = row.querySelectorAll('td');
        
        cells.forEach(cell => {
            if(cell.textContent.toLowerCase().includes(term)) {
                found = true;
            }
        });
        
        row.style.display = found ? '' : 'none';
    });
    
    // Update pagination
    updatePagination(table);
}

function sortTable(table, column, order) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(table.querySelectorAll('th')).findIndex(th => th.dataset.sort === column);
    
    rows.sort((a, b) => {
        const aVal = a.querySelectorAll('td')[columnIndex]?.textContent || '';
        const bVal = b.querySelectorAll('td')[columnIndex]?.textContent || '';
        
        // Try numeric comparison
        const aNum = parseFloat(aVal);
        const bNum = parseFloat(bVal);
        
        if(!isNaN(aNum) && !isNaN(bNum)) {
            return order === 'asc' ? aNum - bNum : bNum - aNum;
        }
        
        // String comparison
        return order === 'asc' 
            ? aVal.localeCompare(bVal)
            : bVal.localeCompare(aVal);
    });
    
    // Reorder rows
    rows.forEach(row => tbody.appendChild(row));
}

function initPagination(table, container) {
    const rowsPerPage = 10;
    const rows = table.querySelectorAll('tbody tr');
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    let currentPage = 1;
    
    function showPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        rows.forEach((row, index) => {
            if(row.style.display !== 'none') {
                row.style.display = index >= start && index < end ? '' : 'none';
            }
        });
        
        // Update buttons
        updatePaginationButtons(container, page, pageCount);
    }
    
    function updatePaginationButtons(container, page, total) {
        let html = '';
        
        // Previous button
        html += `<button class="page-prev" ${page === 1 ? 'disabled' : ''}>&laquo;</button>`;
        
        // Page numbers
        for(let i = 1; i <= total; i++) {
            if(i === page || (i >= page - 2 && i <= page + 2)) {
                html += `<button class="page-num ${i === page ? 'active' : ''}">${i}</button>`;
            } else if(i === page - 3 || i === page + 3) {
                html += `<span class="page-dots">...</span>`;
            }
        }
        
        // Next button
        html += `<button class="page-next" ${page === total ? 'disabled' : ''}>&raquo;</button>`;
        
        container.innerHTML = html;
        
        // Add event listeners
        container.querySelectorAll('button:not([disabled])').forEach(btn => {
            btn.addEventListener('click', function() {
                if(this.classList.contains('page-prev')) {
                    showPage(page - 1);
                } else if(this.classList.contains('page-next')) {
                    showPage(page + 1);
                } else {
                    showPage(parseInt(this.textContent));
                }
            });
        });
    }
    
    showPage(1);
}

function updatePagination(table) {
    const container = table.parentElement.querySelector('.table-pagination');
    if(container) {
        const visibleRows = table.querySelectorAll('tbody tr:not([style*="display: none"])').length;
        const totalRows = table.querySelectorAll('tbody tr').length;
        
        if(visibleRows < totalRows) {
            container.querySelectorAll('button').forEach(btn => {
                btn.disabled = true;
            });
        }
    }
}

function updateBulkActions(table) {
    const selected = table.querySelectorAll('tbody input[type="checkbox"]:checked').length;
    const bulkActions = table.parentElement.querySelector('.bulk-actions');
    
    if(bulkActions) {
        if(selected > 0) {
            bulkActions.classList.add('show');
            bulkActions.querySelector('.selected-count').textContent = selected;
        } else {
            bulkActions.classList.remove('show');
        }
    }
}

/**
 * ============================================
 * CHARTS & STATISTICS
 * ============================================
 */
function initCharts() {
    // Sales Chart
    const salesCanvas = document.getElementById('salesChart');
    if(salesCanvas) {
        const ctx = salesCanvas.getContext('2d');
        const data = JSON.parse(salesCanvas.dataset.chart || '{}');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: data.values || [0, 0, 0, 0, 0, 0],
                    borderColor: '#00a400',
                    backgroundColor: 'rgba(0, 164, 0, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Donut Chart for categories
    const donutCanvas = document.getElementById('categoryChart');
    if(donutCanvas) {
        const ctx = donutCanvas.getContext('2d');
        const data = JSON.parse(donutCanvas.dataset.chart || '{}');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels || ['Milk', 'Cheese', 'Butter', 'Yogurt'],
                datasets: [{
                    data: data.values || [40, 20, 25, 15],
                    backgroundColor: [
                        '#00a400',
                        '#2196F3',
                        '#FF9800',
                        '#9C27B0'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    }
}

/**
 * ============================================
 * AUTO-SAVE
 * ============================================
 */
function initAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave="true"]');
    
    forms.forEach(form => {
        let timeout;
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    autoSaveForm(form);
                }, 2000);
            });
        });
    });
}

function autoSaveForm(form) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Save to localStorage
    const formId = form.id || 'unsaved_form';
    localStorage.setItem(formId, JSON.stringify(data));
    
    // Show auto-save indicator
    const indicator = form.querySelector('.auto-save-indicator') || createAutoSaveIndicator(form);
    indicator.classList.add('show');
    
    setTimeout(() => {
        indicator.classList.remove('show');
    }, 2000);
}

function createAutoSaveIndicator(form) {
    const indicator = document.createElement('div');
    indicator.className = 'auto-save-indicator';
    indicator.innerHTML = '<i class="fas fa-save"></i> Auto-saved';
    form.appendChild(indicator);
    return indicator;
}

/**
 * ============================================
 * BULK ACTIONS
 * ============================================
 */
function initBulkActions() {
    const bulkActionButtons = document.querySelectorAll('.bulk-action-btn');
    
    bulkActionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const action = this.dataset.action;
            const selectedIds = getSelectedIds();
            
            if(selectedIds.length === 0) {
                showToast('Please select items to perform this action', 'warning');
                return;
            }
            
            // Confirm action
            if(this.dataset.confirm) {
                if(!confirm(this.dataset.confirm)) {
                    return;
                }
            }
            
            // Execute action
            executeBulkAction(action, selectedIds);
        });
    });
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function executeBulkAction(action, ids) {
    // Show loading
    showToast(`Executing ${action} on ${ids.length} items...`, 'info');
    
    // Send AJAX request
    fetch('ajax/bulk-action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            ids: ids
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Action failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    });
}

/**
 * ============================================
 * KEYBOARD SHORTCUTS
 * ============================================
 */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save form
        if((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            
            const form = document.querySelector('form:not([data-no-shortcut])');
            if(form) {
                const submitBtn = form.querySelector('[type="submit"]');
                if(submitBtn) {
                    submitBtn.click();
                }
            }
        }
        
        // Ctrl/Cmd + F to focus search
        if((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            
            const searchInput = document.querySelector('.table-search input');
            if(searchInput) {
                searchInput.focus();
            }
        }
        
        // Escape to close modals
        if(e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                closeModal(modal);
            });
        }
        
        // Arrow keys for navigation
        if(e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            const pagination = document.querySelector('.table-pagination');
            if(pagination) {
                const active = pagination.querySelector('.page-num.active');
                if(active) {
                    if(e.key === 'ArrowLeft') {
                        pagination.querySelector('.page-prev:not([disabled])')?.click();
                    } else {
                        pagination.querySelector('.page-next:not([disabled])')?.click();
                    }
                }
            }
        }
    });
}

/**
 * ============================================
 * ACTIVITY LOGGER
 * ============================================
 */
function initActivityLogger() {
    // Log page views
    logActivity('page_view', {
        page: window.location.pathname,
        title: document.title
    });
    
    // Log form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            logActivity('form_submit', {
                form_id: this.id || 'unknown',
                action: this.action
            });
        });
    });
    
    // Log deletions
    document.querySelectorAll('.delete-btn, [data-action="delete"]').forEach(btn => {
        btn.addEventListener('click', function() {
            logActivity('delete_action', {
                target: this.dataset.target || 'unknown',
                type: this.dataset.type || 'item'
            });
        });
    });
}

function logActivity(action, data) {
    // Send to server via beacon API if available
    if(navigator.sendBeacon) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('data', JSON.stringify(data));
        formData.append('timestamp', Date.now());
        
        navigator.sendBeacon('ajax/log-activity.php', formData);
    } else {
        // Fallback to AJAX
        fetch('ajax/log-activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                data: data,
                timestamp: Date.now()
            })
        }).catch(() => {});
    }
}

/**
 * ============================================
 * UTILITY FUNCTIONS
 * ============================================
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if(!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day)
        .replace('HH', hours)
        .replace('mm', minutes);
}

function formatCurrency(amount, currency = 'ETB') {
    return `${currency} ${parseFloat(amount).toFixed(2)}`;
}

function truncateText(text, length = 50) {
    if(text.length <= length) return text;
    return text.substr(0, length) + '...';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * ============================================
 * INITIALIZATION
 * ============================================
 */

// Add custom styles for admin JS
const style = document.createElement('style');
style.textContent = `
    /* Toast Notifications */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    
    .toast {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-bottom: 10px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 300px;
        max-width: 400px;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .toast.show {
        transform: translateX(0);
    }
    
    .toast-success {
        border-left: 4px solid #00a400;
    }
    
    .toast-error {
        border-left: 4px solid #f44336;
    }
    
    .toast-warning {
        border-left: 4px solid #ff9800;
    }
    
    .toast-info {
        border-left: 4px solid #2196F3;
    }
    
    .toast-icon i {
        font-size: 24px;
    }
    
    .toast-success .toast-icon i {
        color: #00a400;
    }
    
    .toast-error .toast-icon i {
        color: #f44336;
    }
    
    .toast-warning .toast-icon i {
        color: #ff9800;
    }
    
    .toast-info .toast-icon i {
        color: #2196F3;
    }
    
    .toast-content {
        flex: 1;
    }
    
    .toast-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #999;
        padding: 0 5px;
    }
    
    .toast-close:hover {
        color: #333;
    }
    
    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: #00a400;
        width: 100%;
        animation: progress 4s linear forwards;
    }
    
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    
    /* Field Validation */
    .field-error {
        color: #f44336;
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .field-error i {
        font-size: 14px;
    }
    
    input.error, textarea.error, select.error {
        border-color: #f44336 !important;
    }
    
    input.success, textarea.success, select.success {
        border-color: #00a400 !important;
    }
    
    /* Image Preview */
    .image-preview {
        margin-top: 15px;
        border: 2px dashed #e0e0e0;
        border-radius: 10px;
        padding: 15px;
    }
    
    .image-preview.show {
        border-color: #00a400;
        background: #f8f9fa;
    }
    
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .preview-header h4 {
        font-size: 14px;
        color: #666;
    }
    
    .preview-header button {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #999;
    }
    
    .preview-header button:hover {
        color: #f44336;
    }
    
    .preview-content img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .image-info {
        font-size: 12px;
        color: #666;
        display: flex;
        justify-content: space-between;
    }
    
    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        user-select: none;
    }
    
    .data-table th[data-sort]::after {
        content: '↕️';
        margin-left: 5px;
        opacity: 0.5;
        font-size: 12px;
    }
    
    .data-table th.sort-asc::after {
        content: '↑';
        opacity: 1;
    }
    
    .data-table th.sort-desc::after {
        content: '↓';
        opacity: 1;
    }
    
    .data-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .data-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .data-table tbody tr.selected {
        background: #e8f5e8;
    }
    
    /* Pagination */
    .table-pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
    }
    
    .table-pagination button {
        padding: 8px 12px;
        border: 1px solid #e0e0e0;
        background: white;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .table-pagination button:hover:not(:disabled) {
        background: #00a400;
        color: white;
        border-color: #00a400;
    }
    
    .table-pagination button.active {
        background: #00a400;
        color: white;
        border-color: #00a400;
    }
    
    .table-pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .page-dots {
        padding: 8px 12px;
        color: #666;
    }
    
    /* Bulk Actions */
    .bulk-actions {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: white;
        padding: 15px 25px;
        border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    
    .bulk-actions.show {
        transform: translateX(-50%) translateY(0);
    }
    
    .selected-count {
        background: #00a400;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .bulk-actions-buttons {
        display: flex;
        gap: 10px;
    }
    
    .bulk-action-btn {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .bulk-action-btn.delete {
        background: #f44336;
        color: white;
    }
    
    .bulk-action-btn.delete:hover {
        background: #d32f2f;
    }
    
    /* Auto Save Indicator */
    .auto-save-indicator {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #00a400;
        color: white;
        padding: 10px 20px;
        border-radius: 30px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateY(100px);
        transition: transform 0.3s ease;
        z-index: 999;
    }
    
    .auto-save-indicator.show {
        transform: translateY(0);
    }
    
    .auto-save-indicator i {
        font-size: 16px;
    }
    
    /* Sidebar Overlay */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 99;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }
`;

document.head.appendChild(style);

// Show a success toast
showToast('Owner added successfully!', 'success');

// Show an error toast
showToast('Failed to delete product', 'error');

// Validate a form manually
if(validateForm(yourForm)) {
    // Submit form
}

// Get selected IDs from table
const selectedIds = getSelectedIds();

// Format date
const date = formatDate('2024-01-15', 'MM/DD/YYYY');