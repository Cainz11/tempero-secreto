-- Receita 1: Feijoada Brasileira (Prato Principal)
INSERT INTO recipes (
    title, description, ingredients, instructions, 
    preparation_time, servings, difficulty, 
    image_url, user_id, category_id, status
) VALUES (
    'Feijoada Brasileira Tradicional',
    'Uma feijoada completa e tradicional, perfeita para reunir a família no fim de semana.',
    '- 1 kg de feijão preto
- 500g de carne seca
- 300g de lombo de porco
- 300g de costelinha de porco
- 200g de linguiça calabresa
- 2 laranjas
- 2 folhas de louro
- 4 dentes de alho
- 2 cebolas
- Sal e pimenta a gosto',
    '1. Deixe o feijão e as carnes de molho separadamente por 12 horas.
2. Cozinhe o feijão com as folhas de louro em panela de pressão por 30 minutos.
3. Em outra panela, cozinhe as carnes até ficarem macias.
4. Refogue o alho e a cebola.
5. Junte o feijão e as carnes.
6. Cozinhe por mais 30 minutos até ficar com o caldo grosso.
7. Sirva com arroz branco, couve refogada, farofa e laranja.',
    180, 8, 'Médio',
    'feijoada.jpg', 1, 
    (SELECT id FROM categories WHERE name = 'Prato Principal'),
    'approved'
);

-- Receita 2: Salada Caesar (Entrada)
INSERT INTO recipes (
    title, description, ingredients, instructions, 
    preparation_time, servings, difficulty, 
    image_url, user_id, category_id, status
) VALUES (
    'Salada Caesar Clássica',
    'Uma salada caesar autêntica com molho caseiro e croutons crocantes.',
    '- 2 corações de alface romana
- 2 peitos de frango grelhados
- 1 xícara de croutons caseiros
- 1/2 xícara de queijo parmesão ralado
Para o molho:
- 1 gema de ovo
- 1 colher de mostarda Dijon
- 2 filés de anchova
- 1 dente de alho
- Suco de 1 limão
- 1/2 xícara de azeite
- Sal e pimenta a gosto',
    '1. Prepare o molho batendo todos os ingredientes no liquidificador.
2. Lave e seque bem as folhas de alface.
3. Grelhe o frango temperado com sal e pimenta.
4. Monte a salada: alface, frango fatiado, croutons.
5. Regue com o molho caesar.
6. Finalize com queijo parmesão ralado e pimenta do reino.',
    30, 4, 'Fácil',
    'salada_caesar.jpg', 1,
    (SELECT id FROM categories WHERE name = 'Entrada'),
    'approved'
);

-- Receita 3: Bolo de Chocolate (Sobremesa)
INSERT INTO recipes (
    title, description, ingredients, instructions, 
    preparation_time, servings, difficulty, 
    image_url, user_id, category_id, status
) VALUES (
    'Bolo de Chocolate Trufado',
    'Um bolo de chocolate super fofinho com cobertura de ganache.',
    'Massa:
- 4 ovos
- 2 xícaras de açúcar
- 1 xícara de óleo
- 1 xícara de chocolate em pó
- 2 xícaras de farinha de trigo
- 1 xícara de água quente
- 1 colher de fermento
Cobertura:
- 200g de chocolate meio amargo
- 200ml de creme de leite
- Chocolate granulado para decorar',
    '1. Pré-aqueça o forno a 180°C.
2. Bata os ovos com açúcar até ficar bem claro.
3. Adicione o óleo e continue batendo.
4. Misture os ingredientes secos peneirados.
5. Adicione a água quente e misture bem.
6. Coloque em forma untada.
7. Asse por 40 minutos.
Para a cobertura:
1. Derreta o chocolate em banho-maria.
2. Misture com o creme de leite.
3. Cubra o bolo depois de frio.
4. Decore com chocolate granulado.',
    60, 12, 'Médio',
    'bolo_chocolate.jpg', 1,
    (SELECT id FROM categories WHERE name = 'Sobremesa'),
    'approved'
); 