-- Receitas para Entrada
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Bruschetta Italiana', 'Tradicional entrada italiana com tomates frescos e manjericão', '- 1 baguete\n- 4 tomates maduros\n- 2 dentes de alho\n- Manjericão fresco\n- Azeite de oliva\n- Sal e pimenta a gosto', '1. Corte a baguete em fatias\n2. Torre as fatias\n3. Esfregue alho nas fatias\n4. Misture os tomates picados com manjericão\n5. Tempere com azeite, sal e pimenta\n6. Coloque sobre as fatias', 20, 4, 'Fácil', 'bruschetta.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Entrada'), 'approved'),

('Carpaccio de Carne', 'Finas fatias de carne crua com molho especial', '- 400g de filé mignon\n- Rúcula\n- Parmesão em lascas\n- Alcaparras\n- Azeite\n- Mostarda Dijon\n- Limão', '1. Congele parcialmente a carne\n2. Fatie bem fino\n3. Prepare o molho com mostarda e limão\n4. Monte com rúcula e parmesão\n5. Finalize com alcaparras', 30, 4, 'Médio', 'carpaccio.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Entrada'), 'approved'),

('Bolinhos de Bacalhau', 'Crocantes bolinhos portugueses de bacalhau', '- 500g de bacalhau dessalgado\n- 500g de batatas\n- 3 ovos\n- Salsa picada\n- Cebola\n- Alho\n- Óleo para fritar', '1. Cozinhe o bacalhau e desfie\n2. Cozinhe e amasse as batatas\n3. Misture com ovos e temperos\n4. Forme bolinhos\n5. Frite até dourar', 60, 6, 'Médio', 'bolinhos_bacalhau.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Entrada'), 'approved');

-- Receitas para Prato Principal
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Strogonoff de Frango', 'Cremoso strogonoff de frango com champignons', '- 1kg de peito de frango\n- 2 caixas de creme de leite\n- Champignons\n- Cebola e alho\n- Mostarda\n- Ketchup\n- Batata palha', '1. Corte o frango em cubos\n2. Refogue com cebola e alho\n3. Adicione champignons\n4. Acrescente creme de leite e molhos\n5. Sirva com batata palha', 40, 6, 'Fácil', 'strogonoff.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Prato Principal'), 'approved'),

('Lasanha à Bolonhesa', 'Clássica lasanha com molho bolonhesa e bechamel', '- Massa de lasanha\n- Carne moída\n- Molho de tomate\n- Molho bechamel\n- Queijo mussarela\n- Parmesão ralado', '1. Prepare o molho bolonhesa\n2. Faça o molho bechamel\n3. Monte camadas alternadas\n4. Cubra com queijo\n5. Asse até dourar', 90, 8, 'Médio', 'lasanha.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Prato Principal'), 'approved'),

('Salmão Grelhado', 'Salmão grelhado com ervas e limão', '- Filés de salmão\n- Ervas frescas\n- Limão\n- Azeite\n- Alho\n- Sal e pimenta', '1. Tempere os filés\n2. Aqueça a grelha\n3. Grelhe 5 min cada lado\n4. Regue com limão\n5. Sirva com ervas', 30, 4, 'Médio', 'salmao.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Prato Principal'), 'approved');

-- Receitas para Sobremesa
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Pudim de Leite', 'Clássico pudim de leite condensado', '- 1 lata de leite condensado\n- 1 lata de leite\n- 3 ovos\n- 1 xícara de açúcar para calda', '1. Faça a calda de açúcar\n2. Bata os ingredientes\n3. Despeje na forma\n4. Asse em banho-maria\n5. Leve à geladeira', 60, 8, 'Médio', 'pudim.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sobremesa'), 'approved'),

('Mousse de Maracujá', 'Mousse cremoso de maracujá', '- 1 lata de leite condensado\n- 1 lata de suco de maracujá\n- 1 lata de creme de leite\n- Polpa de maracujá', '1. Bata o leite condensado com suco\n2. Adicione creme de leite\n3. Despeje em taças\n4. Decore com polpa\n5. Leve à geladeira', 20, 6, 'Fácil', 'mousse_maracuja.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sobremesa'), 'approved'),

('Pavê de Chocolate', 'Pavê de chocolate com biscoitos', '- Biscoitos champagne\n- Leite\n- Chocolate em pó\n- Creme de leite\n- Leite condensado\n- Chocolate granulado', '1. Prepare o creme de chocolate\n2. Molhe os biscoitos no leite\n3. Monte camadas alternadas\n4. Decore com granulado\n5. Leve à geladeira', 40, 8, 'Médio', 'pave.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sobremesa'), 'approved');

