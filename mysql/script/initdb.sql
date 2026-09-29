ALTER USER 'user1'@'%' IDENTIFIED WITH mysql_native_password BY 'user1password';

CREATE DATABASE IF NOT EXISTS testdb;
use testdb;
CREATE TABLE testdb.test (
    id integer,
    name varchar(30)
);
INSERT INTO testdb.test VALUES(1,'hoge');

-- SHOW GRANTS FOR user1@'%';
-- GRANT ALL PRIVILEGES ON `testdb`.* TO `user1`@`%`;