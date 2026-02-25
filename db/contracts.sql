CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_number VARCHAR(100) UNIQUE,
    buyer_name VARCHAR(150),
    rfq_from_buyer DATE,
    quotation_to_buyer DATE,
    contract_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);