-- Update Supabase `messages` table to match Laravel messaging needs.
-- Run this in Supabase SQL Editor.
--
-- Current Laravel message fields:
-- - sender_id (int locally, UUID in Supabase)
-- - receiver_id (int locally, UUID in Supabase)
-- - subject (nullable)
-- - message (required)
-- - read_at (nullable)
--
-- NOTE:
-- Your Laravel UI uses accessors `sanitized_subject` / `sanitized_message`
-- derived from the stored `subject` / `message`, so no extra columns are required
-- unless you want to persist sanitized versions.

-- Ensure required columns exist (safe/no-op if already present)
alter table public.messages
  add column if not exists subject varchar(255),
  add column if not exists read_at timestamptz;

-- Helpful indexes (safe/no-op if already present)
create index if not exists idx_messages_sender_id on public.messages(sender_id);
create index if not exists idx_messages_receiver_id on public.messages(receiver_id);
create index if not exists idx_messages_read_at on public.messages(read_at);
create index if not exists idx_messages_created_at on public.messages(created_at);

