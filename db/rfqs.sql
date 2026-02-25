CREATE TABLE rfqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT,
    rfq_number VARCHAR(100) UNIQUE,
    rfq_date DATE,
    maker VARCHAR(100),
    FOREIGN KEY (contract_id) REFERENCES contracts(id)
);