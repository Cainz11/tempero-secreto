-- Adicionar mais categorias
INSERT INTO categories (name, description) VALUES
('Comida Japonesa', 'Pratos tradicionais japoneses'),
('Comida Mineira', 'Pratos típicos de Minas Gerais'),
('Comida Nordestina', 'Sabores do Nordeste brasileiro'),
('Comida Vegana', 'Pratos sem ingredientes de origem animal'),
('Sucos e Bebidas', 'Bebidas refrescantes e saudáveis');

-- Suco Verde Detox
INSERT INTO recipes (
    title, description, ingredients, instructions, 
    preparation_time, servings, difficulty, 
    image_url, user_id, category_id, status
) VALUES (
    'Suco Verde Detox',
    'Um suco refrescante e saudável, perfeito para começar o dia com energia.',
    '- 2 folhas de couve
- 1 maçã verde
- 1 pepino
- Suco de 1 limão
- 1 pedaço de gengibre
- 200ml de água de coco
- Hortelã a gosto',
    '1. Lave bem todos os ingredientes
2. Corte a maçã e o pepino em pedaços
3. Bata todos os ingredientes no liquidificador
4. Coe e sirva imediatamente
5. Se desejar, adicione gelo',
    10, 2, 'Fácil',
    'suco_verde.jpg', 6,
    (SELECT id FROM categories WHERE name = 'Sucos e Bebidas'),
    'approved'
);

-- Yakisoba Vegano
INSERT INTO recipes (
    title, description, ingredients, instructions, 
    preparation_time, servings, difficulty, 
    image_url, user_id, category_id, status
) VALUES (
    'Yakisoba Vegano',
    'Uma versão vegana do clássico prato japonês, repleto de legumes e sabor.',
    '- 500g de macarrão para yakisoba
- 2 cenouras em julienne
- 2 brócolis em floretes
- 1 cebola em fatias
- 200g de cogumelos shiitake
- 200g de tofu firme em cubos
- Molho:
  - 1/2 xícara de molho shoyu
  - 2 colheres de óleo de gergelim
  - 1 colher de gengibre ralado
  - 2 dentes de alho
  - Pimenta a gosto',
    '1. Prepare o molho misturando todos os ingredientes
2. Frite o tofu até dourar
3. Refogue os legumes mantendo-os crocantes
4. Cozinhe o macarrão conforme instruções
5. Misture tudo com o molho
6. Finalize com cebolinha picada',
    40, 4, 'Médio',
    'yakisoba_vegano.jpg', 6,
    (SELECT id FROM categories WHERE name = 'Comida Vegana'),
    'approved'
);

-- Baião de Dois
INSERT INTO recipes (
    title, description, ingredients, instructions, 
    preparation_time, servings, difficulty, 
    image_url, user_id, category_id, status
) VALUES (
    'Baião de Dois Tradicional',
    'O clássico prato nordestino que une arroz e feijão de corda com queijo coalho.',
    '- 2 xícaras de arroz
- 2 xícaras de feijão de corda cozido
- 200g de queijo coalho em cubos
- 2 cebolas picadas
- 4 dentes de alho
- Cheiro verde a gosto
- Coentro a gosto
- Sal e pimenta a gosto
- Manteiga de garrafa',
    '1. Refogue a cebola e o alho na manteiga de garrafa
2. Adicione o arroz e refogue
3. Acrescente o feijão de corda cozido
4. Adicione o queijo coalho
5. Finalize com cheiro verde e coentro
6. Sirva com manteiga de garrafa adicional',
    45, 6, 'Médio',
    'baiao_dois.jpg', 6,
    (SELECT id FROM categories WHERE name = 'Comida Nordestina'),
    'approved'
); 