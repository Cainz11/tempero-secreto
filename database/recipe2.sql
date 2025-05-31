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
    'salada_caesar.jpg', 6,
    (SELECT id FROM categories WHERE name = 'Entrada'),
    'approved'
); 