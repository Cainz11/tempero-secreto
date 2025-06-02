-- Adicionar coluna icon à tabela categories
ALTER TABLE categories ADD COLUMN IF NOT EXISTS icon VARCHAR(50) DEFAULT 'fas fa-utensils';

-- Atualizar ícones das categorias existentes
UPDATE categories SET icon = 'fas fa-cheese' WHERE name = 'Entrada';
UPDATE categories SET icon = 'fas fa-drumstick-bite' WHERE name = 'Prato Principal';
UPDATE categories SET icon = 'fas fa-ice-cream' WHERE name = 'Sobremesa';
UPDATE categories SET icon = 'fas fa-glass-martini-alt' WHERE name = 'Bebidas';
UPDATE categories SET icon = 'fas fa-hamburger' WHERE name = 'Lanches';
UPDATE categories SET icon = 'fas fa-leaf' WHERE name = 'Saladas';
UPDATE categories SET icon = 'fas fa-soup' WHERE name = 'Sopas';
UPDATE categories SET icon = 'fas fa-carrot' WHERE name = 'Vegetariano';
UPDATE categories SET icon = 'fas fa-seedling' WHERE name = 'Vegano';
UPDATE categories SET icon = 'fas fa-bread-slice' WHERE name = 'Sem Glúten'; 