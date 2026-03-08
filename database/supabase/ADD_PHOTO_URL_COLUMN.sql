-- Add photo_url column to events table in Supabase
-- Run this in your Supabase SQL Editor

ALTER TABLE events 
ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500);

-- Verify the column was added
SELECT column_name, data_type, character_maximum_length 
FROM information_schema.columns 
WHERE table_name = 'events' AND column_name = 'photo_url';
