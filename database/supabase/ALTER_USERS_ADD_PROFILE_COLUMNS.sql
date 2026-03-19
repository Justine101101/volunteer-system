-- Add missing profile columns to Supabase `users` table.
-- Run this in Supabase SQL Editor.
--
-- The Laravel app expects these optional columns to exist:
-- - phone
-- - google_id
-- - photo_url
-- and will send them (nullable) during upsert.

ALTER TABLE public.users
  ADD COLUMN IF NOT EXISTS phone varchar(50),
  ADD COLUMN IF NOT EXISTS google_id varchar(255),
  ADD COLUMN IF NOT EXISTS photo_url varchar(500);

