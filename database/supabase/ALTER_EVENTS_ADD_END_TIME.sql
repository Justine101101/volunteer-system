-- ============================================
-- ADD END TIME SUPPORT FOR EVENTS
-- Run this in Supabase SQL Editor
-- ============================================

-- Add an end-time column (nullable so existing events still work)
ALTER TABLE events
ADD COLUMN IF NOT EXISTS event_end_time TIME;

-- Optional: helpful comment
COMMENT ON COLUMN events.event_end_time IS 'Event end time (TIME).';