-- Receitas para Bebidas
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Caipirinha', 'Clássica caipirinha brasileira', '- 1 limão\n- 2 colheres de açúcar\n- Cachaça\n- Gelo', '1. Corte o limão em pedaços\n2. Macere com açúcar\n3. Adicione gelo\n4. Complete com cachaça\n5. Misture bem', 5, 1, 'Fácil', 'caipirinha.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Bebidas'), 'approved'),

('Vitamina de Abacate', 'Vitamina cremosa de abacate', '- 1 abacate maduro\n- Leite\n- Açúcar\n- Gelo', '1. Bata o abacate com leite\n2. Adicione açúcar\n3. Bata novamente\n4. Sirva com gelo', 10, 2, 'Fácil', 'vitamina_abacate.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Bebidas'), 'approved'),

('Chocolate Quente', 'Chocolate quente cremoso', '- Leite\n- Chocolate em pó\n- Chocolate meio amargo\n- Açúcar\n- Canela', '1. Aqueça o leite\n2. Adicione chocolates\n3. Mexa até derreter\n4. Adoce a gosto\n5. Polvilhe canela', 15, 2, 'Fácil', 'chocolate_quente.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Bebidas'), 'approved');

-- Receitas para Lanches
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Hambúrguer Caseiro', 'Hambúrguer artesanal suculento', '- Carne moída\n- Pão de hambúrguer\n- Queijo\n- Alface e tomate\n- Cebola\n- Molhos diversos', '1. Tempere a carne\n2. Forme os hambúrgueres\n3. Grelhe a carne\n4. Monte o sanduíche\n5. Adicione molhos', 30, 4, 'Médio', 'hamburguer.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Lanches'), 'approved'),

('Pastel de Queijo', 'Pastel crocante de queijo', '- Massa de pastel\n- Queijo mussarela\n- Óleo para fritar', '1. Recheie a massa\n2. Feche bem as bordas\n3. Frite em óleo quente\n4. Escorra em papel\n5. Sirva quente', 30, 10, 'Fácil', 'pastel.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Lanches'), 'approved'),

('Misto Quente', 'Sanduíche de queijo e presunto', '- Pão de forma\n- Queijo\n- Presunto\n- Manteiga', '1. Passe manteiga no pão\n2. Monte com queijo e presunto\n3. Grelhe até dourar\n4. Corte na diagonal', 10, 1, 'Fácil', 'misto_quente.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Lanches'), 'approved');

-- Receitas para Saladas
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Salada Grega', 'Tradicional salada grega com queijo feta', '- Tomates\n- Pepino\n- Cebola roxa\n- Azeitonas pretas\n- Queijo feta\n- Azeite e orégano', '1. Corte os legumes\n2. Misture os ingredientes\n3. Adicione queijo feta\n4. Tempere com azeite\n5. Finalize com orégano', 15, 4, 'Fácil', 'salada_grega.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Saladas'), 'approved'),

('Salada de Quinoa', 'Salada nutritiva de quinoa', '- Quinoa\n- Tomate cereja\n- Pepino\n- Hortelã\n- Limão\n- Azeite', '1. Cozinhe a quinoa\n2. Corte os legumes\n3. Misture tudo\n4. Tempere com limão\n5. Adicione hortelã', 25, 4, 'Fácil', 'salada_quinoa.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Saladas'), 'approved'),

('Salada Waldorf', 'Clássica salada Waldorf', '- Maçã verde\n- Aipo\n- Nozes\n- Uvas\n- Maionese\n- Iogurte natural', '1. Corte maçã e aipo\n2. Misture com nozes e uvas\n3. Prepare o molho\n4. Misture tudo\n5. Sirva gelada', 20, 4, 'Médio', 'salada_waldorf.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Saladas'), 'approved');

-- Receitas para Sopas
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Caldo Verde', 'Tradicional sopa portuguesa', '- Batatas\n- Couve\n- Linguiça\n- Cebola e alho\n- Azeite', '1. Cozinhe as batatas\n2. Refogue os temperos\n3. Bata as batatas\n4. Adicione a couve\n5. Finalize com linguiça', 45, 6, 'Médio', 'caldo_verde.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sopas'), 'approved'),

('Sopa de Legumes', 'Sopa nutritiva de legumes', '- Cenoura\n- Batata\n- Abobrinha\n- Cebola e alho\n- Temperos', '1. Corte os legumes\n2. Refogue os temperos\n3. Cozinhe os legumes\n4. Tempere a gosto\n5. Sirva quente', 40, 4, 'Fácil', 'sopa_legumes.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sopas'), 'approved'),

