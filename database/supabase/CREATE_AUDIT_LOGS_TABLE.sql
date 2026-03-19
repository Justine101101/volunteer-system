-- Supabase table for audit logs (mirror from local SQLite).
-- Run this SQL in Supabase SQL Editor.
--
-- Design notes:
-- - `local_audit_log_id` lets us re-run sync safely (idempotent upsert).
-- - `user_id` is optional and references Supabase `users(id)` when we can map by email.
-- - `local_user_id` and `user_email` are preserved for traceability even if no Supabase user exists.

create extension if not exists "uuid-ossp";

create table if not exists public.audit_logs (
  id uuid primary key default uuid_generate_v4(),
  local_audit_log_id bigint unique not null,

  -- Mapping fields
  user_id uuid null references public.users(id) on delete set null,
  local_user_id bigint null,
  user_email varchar(255) null,

  -- Audit data
  action varchar(100) not null,
  resource_type varchar(150) not null,
  resource_id varchar(191) null,
  payload jsonb null,

  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create index if not exists idx_audit_logs_action on public.audit_logs(action);
create index if not exists idx_audit_logs_created_at on public.audit_logs(created_at);
create index if not exists idx_audit_logs_resource on public.audit_logs(resource_type, resource_id);
create index if not exists idx_audit_logs_user_id on public.audit_logs(user_id);

-- updated_at trigger
create or replace function public.update_audit_logs_updated_at()
returns trigger as $$
begin
  new.updated_at = now();
  return new;
end;
$$ language 'plpgsql';

drop trigger if exists update_audit_logs_updated_at on public.audit_logs;
create trigger update_audit_logs_updated_at
before update on public.audit_logs
for each row execute function public.update_audit_logs_updated_at();

