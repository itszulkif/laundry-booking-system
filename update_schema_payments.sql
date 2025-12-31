-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    payer_id VARCHAR(100),
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    payment_status VARCHAR(50) NOT NULL, -- completed, pending, failed, refunded
    payment_method VARCHAR(50) DEFAULT 'paypal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Add index for faster lookups
CREATE INDEX idx_payment_transaction ON payments(transaction_id);
CREATE INDEX idx_payment_order ON payments(order_id);
