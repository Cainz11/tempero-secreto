ALTER TABLE notifications 
ADD COLUMN recipe_id INT NULL,
ADD COLUMN related_user_id INT NULL,
ADD FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE SET NULL,
ADD FOREIGN KEY (related_user_id) REFERENCES users(id) ON DELETE SET NULL; 