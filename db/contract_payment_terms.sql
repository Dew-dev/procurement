CREATE TABLE contract_payment_terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT,
    term_code VARCHAR(20),   -- DP, P1, P2, P3
    percentage DECIMAL(5,2),
    invoice_number VARCHAR(100),
    invoice_date DATE,
    paid_date DATE,
    FOREIGN KEY (contract_id) REFERENCES contracts(id)
);