-- Receitas para Japonesa
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Sushi de Salmão', 'Clássico sushi japonês com salmão fresco', '- Arroz para sushi\n- Salmão fresco\n- Nori (alga)\n- Vinagre de arroz\n- Wasabi\n- Gengibre em conserva', '1. Prepare o arroz\n2. Corte o salmão\n3. Monte o sushi\n4. Enrole com alga\n5. Corte em pedaços', 60, 4, 'Difícil', 'sushi_salmao.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Japonesa'), 'approved'),

('Tempurá', 'Legumes e camarões empanados à moda japonesa', '- Camarões\n- Legumes variados\n- Farinha tempurá\n- Água gelada\n- Óleo para fritar', '1. Prepare a massa\n2. Empane os ingredientes\n3. Frite por imersão\n4. Escorra\n5. Sirva com molho', 45, 4, 'Médio', 'tempura.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Japonesa'), 'approved'),

('Ramen', 'Sopa japonesa com macarrão e caldo rico', '- Macarrão ramen\n- Caldo de porco\n- Ovo cozido\n- Broto de bambu\n- Carne de porco\n- Cebolinha', '1. Prepare o caldo\n2. Cozinhe o macarrão\n3. Monte a tigela\n4. Adicione os complementos\n5. Sirva quente', 90, 2, 'Difícil', 'ramen.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Japonesa'), 'approved');

-- Receitas para Mineira
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Feijão Tropeiro', 'Tradicional feijão tropeiro mineiro', '- Feijão\n- Farinha de mandioca\n- Bacon\n- Linguiça\n- Ovos\n- Couve', '1. Frite o bacon\n2. Adicione linguiça\n3. Misture o feijão\n4. Adicione farinha\n5. Finalize com ovos', 60, 6, 'Médio', 'feijao_tropeiro.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Mineira'), 'approved'),

('Frango com Quiabo', 'Típico prato mineiro', '- Frango em pedaços\n- Quiabo\n- Cebola e alho\n- Tomate\n- Temperos', '1. Refogue o frango\n2. Prepare o quiabo\n3. Junte os ingredientes\n4. Cozinhe até apurar\n5. Sirva quente', 50, 4, 'Médio', 'frango_quiabo.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Mineira'), 'approved'),

('Tutu de Feijão', 'Purê de feijão com farinha', '- Feijão preto\n- Farinha de mandioca\n- Bacon\n- Cebola e alho\n- Temperos', '1. Cozinhe o feijão\n2. Frite o bacon\n3. Misture a farinha\n4. Acerte o tempero\n5. Sirva quente', 45, 6, 'Fácil', 'tutu_feijao.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Mineira'), 'approved');

-- Receitas para Nordestina
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Acarajé', 'Tradicional quitute baiano', '- Feijão fradinho\n- Cebola\n- Camarão seco\n- Azeite de dendê\n- Vatapá\n- Pimenta', '1. Prepare a massa\n2. Forme as bolinhas\n3. Frite no dendê\n4. Recheie\n5. Sirva quente', 90, 8, 'Difícil', 'acaraje.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Nordestina'), 'approved'),

('Carne de Sol com Mandioca', 'Prato típico nordestino', '- Carne de sol\n- Mandioca\n- Manteiga de garrafa\n- Cebola\n- Temperos', '1. Dessalgue a carne\n2. Cozinhe a mandioca\n3. Frite a carne\n4. Junte os ingredientes\n5. Sirva quente', 60, 4, 'Médio', 'carne_sol.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Nordestina'), 'approved'),

('Tapioca Recheada', 'Tapioca com recheios variados', '- Goma de tapioca\n- Queijo coalho\n- Coco ralado\n- Manteiga\n- Leite condensado', '1. Peneire a goma\n2. Aqueça a frigideira\n3. Faça a tapioca\n4. Adicione recheio\n5. Dobre e sirva', 20, 1, 'Fácil', 'tapioca.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Nordestina'), 'approved');

-- Receitas para Sem Lactose
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Bolo de Chocolate Sem Lactose', 'Bolo fofinho sem leite', '- Farinha de trigo\n- Cacau em pó\n- Óleo vegetal\n- Leite vegetal\n- Açúcar\n- Fermento', '1. Misture os secos\n2. Adicione líquidos\n3. Bata a massa\n4. Asse\n5. Cubra com ganache', 50, 8, 'Médio', 'bolo_chocolate_sl.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Lactose'), 'approved'),

('Mousse de Maracujá Sem Lactose', 'Mousse cremoso sem leite', '- Suco de maracujá\n- Leite de coco\n- Gelatina sem sabor\n- Açúcar\n- Polpa de maracujá', '1. Hidrate a gelatina\n2. Bata o suco\n3. Misture ingredientes\n4. Leve à geladeira\n5. Decore e sirva', 30, 6, 'Fácil', 'mousse_maracuja_sl.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Lactose'), 'approved'),

('Pão Caseiro Sem Lactose', 'Pão macio sem leite', '- Farinha de trigo\n- Água morna\n- Fermento biológico\n- Açúcar\n- Sal\n- Óleo', '1. Misture os ingredientes\n2. Sove a massa\n3. Deixe crescer\n4. Modele\n5. Asse até dourar', 120, 10, 'Médio', 'pao_caseiro_sl.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Lactose'), 'approved');

-- Receitas para Sem Açúcar
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Bolo de Banana Sem Açúcar', 'Bolo natural adoçado com banana', '- Bananas maduras\n- Farinha de aveia\n- Ovos\n- Canela\n- Fermento\n- Nozes', '1. Amasse as bananas\n2. Misture ingredientes\n3. Bata a massa\n4. Asse\n5. Decore com nozes', 45, 8, 'Fácil', 'bolo_banana_sa.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Açúcar'), 'approved'),

('Sorvete de Morango Natural', 'Sorvete sem açúcar refinado', '- Morangos\n- Iogurte natural\n- Adoçante natural\n- Essência de baunilha', '1. Bata os morangos\n2. Misture ingredientes\n3. Leve ao freezer\n4. Bata novamente\n5. Congele', 240, 6, 'Médio', 'sorvete_morango_sa.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Açúcar'), 'approved'),

('Cookies de Amendoim', 'Cookies sem açúcar refinado', '- Pasta de amendoim\n- Farinha de amêndoas\n- Adoçante xilitol\n- Ovo\n- Essência de baunilha', '1. Misture ingredientes\n2. Forme cookies\n3. Asse\n4. Esfrie\n5. Sirva', 30, 12, 'Fácil', 'cookies_amendoim_sa.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Açúcar'), 'approved'); 