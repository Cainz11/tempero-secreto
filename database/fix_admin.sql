-- Remover usuário admin se existir
DELETE FROM users WHERE username = 'admin' OR email = 'admin@tempero-secreto.com.br';

-- Criar usuário admin novo
INSERT INTO users (username, email, password, full_name, is_admin) VALUES 
('admin', 'admin@tempero-secreto.com.br', '$2y$10$8tDjcKhUGHJxCFJ3CZgqvOsxqcyNXhbA.yF.1KQxP6XAOl4tKoYyy', 'Administrador', 1); 