/**
 * Admin JavaScript
 * Mamz Clothing - Fashion Marketplace
 */

document.addEventListener('DOMContentLoaded', function() {
    initAdmin();
});

function initAdmin() {
    // Initialize sidebar toggle
    initSidebarToggle();
    
    // Initialize dropdowns
    initDropdowns();
    
    // Initialize modals
    initModals();
    
    // Initialize confirmations
    initConfirmations();
    
    // Initialize image preview
    initImagePreview();
    
    // Initialize charts
    initCharts();
}

/**
 * Sidebar Toggle
 */
function initSidebarToggle() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
}

/**
 * Dropdowns
 */
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.action-dropdown');
    
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('.action-btn');
        const menu = dropdown.querySelector('.action-menu');
        
        if (button && menu) {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close other dropdowns
                document.querySelectorAll('.action-menu').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });
                
                menu.classList.toggle('show');
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.action-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    });
}

/**
 * Modals
 */
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modals = document.querySelectorAll('.modal');
    const modalCloses = document.querySelectorAll('.modal-close');
    
    // Open modal
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.dataset.modal;
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.classList.add('show');
            }
        });
    });
    
    // Close modal
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
            }
        });
    });
    
    // Close modal on outside click
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });
}

/**
 * Confirmations
 */
function initConfirmations() {
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.dataset.confirm;
            const href = this.href;
            
            confirmAction(message, function() {
                window.location.href = href;
            });
        });
    });
}

/**
 * Image Preview
 */
function initImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(`#${input.id}-preview`);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

/**
 * Charts
 */
function initCharts() {
    // Daily sales chart
    const dailySalesCanvas = document.getElementById('dailySalesChart');
    if (dailySalesCanvas && typeof Chart !== 'undefined') {
        const dailySalesData = JSON.parse(dailySalesCanvas.dataset.chartData || '[]');
        new Chart(dailySalesCanvas, {
            type: 'line',
            data: {
                labels: dailySalesData.map(d => d.date),
                datasets: [{
                    label: 'Penjualan',
                    data: dailySalesData.map(d => d.total),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Monthly sales chart
    const monthlySalesCanvas = document.getElementById('monthlySalesChart');
    if (monthlySalesCanvas && typeof Chart !== 'undefined') {
        const monthlySalesData = JSON.parse(monthlySalesCanvas.dataset.chartData || '[]');
        new Chart(monthlySalesCanvas, {
            type: 'bar',
            data: {
                labels: monthlySalesData.map(d => d.month),
                datasets: [{
                    label: 'Penjualan',
                    data: monthlySalesData.map(d => d.total),
                    backgroundColor: '#2563EB'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Top products chart
    const topProductsCanvas = document.getElementById('topProductsChart');
    if (topProductsCanvas && typeof Chart !== 'undefined') {
        const topProductsData = JSON.parse(topProductsCanvas.dataset.chartData || '[]');
        new Chart(topProductsCanvas, {
            type: 'doughnut',
            data: {
                labels: topProductsData.map(d => d.nama_produk),
                datasets: [{
                    data: topProductsData.map(d => d.total_sold),
                    backgroundColor: [
                        '#2563EB',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#EC4899',
                        '#06B6D4',
                        '#84CC16',
                        '#F97316',
                        '#6366F1'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
}

/**
 * Update Status
 */
function updateStatus(url, data, callback) {
    ajaxRequest(url, 'POST', data, function(response) {
        if (response.success) {
            showAlert('success', response.message);
            if (callback) callback();
        } else {
            showAlert('error', response.message);
        }
    });
}

/**
 * Delete Item
 */
function deleteItem(url, callback) {
    confirmAction('Apakah Anda yakin ingin menghapus item ini?', function() {
        ajaxRequest(url, 'POST', {}, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                if (callback) callback();
            } else {
                showAlert('error', response.message);
            }
        });
    });
}
