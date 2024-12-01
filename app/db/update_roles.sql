-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('admin', 'business_owner') NOT NULL DEFAULT 'business_owner';

-- Add unique constraint to businesses table to ensure one business per user
ALTER TABLE businesses ADD CONSTRAINT unique_business_per_user UNIQUE (user_id);

-- Update existing admin user (replace 1 with your actual admin user ID)
UPDATE users SET role = 'admin' WHERE id = 1;

-- Add foreign key to ensure business exists when creating menu
ALTER TABLE menus ADD CONSTRAINT fk_business_menu FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE;
