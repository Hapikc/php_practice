-- Создание базы данных
CREATE
DATABASE IF NOT EXISTS mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE
mvc;

-- Таблица ролей пользователей
CREATE TABLE roles
(
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    name    VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- Таблица подразделений
CREATE TABLE departments
(
    department_id INT PRIMARY KEY AUTO_INCREMENT,
    name          VARCHAR(255) NOT NULL,
    type          VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Таблица пользователей
CREATE TABLE users
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    surname       VARCHAR(100) NOT NULL,
    patronymic    VARCHAR(100),
    birth_date    DATE,
    login         VARCHAR(100) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    role_id       INT          NOT NULL,
    department_id INT,
    avatar        VARCHAR(255),
    FOREIGN KEY (role_id) REFERENCES roles (role_id),
    FOREIGN KEY (department_id) REFERENCES departments (department_id)
) ENGINE=InnoDB;

-- Таблица помещений
CREATE TABLE rooms
(
    room_id       INT PRIMARY KEY AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    type          VARCHAR(100) NOT NULL,
    department_id INT          NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments (department_id)
) ENGINE=InnoDB;

-- Таблица телефонов
CREATE TABLE phones
(
    phone_id INT PRIMARY KEY AUTO_INCREMENT,
    number   VARCHAR(20) NOT NULL,
    room_id  INT         NOT NULL,
    user_id  INT,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB;

-- Вставка начальных данных
INSERT INTO roles (role_id, name)
VALUES (1, 'Администратор'),
       (2, 'Системный администратор'),
       (3, 'Обычный пользователь');

INSERT INTO departments (department_id, name, type)
VALUES (1, 'IT отдел', 'Технический'),
       (2, 'Отдел кадров', 'Административный'),
       (3, 'Бухгалтерия', 'Финансовый');

INSERT INTO users (name, surname, patronymic, birth_date, login, password, role_id, department_id)
VALUES ('Иван', 'Иванов', 'Иванович', '1985-05-15', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1), -- пароль: admin
       ('Петр', 'Петров', 'Петрович', '1990-08-20', 'sysadmin', 'e00cf25ad42683b3df678c61f42c6bda', 2,
        1),                                                                                             -- пароль: sysadmin

INSERT INTO rooms (name, type, department_id)
VALUES
    ('Кабинет 101', 'Офис', 1), ('Кабинет 102', 'Офис', 1), ('Кабинет 201', 'Переговорная', 2), ('Кабинет 301', 'Офис', 3);

INSERT INTO phones (number, room_id, user_id)
VALUES ('1001', 1, 1),
       ('1002', 1, 2),
       ('2001', 3, 3),
       ('3001', 4, 4);

ALTER TABLE users DROP FOREIGN KEY users_ibfk_1;
ALTER TABLE users DROP FOREIGN KEY users_ibfk_2;

ALTER TABLE users
    ADD CONSTRAINT users_ibfk_1
        FOREIGN KEY (role_id) REFERENCES roles (role_id) ON DELETE CASCADE;

ALTER TABLE users
    ADD CONSTRAINT users_ibfk_2
        FOREIGN KEY (department_id) REFERENCES departments (department_id) ON DELETE SET NULL;

ALTER TABLE rooms DROP FOREIGN KEY rooms_ibfk_1;
ALTER TABLE rooms
    ADD CONSTRAINT rooms_ibfk_1
        FOREIGN KEY (department_id) REFERENCES departments (department_id) ON DELETE CASCADE;

ALTER TABLE phones DROP FOREIGN KEY phones_ibfk_1;
ALTER TABLE phones DROP FOREIGN KEY phones_ibfk_2;

ALTER TABLE phones
    ADD CONSTRAINT phones_ibfk_1
        FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE CASCADE;

ALTER TABLE phones
    ADD CONSTRAINT phones_ibfk_2
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL;
