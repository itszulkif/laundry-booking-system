<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Get City ID
$city_id = isset($_GET['city_id']) ? (int)$_GET['city_id'] : 0;

if ($city_id === 0) {
    redirect('../index.php');
}

// Fetch City Name
$city_query = "SELECT name FROM cities WHERE id = $city_id";
$city_result = $conn->query($city_query);
$city = $city_result->fetch_assoc();

// Fetch Services
$services_query = "SELECT * FROM services WHERE status = 'active'";
$services_result = $conn->query($services_query);
?>

<div class="container py-5">
    <!-- Step Indicator -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="step-indicator">
                <div class="step-item completed"><i class="bi bi-geo-alt"></i></div>
                <div class="step-item active">2</div>
                <div class="step-item">3</div>
                <div class="step-item">4</div>
                <div class="step-item">5</div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold">Select Services</h2>
                <p class="text-muted">Available in <span class="text-primary fw-bold"><?php echo htmlspecialchars($city['name'] ?? 'Your City'); ?></span></p>
            </div>
        </div>
    </div>

    <form action="staff.php" method="GET" id="servicesForm">
        <input type="hidden" name="city_id" value="<?php echo $city_id; ?>">
        <input type="hidden" name="quantity" id="hiddenQuantity" value="1">
        
        <div class="row g-4">
            <!-- Services Grid -->
            <div class="col-lg-8">
                <div class="row g-4">
                    <?php if ($services_result->num_rows > 0): ?>
                        <?php while($service = $services_result->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <label class="antigravity-card p-4 h-100 d-block cursor-pointer position-relative service-card" for="service_<?php echo $service['id']; ?>">
                                    <input type="radio" name="service_id" value="<?php echo $service['id']; ?>" 
                                           id="service_<?php echo $service['id']; ?>"
                                           class="form-check-input position-absolute top-0 end-0 m-3 service-radio" 
                                           data-price="<?php echo $service['price']; ?>"
                                           data-name="<?php echo htmlspecialchars($service['name']); ?>"
                                           data-unit="<?php echo $service['price_unit']; ?>">
                                    
                                    <div class="text-center mb-3">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi <?php echo $service['icon']; ?> fs-3 text-primary"></i>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold text-center mb-2"><?php echo htmlspecialchars($service['name']); ?></h5>
                                    <p class="text-muted text-center small mb-3"><?php echo htmlspecialchars($service['description']); ?></p>
                                    <div class="text-center">
                                        <span class="badge bg-primary-soft text-primary bg-opacity-10 fs-6">
                                            $<?php echo number_format($service['price'], 2); ?> / <?php echo $service['price_unit'] == 'kg' ? 'kg' : 'item'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Quantity Input for All Services -->
                                    <div class="mt-3 quantity-input-wrapper" style="display: none;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <button type="button" class="btn btn-sm btn-light border rounded-circle" onclick="updateQty(<?php echo $service['id']; ?>, -1); event.preventDefault();">-</button>
                                            <input type="number" 
                                                   id="qty_<?php echo $service['id']; ?>"
                                                   class="form-control form-control-sm text-center mx-2 quantity-input" 
                                                   style="width: 60px;"
                                                   value="1" min="1" step="1"
                                                   readonly>
                                            <button type="button" class="btn btn-sm btn-light border rounded-circle" onclick="updateQty(<?php echo $service['id']; ?>, 1); event.preventDefault();">+</button>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="text-muted">No services available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="col-lg-4">
                <div class="antigravity-card p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Booking Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">City</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($city['name'] ?? 'Unknown'); ?></span>
                    </div>
                    <hr>
                    <div id="selectedServicesList" class="mb-3">
                        <p class="text-muted small fst-italic">No service selected</p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary" id="totalPrice">$0.00</span>
                    </div>
                    <button type="submit" class="btn btn-primary-soft w-100" id="nextBtn" disabled>
                        Select Staff <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updateQty(id, change) {
    const input = document.getElementById('qty_' + id);
    let val = parseInt(input.value) || 1;
    val += change;
    if (val < 1) val = 1;
    input.value = val;
    
    // Trigger change event manually
    const event = new Event('input', { bubbles: true });
    input.dispatchEvent(event);
}

document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.service-radio');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const selectedList = document.getElementById('selectedServicesList');
    const totalPriceEl = document.getElementById('totalPrice');
    const nextBtn = document.getElementById('nextBtn');

    function updateSummary() {
        let total = 0;
        let html = '';
        let selected = false;

        // Reset all cards first
        document.querySelectorAll('.antigravity-card').forEach(c => {
            c.style.borderColor = 'rgba(0,0,0,0.02)';
            c.style.backgroundColor = 'var(--white)';
            const qw = c.querySelector('.quantity-input-wrapper');
            if(qw) qw.style.display = 'none';
        });

        radios.forEach(rb => {
            if (rb.checked) {
                selected = true;
                const card = rb.closest('.antigravity-card');
                const qtyWrapper = card.querySelector('.quantity-input-wrapper');
                const qtyInput = card.querySelector('.quantity-input');
                
                // Show quantity input
                qtyWrapper.style.display = 'block';
                
                // Highlight card
                card.style.borderColor = 'var(--primary-color)';
                card.style.backgroundColor = '#f8fbff';

                const price = parseFloat(rb.dataset.price);
                const name = rb.dataset.name;
                const unit = rb.dataset.unit;
                const qty = parseInt(qtyInput.value) || 1;
                
                // Update hidden quantity input
                document.getElementById('hiddenQuantity').value = qty;
                
                const itemTotal = price * qty;
                total = itemTotal;
                
                html = `
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">${name} (x${qty})</span>
                        <span class="small">$${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            }
        });

        if (!selected) {
            selectedList.innerHTML = '<p class="text-muted small fst-italic">No service selected</p>';
            nextBtn.disabled = true;
        } else {
            selectedList.innerHTML = html;
            nextBtn.disabled = false;
        }

        totalPriceEl.textContent = '$' + total.toFixed(2);
    }

    radios.forEach(rb => {
        rb.addEventListener('change', updateSummary);
    });

    quantityInputs.forEach(input => {
        input.addEventListener('input', updateSummary);
        input.addEventListener('click', (e) => e.stopPropagation()); // Prevent card click
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
