
<?php

$host = "dpg-d8abfkog4nts73dbvulg-a";
$user = "shikoku";
$pass = "qSTMZdU1idwuWso7eTiePld8lbkqgHjd";
$dbname = "diplom_xi6u";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$sql = "

CREATE TABLE IF NOT EXISTS addresses (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    is_default TINYINT(1) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS justemail (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(60) NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20) NOT NULL,
    user_login VARCHAR(50) NOT NULL,
    total DECIMAL(10,0) NOT NULL,
    status INT NOT NULL,
    payment_method INT NOT NULL,
    order_date INT NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    item VARCHAR(45) NOT NULL,
    image VARCHAR(60) NOT NULL,
    price VARCHAR(25) NOT NULL
);

CREATE TABLE IF NOT EXISTS regisrtation (
    NAME VARCHAR(50) NOT NULL,
    MAIL VARCHAR(70) NOT NULL,
    PASSWORD VARCHAR(150) NOT NULL,
    ID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS wishlist (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
);

INSERT INTO products (id, item, image, price) VALUES
(2, 'Легкая обувь для бега (Серые)', 'product-11.jpg', '5000 ₽'),
(3, 'Обувь с плоской шнуровкой', 'product-10.jpg', '4800₽'),
(4, 'Легкая обувь для бега (Черные)', 'product-2.jpg', '4950₽'),
(5, 'Высокие кроссовки(Серые)', 'product-5.jpg', '5500₽'),
(6, 'Женские кроссовки на высокой подошве(Белые)', 'product-13.jpg', '3400₽'),
(7, 'Высокие носки унисекс(Белые,черные,серые) 5шт', 'product-7.jpg', '500₽');

";

if ($conn->multi_query($sql)) {
    echo "База и таблицы успешно созданы";
} else {
    echo "Ошибка: " . $conn->error;
}

$conn->close();

?>
