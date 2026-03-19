-- Update Supabase `contacts` table to match Laravel contact form + admin needs.
-- Run this in Supabase SQL Editor.
--
-- Laravel app sends:
-- - name, email, message, subject
-- Supabase schema may also include:
-- - phone, contact_status

-- Add missing columns safely (no-op if they already exist)
alter table contacts
  add column if not exists phone varchar(50),
  add column if not exists subject varchar(255),
  add column if not exists contact_status varchar(50) default 'new',
  add column if not exists created_at timestamptz default now(),
  add column if not exists updated_at timestamptz default now();

-- Ensure allowed statuses (safe if constraint already exists)
do $$
begin
  if not exists (
    select 1
    from pg_constraint
    where conname = 'contacts_contact_status_check'
  ) then
    alter table public.contacts
      add constraint contacts_contact_status_check
      check (contact_status in ('new', 'read', 'replied', 'closed'));
  end if;
end $$;

-- Helpful indexes
create index if not exists idx_contacts_status on public.contacts(contact_status);
create index if not exists idx_contacts_created_at on public.contacts(created_at);
create index if not exists idx_contacts_email on public.contacts(email);

-- updated_at trigger (safe if already present under a different name in your schema.sql)
create or replace function public.update_contacts_updated_at()
returns trigger as $$
begin
  new.updated_at = now();
  return new;
end;
$$ language 'plpgsql';

drop trigger if exists update_contacts_updated_at on public.contacts;
create trigger update_contacts_updated_at
before update on public.contacts
for each row execute function public.update_contacts_updated_at();

