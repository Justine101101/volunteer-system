-- Ensure events table has all required columns
-- Run this SQL in your Supabase SQL Editor to fix schema issues

-- Add missing columns if they don't exist
DO $$
BEGIN
    -- photo_url
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

    -- organizer
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'events' 
        AND column_name = 'organizer'
    ) THEN
        ALTER TABLE events ADD COLUMN organizer VARCHAR(255);
        RAISE NOTICE 'Added organizer column';
    ELSE
        RAISE NOTICE 'organizer column already exists';
    END IF;

    -- requirements
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'events' 
        AND column_name = 'requirements'
    ) THEN
        ALTER TABLE events ADD COLUMN requirements TEXT;
        RAISE NOTICE 'Added requirements column';
    ELSE
        RAISE NOTICE 'requirements column already exists';
    END IF;

    -- venue
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'events' 
        AND column_name = 'venue'
    ) THEN
        ALTER TABLE events ADD COLUMN venue VARCHAR(255);
        RAISE NOTICE 'Added venue column';
    ELSE
        RAISE NOTICE 'venue column already exists';
    END IF;

    -- event_end_time
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'events' 
        AND column_name = 'event_end_time'
    ) THEN
        ALTER TABLE events ADD COLUMN event_end_time TIME;
        RAISE NOTICE 'Added event_end_time column';
    ELSE
        RAISE NOTICE 'event_end_time column already exists';
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
-- event_end_time (TIME) - nullable
-- location (VARCHAR(255))
-- photo_url (VARCHAR(500)) - nullable
-- organizer (VARCHAR(255)) - nullable
-- requirements (TEXT) - nullable
-- venue (VARCHAR(255)) - nullable
-- max_participants (INTEGER) - nullable
-- event_status (VARCHAR(50))
-- created_by (UUID) - nullable, foreign key
-- created_at (TIMESTAMP WITH TIME ZONE)
-- updated_at (TIMESTAMP WITH TIME ZONE)
