-- Ensure events table has all required columns
-- Run this SQL in your Supabase SQL Editor to fix schema issues

-- Add photo_url column if it doesn't exist
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'events' 
        AND column_name = 'photo_url'
    ) THEN
        ALTER TABLE events ADD COLUMN photo_url VARCHAR(500);
        RAISE NOTICE 'Added photo_url column';
    ELSE
        RAISE NOTICE 'photo_url column already exists';
    END IF;
END $$;

-- Verify all expected columns exist
SELECT 
    column_name, 
    data_type, 
    character_maximum_length,
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_schema = 'public'
AND table_name = 'events'
ORDER BY ordinal_position;

-- Expected columns:
-- id (UUID)
-- title (VARCHAR(255))
-- description (TEXT)
-- event_date (DATE)
-- event_time (TIME)
-- location (VARCHAR(255))
-- photo_url (VARCHAR(500)) - nullable
-- max_participants (INTEGER) - nullable
-- event_status (VARCHAR(50))
-- created_by (UUID) - nullable, foreign key
-- created_at (TIMESTAMP WITH TIME ZONE)
-- updated_at (TIMESTAMP WITH TIME ZONE)
