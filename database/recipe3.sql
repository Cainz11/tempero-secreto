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
    'bolo_chocolate.jpg', 6,
    (SELECT id FROM categories WHERE name = 'Sobremesa'),
    'approved'
); 