('Creme de Abóbora', 'Cremoso creme de abóbora', '- Abóbora\n- Cebola e alho\n- Creme de leite\n- Gengibre\n- Temperos', '1. Cozinhe a abóbora\n2. Refogue temperos\n3. Bata tudo\n4. Adicione creme\n5. Sirva quente', 35, 4, 'Fácil', 'creme_abobora.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sopas'), 'approved');

-- Receitas para Vegetariano
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Risoto de Cogumelos', 'Cremoso risoto vegetariano', '- Arroz arbóreo\n- Cogumelos variados\n- Cebola e alho\n- Vinho branco\n- Queijo parmesão', '1. Refogue os cogumelos\n2. Prepare o risoto\n3. Adicione vinho\n4. Finalize com queijo\n5. Sirva quente', 40, 4, 'Médio', 'risoto_cogumelos.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Vegetariano'), 'approved'),

('Quiche de Espinafre', 'Quiche vegetariano de espinafre', '- Massa podre\n- Espinafre\n- Queijo\n- Ovos\n- Creme de leite', '1. Pré-asse a massa\n2. Refogue o espinafre\n3. Prepare o recheio\n4. Monte a quiche\n5. Asse até dourar', 60, 8, 'Médio', 'quiche_espinafre.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Vegetariano'), 'approved'),

('Berinjela à Parmegiana', 'Berinjela empanada com molho', '- Berinjela\n- Molho de tomate\n- Queijo mussarela\n- Parmesão\n- Farinha e ovos', '1. Empane as berinjelas\n2. Frite\n3. Monte camadas\n4. Cubra com queijo\n5. Asse no forno', 50, 6, 'Médio', 'berinjela_parmegiana.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Vegetariano'), 'approved');

-- Receitas para Vegano
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Curry de Grão de Bico', 'Curry vegano com grão de bico', '- Grão de bico\n- Leite de coco\n- Curry em pó\n- Legumes\n- Temperos', '1. Cozinhe o grão\n2. Refogue legumes\n3. Adicione curry\n4. Misture leite de coco\n5. Cozinhe até engrossar', 40, 4, 'Médio', 'curry_grao.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Vegano'), 'approved'),

('Strogonoff de Cogumelos', 'Versão vegana de strogonoff', '- Cogumelos\n- Leite de castanha\n- Mostarda\n- Cebola e alho\n- Temperos', '1. Refogue cogumelos\n2. Prepare o molho\n3. Misture tudo\n4. Cozinhe até cremoso\n5. Sirva com arroz', 30, 4, 'Fácil', 'strogonoff_cogumelos.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Vegano'), 'approved'),

('Bolo de Cenoura Vegano', 'Bolo sem ingredientes animais', '- Cenouras\n- Farinha\n- Óleo\n- Açúcar\n- Fermento', '1. Bata cenoura e óleo\n2. Misture secos\n3. Forme a massa\n4. Asse\n5. Cubra com ganache', 45, 8, 'Fácil', 'bolo_cenoura_vegano.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Vegano'), 'approved');

-- Receitas para Sem Glúten
INSERT INTO recipes (title, description, ingredients, instructions, preparation_time, servings, difficulty, image_url, user_id, category_id, status) VALUES
('Pão de Queijo', 'Tradicional pão de queijo mineiro', '- Polvilho\n- Queijo\n- Óleo\n- Ovos\n- Leite', '1. Escalde o polvilho\n2. Adicione ingredientes\n3. Sove a massa\n4. Forme bolinhas\n5. Asse até dourar', 40, 20, 'Médio', 'pao_queijo.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Glúten'), 'approved'),

('Bolo de Amendoim', 'Bolo sem glúten de amendoim', '- Amendoim\n- Farinha de arroz\n- Ovos\n- Açúcar\n- Fermento', '1. Triture amendoim\n2. Bata os ovos\n3. Misture ingredientes\n4. Asse a massa\n5. Decore com amendoim', 50, 8, 'Médio', 'bolo_amendoim.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Glúten'), 'approved'),

('Brownie de Chocolate', 'Brownie sem glúten', '- Chocolate meio amargo\n- Manteiga\n- Ovos\n- Açúcar\n- Farinha de amêndoas', '1. Derreta chocolate\n2. Misture ingredientes\n3. Prepare a massa\n4. Asse\n5. Corte em quadrados', 45, 12, 'Médio', 'brownie_chocolate.jpg', (SELECT id FROM users WHERE username = 'admin'), (SELECT id FROM categories WHERE name = 'Sem Glúten'), 'approved'); 