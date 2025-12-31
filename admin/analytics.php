<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Calculate KPIs
// Total Revenue
$revenue_query = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'COMPLETED'";
$revenue_result = $conn->query($revenue_query);
$total_revenue = $revenue_result->fetch_assoc()['total'] ?? 0;

// Total Transactions
$tx_query = "SELECT COUNT(*) as total FROM payments";
$tx_result = $conn->query($tx_query);
$total_tx = $tx_result->fetch_assoc()['total'];

// Success Rate
$success_query = "SELECT COUNT(*) as total FROM payments WHERE payment_status = 'COMPLETED'";
$success_result = $conn->query($success_query);
$success_tx = $success_result->fetch_assoc()['total'];
$success_rate = $total_tx > 0 ? ($success_tx / $total_tx) * 100 : 0;

// Monthly Revenue (Last 6 months)
// Monthly Revenue (Last 6 months)
$six_months_ago = date('Y-m-01', strtotime('-5 months'));
$query = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month_key,
            DATE_FORMAT(created_at, '%b') as month_label, 
            SUM(amount) as total 
          FROM payments 
          WHERE payment_status = 'COMPLETED' 
          AND created_at >= '$six_months_ago' 
          GROUP BY YEAR(created_at), MONTH(created_at) 
          ORDER BY created_at ASC";
$result = $conn->query($query);

$revenue_data = [];
while($row = $result->fetch_assoc()) {
    $revenue_data[$row['month_key']] = $row;
}

// Ensure all last 6 months are represented
$monthly_labels = [];
$monthly_values = [];

for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $label = date('M', strtotime("-$i months"));
    
    $monthly_labels[] = $label;
    $monthly_values[] = isset($revenue_data[$date]) ? (float)$revenue_data[$date]['total'] : 0;
}

// Top Customers
$top_query = "SELECT o.customer_name, SUM(p.amount) as total_spent, COUNT(p.id) as tx_count 
              FROM payments p 
              JOIN orders o ON p.order_id = o.id 
              WHERE p.payment_status = 'COMPLETED' 
              GROUP BY o.customer_email 
              ORDER BY total_spent DESC 
              LIMIT 5";
$top_result = $conn->query($top_query);
?>

<div class="mb-4">
    <h2 class="fw-bold text-primary">Analytics Dashboard</h2>
    <p class="text-muted">Financial performance and payment insights.</p>
</div>

<!-- KPIs -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted text-uppercase fw-bold m-0">Total Revenue</h6>
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <i class="bi bi-currency-dollar text-primary fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">$<?php echo number_format($total_revenue, 2); ?></h3>
                <small class="text-success"><i class="bi bi-arrow-up-short"></i> Lifetime Earnings</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted text-uppercase fw-bold m-0">Transactions</h6>
                    <div class="bg-info bg-opacity-10 p-2 rounded">
                        <i class="bi bi-receipt text-info fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?php echo $total_tx; ?></h3>
                <small class="text-muted">Total Payments Processed</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted text-uppercase fw-bold m-0">Success Rate</h6>
                    <div class="bg-success bg-opacity-10 p-2 rounded">
                        <i class="bi bi-check-lg text-success fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?php echo number_format($success_rate, 1); ?>%</h3>
                <small class="text-muted">Completed vs Failed</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Monthly Revenue Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0">Monthly Revenue</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($monthly_labels); ?>,
            datasets: [{
                label: 'Revenue ($)',
                data: <?php echo json_encode($monthly_values); ?>,
                borderColor: '#0d6efd',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0d6efd',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5],
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
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
    </script>
            </div>
        </div>
    </div>
    
    <!-- Top Customers -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0">Top Customers</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if ($top_result->num_rows > 0): ?>
                        <?php while($row = $top_result->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($row['customer_name']); ?></h6>
                                <small class="text-muted"><?php echo $row['tx_count']; ?> transactions</small>
                            </div>
                            <span class="fw-bold text-primary">$<?php echo number_format($row['total_spent'], 2); ?></span>
                        </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted py-4">No data available</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
