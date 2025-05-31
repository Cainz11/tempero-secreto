-- Remover usuário admin se existir
DELETE FROM users WHERE username = 'admin' OR email = 'admin@tempero-secreto.com.br';

-- Criar usuário admin novo com senha: admin123
INSERT INTO users (username, email, password, full_name, is_admin) VALUES 
('admin', 'admin@tempero-secreto.com.br', '$2y$10$YCJnZHJhc2VkX3Bhc3N3b.7bVz0nLSswhY0q9LnXZohaP8XyL4oFm', 'Administrador', 1); 