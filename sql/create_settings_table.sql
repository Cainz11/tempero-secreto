CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir configurações padrão
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Tempero Secreto'),
('site_description', 'Compartilhe suas receitas favoritas com a comunidade'),
('maintenance_mode', '0'),
('recipes_per_page', '12'),
('allow_comments', '1'),
('require_approval', '1')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value); 