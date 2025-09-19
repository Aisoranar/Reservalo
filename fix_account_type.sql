-- Corregir la columna account_type para incluir 'individual'
ALTER TABLE users MODIFY COLUMN account_type ENUM('regular', 'premium', 'business', 'individual') DEFAULT 'regular';
