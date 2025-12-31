        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="newOrderToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-primary text-white">
            <i class="bi bi-bell-fill me-2"></i>
            <strong class="me-auto">New Order Received!</strong>
            <small class="text-white-50" id="toastTime">Just now</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody">
            <!-- Order details will be inserted here -->
        </div>
    </div>
</div>

<!-- Notification Sound -->
<audio id="notificationSound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGWi77eeeTRAMUKfj8LZjHAY4ktfyzHksBSR3x/DdkEAKFF606+uoVRQKRp/g8r5sIQUrgs7y2Ik2CBlou+3nnk0QDFCn4/C2YxwGOJLX8sx5LAUkd8fw3ZBBChRdtOvrqFUUCkaf4PK+bCEFK4LO8tmJNggZaLvt555NEAxQp+PwtmMcBjiS1/LMeSwFJHfH8N2QQQoUXbTr66hVFApGn+DyvmwhBSuCzvLZiTYIGWi77eeeTRAMUKfj8LZjHAY4ktfyzHksBSR3x/DdkEEKFF206+uoVRQKRp/g8r5sIQUrgs7y2Ik2CBlou+3nnk0QDFCn4/C2YxwGOJLX8sx5LAUkd8fw3ZBBChRdtOvrqFUUCkaf4PK+bCEFK4LO8tmJNggZaLvt555NEAxQp+PwtmMcBjiS1/LMeSwFJHfH8N2QQQoUXbTr66hVFApGn+DyvmwhBSuCzvLZiTYIGWi77eeeTRAMUKfj8LZjHAY4ktfyzHksBSR3x/DdkEEKFF206+uoVRQKRp/g8r5sIQUrgs7y2Ik2CBlou+3nnk0QDFCn4/C2YxwGOJLX8sx5LAUkd8fw3ZBBChRdtOvrqFUUCkaf4PK+bCEFK4LO8tmJNggZaLvt555NEAxQp+PwtmMcBjiS1/LMeSwFJHfH8N2QQQoUXbTr66hVFApGn+DyvmwhBSuCzvLZiTYIGWi77eeeTRAMUKfj8LZjHAY4ktfyzHksBSR3x/DdkEEKFF206+uoVRQKRp/g8r5sIQUrgs7y2Ik2CBlou+3nnk0QDFCn4/C2YxwGOJLX8sx5LAUkd8fw3ZBBChRdtOvrqFUUCkaf4PK+bCEFK4LO8tmJNggZaLvt555NEAxQp+PwtmMcBjiS1/LMeSwFJHfH8N2QQQoUXbTr66hVFApGn+DyvmwhBSuCzvLZiTYIGWi77eeeTRAMUKfj8LZjHAY4ktfyzHksBSR3x/DdkEEKFF206+uoVRQKRp/g8r5sIQU=" type="audio/wav">
</audio>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Real-time Order Notification Script -->
<script>
(function() {
    let lastOrderId = 0;
    const CHECK_INTERVAL = 10000; // Check every 10 seconds
    const notificationSound = document.getElementById('notificationSound');
    const toastElement = document.getElementById('newOrderToast');
    const toastBody = document.getElementById('toastBody');
    const toastTime = document.getElementById('toastTime');
    
    // Initialize last order ID from localStorage
    const storedLastId = localStorage.getItem('lastOrderId');
    if (storedLastId) {
        lastOrderId = parseInt(storedLastId);
    } else {
        // Get current max order ID on first load
        fetch('../api/check_new_orders.php?last_id=999999')
            .then(response => response.json())
            .then(data => {
                // Set to current max, so we only notify for future orders
                fetch('../api/check_new_orders.php?last_id=0')
                    .then(response => response.json())
                    .then(allData => {
                        if (allData.new_orders && allData.new_orders.length > 0) {
                            lastOrderId = Math.max(...allData.new_orders.map(o => o.id));
                            localStorage.setItem('lastOrderId', lastOrderId);
                        }
                    });
            });
    }
    
    function checkNewOrders() {
        fetch(`../api/check_new_orders.php?last_id=${lastOrderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.new_orders && data.new_orders.length > 0) {
                    // Update last order ID
                    const maxId = Math.max(...data.new_orders.map(o => o.id));
                    lastOrderId = maxId;
                    localStorage.setItem('lastOrderId', lastOrderId);
                    
                    // Show notification for each new order
                    data.new_orders.forEach((order, index) => {
                        setTimeout(() => {
                            showNotification(order);
                        }, index * 500); // Stagger notifications by 500ms
                    });
                }
            })
            .catch(error => {
                console.error('Error checking for new orders:', error);
            });
    }
    
    function showNotification(order) {
        // Play sound
        if (notificationSound) {
            notificationSound.currentTime = 0;
            notificationSound.play().catch(e => console.log('Could not play sound:', e));
        }
        
        // Format the toast body
        const statusBadge = getStatusBadge(order.status);
        toastBody.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">${order.order_code}</h6>
                    <p class="mb-1 small"><i class="bi bi-person me-1"></i>${order.customer_name}</p>
                    <p class="mb-1 small"><i class="bi bi-geo-alt me-1"></i>${order.city_name || 'N/A'}</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="badge ${statusBadge.class} rounded-pill">${statusBadge.text}</span>
                        <strong class="text-primary">$${parseFloat(order.total_price).toFixed(2)}</strong>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <a href="orders.php" class="btn btn-sm btn-primary w-100">View Orders</a>
            </div>
        `;
        
        toastTime.textContent = 'Just now';
        
        // Show the toast
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 8000
        });
        toast.show();
    }
    
    function getStatusBadge(status) {
        const badges = {
            'pending': { class: 'bg-warning text-dark', text: 'Pending' },
            'confirmed': { class: 'bg-info text-white', text: 'Confirmed' },
            'in_progress': { class: 'bg-primary text-white', text: 'In Progress' },
            'completed': { class: 'bg-success text-white', text: 'Completed' },
            'cancelled': { class: 'bg-danger text-white', text: 'Cancelled' }
        };
        return badges[status] || { class: 'bg-secondary text-white', text: status };
    }
    
    // Start checking for new orders
    setInterval(checkNewOrders, CHECK_INTERVAL);
    
    // Also check when page becomes visible again
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkNewOrders();
        }
    });
})();
</script>

<style>
.toast {
    min-width: 320px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.toast-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.toast-body {
    padding: 1rem;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideInRight 0.3s ease-out;
}
</style>

</body>
</html>
