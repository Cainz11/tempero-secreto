-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-utensils',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de receitas
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    ingredients TEXT,
    instructions TEXT,
    prep_time INT,
    cook_time INT,
    servings INT,
    difficulty VARCHAR(50),
    image_url VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de comentários
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de curtidas
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id),
    UNIQUE KEY unique_like (user_id, recipe_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de visualizações
CREATE TABLE views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_view (user_id, recipe_id)
);

-- Inserir usuário de exemplo
INSERT INTO users (username, email, password, full_name, is_admin) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 1);

-- Inserir categorias de exemplo
INSERT INTO categories (name, description, icon) VALUES
('Massas', 'Receitas de massas deliciosas', 'fas fa-pizza-slice'),
('Sobremesas', 'Sobremesas irresistíveis', 'fas fa-ice-cream'),
('Carnes', 'Receitas com carnes', 'fas fa-drumstick-bite'),
('Vegetariano', 'Receitas vegetarianas', 'fas fa-leaf'),
('Bebidas', 'Bebidas refrescantes', 'fas fa-glass-martini-alt');

-- Inserir receitas de exemplo
INSERT INTO recipes (user_id, category_id, title, description, ingredients, instructions, prep_time, cook_time, servings, difficulty, status) VALUES
(1, 1, 'Macarrão à Bolonhesa', 'Macarrão à bolonhesa tradicional italiano', 'Macarrão, Carne moída, Molho de tomate', 'Cozinhe o macarrão...', 20, 30, 4, 'Fácil', 'approved'),
(1, 2, 'Pudim de Leite', 'Pudim de leite condensado cremoso', 'Leite condensado, Leite, Ovos', 'Prepare a calda...', 30, 60, 8, 'Médio', 'approved'),
(1, 3, 'Bife à Parmegiana', 'Bife à parmegiana suculento', 'Carne, Queijo, Molho de tomate', 'Prepare o bife...', 40, 30, 2, 'Médio', 'approved'),
(1, 4, 'Salada Caesar', 'Salada Caesar vegetariana', 'Alface, Croutons, Molho Caesar', 'Lave a alface...', 15, 0, 2, 'Fácil', 'approved'),
(1, 5, 'Caipirinha', 'Caipirinha tradicional brasileira', 'Limão, Açúcar, Cachaça, Gelo', 'Macere o limão...', 5, 0, 1, 'Fácil', 'approved');

-- Adicionar alguns likes
INSERT INTO likes (user_id, recipe_id) VALUES
(1, 1),
(1, 2),
(1, 3); 