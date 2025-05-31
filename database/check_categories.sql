SELECT c.name, COUNT(r.id) as total 
FROM categories c 
LEFT JOIN recipes r ON c.id = r.category_id 
GROUP BY c.name 
ORDER BY c.name; 