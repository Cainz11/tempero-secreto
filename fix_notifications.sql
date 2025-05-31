-- Drop existing foreign keys if they exist
SET @db = 'tempero_secreto';
SET @table = 'notifications';

SELECT CONCAT('ALTER TABLE ', @table, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
INTO @dropForeignKeys
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @db
  AND TABLE_NAME = @table
  AND REFERENCED_TABLE_NAME IS NOT NULL;

PREPARE stmt FROM @dropForeignKeys;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop existing columns if they exist
ALTER TABLE notifications 
DROP COLUMN IF EXISTS recipe_id,
DROP COLUMN IF EXISTS related_user_id;

-- Add columns and foreign keys
ALTER TABLE notifications 
ADD COLUMN recipe_id INT NULL,
ADD COLUMN related_user_id INT NULL;

ALTER TABLE notifications
ADD CONSTRAINT fk_notifications_recipe
FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE SET NULL;

ALTER TABLE notifications
ADD CONSTRAINT fk_notifications_user
FOREIGN KEY (related_user_id) REFERENCES users(id) ON DELETE SET NULL; 