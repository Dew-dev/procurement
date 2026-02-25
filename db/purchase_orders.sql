CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT,
    po_number VARCHAR(100) UNIQUE,
    po_date DATE,
    po_payment_term VARCHAR(50),
    wip_status VARCHAR(50),
    exact_delivery_date DATE,
    dimension VARCHAR(255),
    weight VARCHAR(100),
    shipping_documents TEXT,
    incoterm VARCHAR(50),
    FOREIGN KEY (quotation_id) REFERENCES quotations(id)
);