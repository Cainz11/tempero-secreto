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
    'feijoada.jpg', 6, 
    (SELECT id FROM categories WHERE name = 'Prato Principal'),
    'approved'
); 