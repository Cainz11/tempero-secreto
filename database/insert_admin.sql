-- Inserir usuário admin
INSERT INTO users (username, email, password, full_name, is_admin)
VALUES (
    'admin',
    'admin@tempero-secreto.com.br',
    '$2y$10$8tqwXP3HXVjBkNJ3.M8VO.GPVmhLIXd5qBwAFXtQJrqNAIgRZIyIO', -- senha: admin123
    'Administrador',
    1
); 