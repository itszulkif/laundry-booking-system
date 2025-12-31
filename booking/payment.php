<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/paypal_config.php';
require_once '../includes/payid_config.php';
require_once '../includes/afterpay_config.php';
require_once '../includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id === 0) {
    redirect('../index.php');
}

// Fetch Order Details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$subtotal = $order['total_price'] - $order['tax_amount'];

// Check if already paid
if ($order['status'] !== 'pending') {
    // If already paid or cancelled, redirect appropriately
    if ($order['status'] === 'confirmed' || $order['status'] === 'completed') {
         // Show success message or redirect to a status page
         echo "<script>window.location.href = 'payment_success.php?existing=1&order_id=$order_id';</script>";
         exit;
    }
}

?>

<style>
.payment-method-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.payment-method-card:hover {
    border-color: #0070ba;
    box-shadow: 0 4px 12px rgba(0, 112, 186, 0.15);
    transform: translateY(-2px);
}

.payment-method-card.active {
    border-color: #0070ba;
    background: #f8fbff;
}

.payment-method-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.payment-method-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.payment-method-description {
    color: #666;
    font-size: 0.9rem;
}

.payment-container {
    display: none;
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #f9f9f9;
}

.payment-container.active {
    display: block;
}

/* PayID Modal Styles */
.payid-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    animation: fadeIn 0.3s ease;
}

.payid-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.payid-modal-content {
    background-color: white;
    border-radius: 16px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
    margin: 20px;
}

.payid-modal-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.payid-modal-header h4 {
    margin: 0;
    font-size: 1.25rem;
}

.payid-modal-body {
    padding: 24px 20px;
}

.payid-content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}

.payid-details {
    width: 100%;
}

.payid-detail-item {
    margin-bottom: 16px;
    padding: 14px 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.payid-detail-item:last-child {
    margin-bottom: 0;
}

.payid-detail-label {
    font-size: 0.75rem;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
}

.payid-detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    word-break: break-word;
}

.payid-modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.close-modal:hover {
    background: #f0f0f0;
    color: #333;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .payid-modal-content {
        width: 95%;
        max-width: none;
        margin: 10px;
        border-radius: 12px;
    }
    
    .payid-modal-header {
        padding: 16px;
    }
    
    .payid-modal-header h4 {
        font-size: 1.1rem;
    }
    
    .payid-modal-body {
        padding: 20px 16px;
    }
    
    .payid-detail-item {
        margin-bottom: 12px;
        padding: 12px 14px;
    }
    
    .payid-detail-label {
        font-size: 0.7rem;
    }
    
    .payid-detail-value {
        font-size: 0.95rem;
    }
    
    .payid-modal-footer {
        padding: 14px 16px;
        gap: 8px;
    }
    
    .payid-modal-footer .btn {
        flex: 1;
        min-width: 0;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .payid-modal-content {
        width: 100%;
        margin: 0;
        border-radius: 0;
        max-height: 100vh;
    }
    
    .payid-modal-footer {
        flex-direction: column;
    }
    
    .payid-modal-footer .btn {
        width: 100%;
    }
}

/* Afterpay Button Styles */
#afterpay-button-container {
    margin-top: 16px;
}

