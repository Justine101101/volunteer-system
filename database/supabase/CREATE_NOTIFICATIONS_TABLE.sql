-- Create notifications table in Supabase (in-app notifications)
-- Run this SQL in your Supabase SQL Editor

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE IF NOT EXISTS public.notifications (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    user_id UUID REFERENCES public.users(id) ON DELETE CASCADE,
    type VARCHAR(100) NOT NULL, -- e.g. registration.approved, registration.rejected, announcement
    title VARCHAR(255) NOT NULL,
    body TEXT,
    metadata JSONB,
    read_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_notifications_user_id_created_at ON public.notifications(user_id, created_at DESC);
CREATE INDEX IF NOT EXISTS idx_notifications_read_at ON public.notifications(read_at);
CREATE INDEX IF NOT EXISTS idx_notifications_type ON public.notifications(type);

-- RLS: this table is managed by the backend using the service role key.
ALTER TABLE public.notifications ENABLE ROW LEVEL SECURITY;
-- No policies added intentionally.

-- updated_at trigger (safe standalone)
CREATE OR REPLACE FUNCTION public.update_notifications_updated_at()
RETURNS trigger AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

DROP TRIGGER IF EXISTS update_notifications_updated_at ON public.notifications;
CREATE TRIGGER update_notifications_updated_at
BEFORE UPDATE ON public.notifications
FOR EACH ROW EXECUTE FUNCTION public.update_notifications_updated_at();

