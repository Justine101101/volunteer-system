-- Add onsite attendance columns to event_registrations (idempotent).
-- Run in Supabase SQL Editor if your project was created before these columns existed.

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'public'
          AND table_name = 'event_registrations'
          AND column_name = 'attended_at'
    ) THEN
        ALTER TABLE event_registrations
            ADD COLUMN attended_at TIMESTAMP WITH TIME ZONE;
        RAISE NOTICE 'Added attended_at column';
    ELSE
        RAISE NOTICE 'attended_at column already exists';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'public'
          AND table_name = 'event_registrations'
          AND column_name = 'attendance_marked_by'
    ) THEN
        ALTER TABLE event_registrations
            ADD COLUMN attendance_marked_by UUID REFERENCES users(id) ON DELETE SET NULL;
        RAISE NOTICE 'Added attendance_marked_by column';
    ELSE
        RAISE NOTICE 'attendance_marked_by column already exists';
    END IF;
END $$;
