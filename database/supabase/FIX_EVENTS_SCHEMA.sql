-- ============================================
-- FIX EVENTS TABLE SCHEMA
-- Run this in Supabase SQL Editor
-- ============================================

-- Step 1: Add photo_url column if missing
ALTER TABLE events 
ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500);

-- Step 2: Verify the table structure
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

-- Expected output should show these columns:
-- ✅ id (uuid)
-- ✅ title (character varying)
-- ✅ description (text)
-- ✅ event_date (date)
-- ✅ event_time (time without time zone)
-- ✅ location (character varying)
-- ✅ photo_url (character varying) ← This might be missing
-- ✅ max_participants (integer)
-- ✅ event_status (character varying)
-- ✅ created_by (uuid)
-- ✅ created_at (timestamp with time zone)
-- ✅ updated_at (timestamp with time zone)

-- If photo_url is missing, the ALTER TABLE above will add it.
-- After running this, try creating an event again.
