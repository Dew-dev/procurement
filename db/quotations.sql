CREATE TABLE quotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfq_id INT,
    quotation_number VARCHAR(100) UNIQUE,
    quotation_date DATE,
    FOREIGN KEY (rfq_id) REFERENCES rfqs(id)
);