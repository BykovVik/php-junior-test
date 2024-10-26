CREATE TABLE events (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    schedule DATE
);

CREATE TABLE ticket_types (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    price INT
);

CREATE TABLE orders (
    id INT PRIMARY KEY,
    event_id INT,
    event_date DATE,
    user_id INT,
    created_at DATETIME,
    FOREIGN KEY (event_id) REFERENCES events(id)
);

CREATE TABLE order_items (
    id INT PRIMARY KEY,
    order_id INT,
    ticket_type_id INT,
    quantity INT,
    price INT,
    barcode VARCHAR(255),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id)
);