-- Atualizar o email do usuário administrador
UPDATE users 
SET email = 'admin@tempero-secreto.com.br' 
WHERE username = 'admin'; 