.afterpay-button {
    background: linear-gradient(135deg, #b2fce4 0%, #06ffa5 100%);
    color: #000;
    border: none;
    padding: 16px 32px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.afterpay-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(6, 255, 165, 0.4);
}

.afterpay-logo {
    height: 24px;
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="antigravity-card p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Complete Your Payment</h3>
                    <p class="text-muted">Order Code: <span class="text-primary fw-bold"><?php echo htmlspecialchars($order['order_code']); ?></span></p>
                </div>

                <div class="bg-light p-3 rounded mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Customer</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Service Date</span>
                        <span class="fw-bold"><?php echo date('M d, Y', strtotime($order['booking_date'])); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">GST (10%)</span>
                        <span class="fw-bold">$<?php echo number_format($order['tax_amount'], 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold">Total Amount</span>
                        <span class="fs-4 fw-bold text-primary">$<?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                </div>

                <h5 class="mb-3 fw-bold">Select Payment Method</h5>

                <!-- PayPal Payment Method -->
                <div class="payment-method-card" onclick="selectPaymentMethod('paypal')">
                    <div class="payment-method-icon text-primary">
                        <i class="bi bi-paypal"></i>
                    </div>
                    <div class="payment-method-title">PayPal</div>
                    <div class="payment-method-description">Pay securely with your PayPal account or credit card</div>
                </div>
                <div id="paypal-container" class="payment-container">
                    <div id="paypal-button-container"></div>
                </div>

                <!-- PayID Payment Method -->
                <div class="payment-method-card" onclick="openPayIDModal()">
                    <div class="payment-method-icon" style="color: #FF6B00;">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div class="payment-method-title">PayID</div>
                    <div class="payment-method-description">Scan QR code with your banking app to pay instantly</div>
                </div>

                <!-- Afterpay Payment Method -->
                <div class="payment-method-card" onclick="selectPaymentMethod('afterpay')">
                    <div class="payment-method-icon" style="color: #B2FCE4;">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <div class="payment-method-title">Afterpay</div>
                    <div class="payment-method-description">Buy now, pay later in 4 interest-free installments</div>
                </div>
                <div id="afterpay-container" class="payment-container">
                    <div id="afterpay-button-container">
                        <button class="afterpay-button" onclick="initiateAfterpay()">
                            <span>Pay with</span>
                            <strong>Afterpay</strong>
                        </button>
                    </div>
                    <p class="text-center text-muted mt-3 small">
                        <i class="bi bi-info-circle"></i> 4 interest-free payments of $<?php echo number_format($order['total_price'] / 4, 2); ?>
                    </p>
                </div>
                
                <div class="text-center mt-4">
                    <a href="payment_cancel.php?order_id=<?php echo $order_id; ?>" class="text-muted small text-decoration-none">Cancel Payment</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PayID Modal -->
<div id="payidModal" class="payid-modal">
    <div class="payid-modal-content">
        <div class="payid-modal-header">
            <h4 class="mb-0 fw-bold">Pay with PayID</h4>
            <button class="close-modal" onclick="closePayIDModal()">&times;</button>
        </div>
        <div class="payid-modal-body">
            
            <div class="payid-content-grid">
                <div class="payid-details">
                    <div class="payid-detail-item">
                        <div class="payid-detail-label">Business Name</div>
                        <div class="payid-detail-value"><?php echo htmlspecialchars(PAYID_BUSINESS_NAME); ?></div>
                    </div>
                    
                    <div class="payid-detail-item">
                        <div class="payid-detail-label">Mobile Number</div>
                        <div class="payid-detail-value"><?php echo htmlspecialchars(PAYID_MOBILE_NUMBER); ?></div>
                    </div>
                    
                    <div class="payid-detail-item">
                        <div class="payid-detail-label">Order Code</div>
                        <div class="payid-detail-value"><?php echo htmlspecialchars($order['order_code']); ?></div>
                    </div>
                    
                    <div class="payid-detail-item">
                        <div class="payid-detail-label">Amount to Pay</div>
                        <div class="payid-detail-value text-primary">$<?php echo number_format($order['total_price'], 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="payid-modal-footer">
            <button class="btn btn-secondary" onclick="closePayIDModal()">Cancel</button>
            <button class="btn btn-primary" onclick="confirmPayIDPayment()">
                <i class="bi bi-check-circle"></i> I've Completed Payment
            </button>
        </div>
    </div>
</div>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=<?php echo PAYPAL_CURRENCY; ?>"></script>

<!-- Afterpay SDK -->
<script src="<?php echo AFTERPAY_JS_SDK; ?>"></script>

<script>
let currentPaymentMethod = null;

function selectPaymentMethod(method) {
    // Hide all payment containers
    document.querySelectorAll('.payment-container').forEach(container => {
        container.classList.remove('active');
    });
    
    // Remove active class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('active');
    });
    
    // Show selected payment container
    const container = document.getElementById(method + '-container');
    if (container) {
        container.classList.add('active');
    }
    
    // Add active class to selected card (find by onclick attribute)
    event.currentTarget.classList.add('active');
    
    currentPaymentMethod = method;
    
    // Initialize PayPal if selected and not already initialized
    if (method === 'paypal' && !window.paypalInitialized) {
        initializePayPal();
    }
}

function initializePayPal() {
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $order['total_price']; ?>'
                    },
                    description: 'Order #<?php echo $order['order_code']; ?>'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Show a loading spinner or message
                document.getElementById('paypal-button-container').innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Processing payment...</p></div>';
                
                // Post data to success page
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'payment_success.php';
                
                const inputs = [
                    {name: 'order_id', value: '<?php echo $order_id; ?>'},
                    {name: 'transaction_id', value: details.id},
                    {name: 'payer_id', value: details.payer.payer_id},
                    {name: 'amount', value: details.purchase_units[0].amount.value},
                    {name: 'currency', value: details.purchase_units[0].amount.currency_code},
                    {name: 'status', value: details.status},
                    {name: 'payment_method', value: 'paypal'}
                ];
                
                inputs.forEach(input => {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = input.name;
                    hiddenField.value = input.value;
                    form.appendChild(hiddenField);
                });
                
                document.body.appendChild(form);
                form.submit();
            });
        },
        onCancel: function(data) {
            window.location.href = 'payment_cancel.php?order_id=<?php echo $order_id; ?>';
        },
        onError: function(err) {
            console.error(err);
            alert('An error occurred during payment. Please try again.');
        }
    }).render('#paypal-button-container');
    
    window.paypalInitialized = true;
}

