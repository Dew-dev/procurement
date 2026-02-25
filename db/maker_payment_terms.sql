CREATE TABLE maker_payment_terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT,
    term_code VARCHAR(20),   -- DP, P1, P2, P3
    percentage DECIMAL(5,2),
    invoice_number VARCHAR(100),
    invoice_date DATE,
    paid_date DATE,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id)
);