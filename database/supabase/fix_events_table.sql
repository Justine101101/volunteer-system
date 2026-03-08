-- Fix events table schema - ensure all required columns exist
-- Run this SQL in your Supabase SQL Editor

-- Add photo_url column if it doesn't exist
ALTER TABLE events 
ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500);

-- Ensure all required columns exist with correct types
DO $$
BEGIN
    -- Check and add photo_url if missing
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'events' AND column_name = 'photo_url'
    ) THEN
        ALTER TABLE events ADD COLUMN photo_url VARCHAR(500);
    END IF;

    -- Verify all columns exist
    -- This will help identify any missing columns
    RAISE NOTICE 'Events table columns verified';
END $$;

-- Show current table structure for verification
SELECT 
    column_name, 
    data_type, 
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_name = 'events'
ORDER BY ordinal_position;
