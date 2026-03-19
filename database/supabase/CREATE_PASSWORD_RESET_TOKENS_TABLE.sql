-- Supabase table for Laravel password reset tokens.
-- Run this in Supabase SQL Editor.
--
-- Laravel (this project) uses: `password_reset_tokens` (see config/auth.php).
-- Local SQLite schema:
-- - email (primary key)
-- - token
-- - created_at (nullable)

create table if not exists public.password_reset_tokens (
  email varchar(255) primary key,
  token varchar(255) not null,
  created_at timestamptz null
);

create index if not exists idx_password_reset_tokens_created_at
  on public.password_reset_tokens(created_at);

