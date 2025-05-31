-- Drop existing columns if they exist
ALTER TABLE notifications 
DROP COLUMN IF EXISTS recipe_id,
DROP COLUMN IF EXISTS related_user_id;

-- Add columns and foreign keys
ALTER TABLE notifications 
ADD COLUMN recipe_id INT NULL,
ADD COLUMN related_user_id INT NULL;

-- Add foreign keys
ALTER TABLE notifications
ADD CONSTRAINT fk_notifications_recipe
FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE SET NULL;

ALTER TABLE notifications
ADD CONSTRAINT fk_notifications_user
FOREIGN KEY (related_user_id) REFERENCES users(id) ON DELETE SET NULL; 