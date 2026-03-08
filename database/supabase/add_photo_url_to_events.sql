-- Add photo_url column to events table if it doesn't exist
-- Run this SQL in your Supabase SQL Editor

ALTER TABLE events 
ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500);

-- Add a comment to document the column
COMMENT ON COLUMN events.photo_url IS 'URL or path to the event photo image';
