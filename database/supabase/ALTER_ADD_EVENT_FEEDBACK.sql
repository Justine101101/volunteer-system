-- Create event feedback table (one rating per user per event)
-- Run this in Supabase SQL editor using a service role / admin connection.

create table if not exists public.event_feedback (
  id uuid primary key default gen_random_uuid(),
  event_id uuid not null references public.events(id) on delete cascade,
  user_id uuid not null references public.users(id) on delete cascade,
  rating integer not null check (rating between 1 and 5),
  comment text null,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  unique (event_id, user_id)
);

-- Helpful index for event summary queries
create index if not exists event_feedback_event_id_idx on public.event_feedback (event_id);

-- Optional: simple trigger to keep updated_at fresh (if you already have one, skip)
do $$
begin
  if not exists (
    select 1
    from pg_trigger
    where tgname = 'set_event_feedback_updated_at'
  ) then
    create or replace function public.set_updated_at()
    returns trigger as $fn$
    begin
      new.updated_at = now();
      return new;
    end;
    $fn$ language plpgsql;

    create trigger set_event_feedback_updated_at
    before update on public.event_feedback
    for each row execute function public.set_updated_at();
  end if;
end $$;

