-- Add google_id column to users table in Supabase
-- Run this SQL in Supabase SQL Editor

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS google_id TEXT NULL;

-- Verify the column was added
-- SELECT column_name, data_type, is_nullable 
-- FROM information_schema.columns 
-- WHERE table_name = 'users' AND column_name = 'google_id';