// PayID Modal Functions
function openPayIDModal() {
    document.getElementById('payidModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closePayIDModal() {
    document.getElementById('payidModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

function confirmPayIDPayment() {
    // Show loading state
    const modal = document.getElementById('payidModal');
    const footer = modal.querySelector('.payid-modal-footer');
    footer.innerHTML = '<div class="spinner-border text-primary" role="status"></div><span class="ms-2">Processing...</span>';
    
    // Submit payment confirmation
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'process_payid_payment.php';
    
    const orderIdInput = document.createElement('input');
    orderIdInput.type = 'hidden';
    orderIdInput.name = 'order_id';
    orderIdInput.value = '<?php echo $order_id; ?>';
    form.appendChild(orderIdInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Afterpay Functions
function initiateAfterpay() {
    // Check amount limits
    const amount = <?php echo $order['total_price']; ?>;
    if (amount < <?php echo AFTERPAY_MIN_AMOUNT; ?>) {
        alert('Order amount is below Afterpay minimum of $<?php echo AFTERPAY_MIN_AMOUNT; ?>');
        return;
    }
    if (amount > <?php echo AFTERPAY_MAX_AMOUNT; ?>) {
        alert('Order amount exceeds Afterpay maximum of $<?php echo AFTERPAY_MAX_AMOUNT; ?>');
        return;
    }
    
    // Show loading
    document.getElementById('afterpay-button-container').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p class="mt-2">Initializing Afterpay...</p></div>';
    
    // Create Afterpay checkout
    fetch('create_afterpay_checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: <?php echo $order_id; ?>,
            amount: amount,
            currency: '<?php echo AFTERPAY_CURRENCY; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.token) {
            // Redirect to Afterpay
            window.location.href = data.redirectCheckoutUrl;
        } else {
            alert('Error initializing Afterpay: ' + (data.error || 'Unknown error'));
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error initializing Afterpay. Please try again.');
        location.reload();
    });
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('payidModal');
    if (event.target === modal) {
        closePayIDModal();
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
