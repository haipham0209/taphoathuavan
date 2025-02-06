
UPDATE user 
SET status = 'active' 
WHERE username = 'lan1';

drop table order_details;
drop table discounts;
drop table product;
drop table category;
drop table StoreDescriptions;
drop table orders;
drop table daily_revenue;
drop table store;
drop table user;


DROP DATABASE IF EXISTS wearebugs;


CREATE DATABASE wearebugs;


USE wearebugs;


 CREATE TABLE user (
    userid INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    mail VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(255),
    status ENUM('pending', 'active') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE store (
    storeid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL UNIQUE,
    sname VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    tel VARCHAR(20),
    logopath VARCHAR(255) DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES user(userid)
);

CREATE TABLE daily_revenue (
    store_id INT NOT NULL,
    revenue_date DATE NOT NULL,
    total_revenue DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_profit DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (store_id, revenue_date),
    FOREIGN KEY (store_id) REFERENCES store(storeid)
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    store_id INT NOT NULL,
    customer_id INT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'canceled') DEFAULT 'pending',
    received_amount DECIMAL(10, 2) NULL,
    FOREIGN KEY (store_id) REFERENCES store(storeid)
);


CREATE TABLE StoreDescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    storeid INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (storeid) REFERENCES store(storeid)
);


CREATE TABLE category (
    category_id INT NOT NULL,
    storeid INT NOT NULL,
    cname VARCHAR(100) NOT NULL,
    PRIMARY KEY (category_id, storeid),
    FOREIGN KEY (storeid) REFERENCES store(storeid)
);



CREATE TABLE product (

    productid INT NOT NULL,
    storeid INT NOT NULL,
    category_id INT NOT NULL,
    pname VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    costPrice DECIMAL(10,2) NOT NULL,
    discounted_price DECIMAL(10, 2),
    description TEXT,
    stock_quantity INT NOT NULL,
    barcode VARCHAR(13) NOT NULL,
    productImage VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (storeid) REFERENCES store(storeid),
    FOREIGN KEY (category_id, storeid) REFERENCES category(category_id, storeid)
);
CREATE INDEX idx_productid ON product(productid);
ALTER TABLE product ADD CONSTRAINT unique_barcode_per_store UNIQUE (storeid, barcode);


CREATE TABLE discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    productid INT NOT NULL,
    discount_value DECIMAL(5, 2) NOT NULL,
    start_date DATETIME,
    end_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (productid) REFERENCES product(productid)
);


CREATE TABLE order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL,
    productid INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_number) REFERENCES orders(order_number),
    FOREIGN KEY (productid) REFERENCES product(productid)
);

CREATE TABLE order_history (
    orderid INT AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(10) NOT NULL,
    storeid INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    order_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE orders ADD COLUMN discount INT;
-- select * from order_details;
ALTER TABLE order_details ADD COLUMN item_price INT AFTER productid;
-- 1 cửa hàng ko trùng barcode nhưng cửa hàng khác nhau thì ok




-- Thêm bảng 2024/11/22
-- Bảng lưu đơn hàng
ALTER TABLE order_details DROP FOREIGN KEY order_details_ibfk_1;
drop table order_details;
drop table orders;
drop table daily_revenue;
-- Bảng lưu đơn hàng
-- Tạo bảng orders sau khi sửa lỗi











-- ALTER TABLE store
-- ADD COLUMN logopath VARCHAR(255) DEFAULT NULL AFTER tel;

UPDATE user 
SET status = 'active' 
WHERE username = 'hai';


show columns from user;
show columns from store;
show columns from product;
show columns from category;

-- 1. Xóa bảng có khóa ngoại trước (tránh lỗi liên kết)
DROP TABLE order_details;
DROP TABLE discounts;
DROP TABLE product;
DROP TABLE category;
DROP TABLE orders;
DROP TABLE daily_revenue;

-- 2. Xóa bảng không phụ thuộc
DROP TABLE IF EXISTS StoreDescriptions;
DROP TABLE IF EXISTS store;
DROP TABLE IF EXISTS user;


-- DROP DATABASE IF EXISTS wearebugs; -- Xóa cơ sở dữ liệu nếu đã tồn tại
-- CREATE DATABASE wearebugs; -- Tạo cơ sở dữ liệu mới
-- USE wearebugs; -- Sử dụng cơ sở dữ liệu vừa tạo

-- -- Bảng user
-- CREATE TABLE user (
--     userid INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(100) NOT NULL UNIQUE COLLATE utf8mb4_0900_ai_ci,
--     mail VARCHAR(255) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL COLLATE utf8mb4_0900_ai_ci,
--     token VARCHAR(255),
--     status ENUM('pending', 'active') NOT NULL DEFAULT 'pending',
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- -- Bảng category
-- CREATE TABLE category (
--     category_id INT NOT NULL, -- Bỏ AUTO_INCREMENT
--     userid INT NOT NULL,
--     cname VARCHAR(100) NOT NULL,
--     PRIMARY KEY (category_id, userid), -- Thay đổi ràng buộc PRIMARY KEY
--     FOREIGN KEY (userid) REFERENCES user(userid) ON DELETE CASCADE,
--     CONSTRAINT unique_cname UNIQUE (cname, userid) -- Đảm bảo cname duy nhất theo userid
-- );

-- -- Bảng store
-- CREATE TABLE store (
--     storeid INT AUTO_INCREMENT PRIMARY KEY,
--     userid INT NOT NULL,
--     sname VARCHAR(100) NOT NULL COLLATE utf8mb4_0900_ai_ci,
--     address VARCHAR(255),
--     tel VARCHAR(20),
--     description TEXT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     CONSTRAINT fk_user FOREIGN KEY (userid) REFERENCES user(userid) 
--     ON DELETE CASCADE 
-- );



-- -- Bảng product
-- CREATE TABLE product (
--     productid INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- Thêm AUTO_INCREMENT
--     userid INT NOT NULL,
--     category_id INT NOT NULL,
--     -- storeid INT NOT NULL, -- Thêm trường storeid
--     pname VARCHAR(255) NOT NULL,
--     price DECIMAL(10, 2) NOT NULL,
--     costPrice DECIMAL(10, 2) NOT NULL,
--     description TEXT,
--     stock_quantity INT NOT NULL,
--     barcode VARCHAR(13) UNIQUE NOT NULL,
--     productImage VARCHAR(255), -- Thêm cột productImage để chứa đường dẫn hình ảnh
--     FOREIGN KEY (userid) REFERENCES user(userid),
--     FOREIGN KEY (category_id) REFERENCES category(category_id)
--     -- FOREIGN KEY (userid) REFERENCES store(userid),
-- );

-- -- Chèn dữ liệu vào bảng user
-- INSERT INTO user (username, mail, password, token, status) 
-- VALUES 
-- ('wrb', 'wrb@example.com', '$2y$10$8Xj..zBCFY87Dl1yrBqxdepSMjaIBUVleEfnD8sfyDKjqRYmyOyb6', 'token123', 'active');

-- -- Chèn dữ liệu vào bảng store
-- INSERT INTO store (userid, sname, address, tel, description)
-- VALUES (1, 'WRB STORE', '123 Street ABC', '0123456789', 'AAAAAAA');

-- UPDATE user 
-- SET status = 'active' 
-- WHERE username = 'hai';
------------------------------------------------------------------------------------------------------------------------------


