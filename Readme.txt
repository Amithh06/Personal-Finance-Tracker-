create database name cinematic_login
table users= (accepted users)

CREATE TABLE users (
    userid VARCHAR(50) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'active') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_expense (
    userid VARCHAR(50) NOT NULL,  -- Match the datatype with the users table's userid
    expense_name VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    month TEXT DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (userid, expense_name),
    FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE
);

