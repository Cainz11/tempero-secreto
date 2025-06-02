-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS tempero_secreto;
USE tempero_secreto;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-utensils',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de receitas
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    preparation_time INT NOT NULL COMMENT 'Tempo em minutos',
    servings INT NOT NULL,
    difficulty ENUM('Fácil', 'Médio', 'Difícil') NOT NULL,
    image_url VARCHAR(255),
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de comentários
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de favoritos
CREATE TABLE IF NOT EXISTS favorites (
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir algumas categorias padrão
INSERT INTO categories (name, description, icon) VALUES
('Entrada', 'Pratos para começar a refeição', 'fas fa-cheese'),
('Prato Principal', 'Pratos principais para sua refeição', 'fas fa-drumstick-bite'),
('Sobremesa', 'Sobremesas e doces', 'fas fa-ice-cream'),
('Bebidas', 'Bebidas e coquetéis', 'fas fa-glass-martini-alt'),
('Lanches', 'Lanches rápidos e petiscos', 'fas fa-hamburger'),
('Saladas', 'Saladas e pratos leves', 'fas fa-leaf'),
('Sopas', 'Sopas e caldos', 'fas fa-soup'),
('Vegetariano', 'Pratos vegetarianos', 'fas fa-carrot'),
('Vegano', 'Pratos veganos', 'fas fa-seedling'),
('Sem Glúten', 'Pratos sem glúten', 'fas fa-bread-slice');

-- Criar um usuário administrador padrão
-- Senha: admin123 (você deve alterar isso após o primeiro login)
INSERT INTO users (username, email, password, full_name, is_admin) VALUES
('admin', 'admin@temperosecreto.com', '$2y$10$8tDjcKhUGHJxCFJ3CZgqvOsxqcyNXhbA.yF.1KQxP6XAOl4tKoYyy', 'Administrador', 1); 