-- Ensure Supabase `public.members` matches the fields used by Laravel's DatabaseQueryService::upsertMember().
-- Safe to run multiple times (uses IF NOT EXISTS where supported).

-- 1) Add missing columns
ALTER TABLE public.members
  ADD COLUMN IF NOT EXISTS email text,
  ADD COLUMN IF NOT EXISTS phone text,
  ADD COLUMN IF NOT EXISTS address text,
  ADD COLUMN IF NOT EXISTS skills text,
  ADD COLUMN IF NOT EXISTS availability text,
  ADD COLUMN IF NOT EXISTS emergency_contact_name text,
  ADD COLUMN IF NOT EXISTS emergency_contact_phone text,
  ADD COLUMN IF NOT EXISTS member_status text DEFAULT 'active',
  ADD COLUMN IF NOT EXISTS photo_url text,
  ADD COLUMN IF NOT EXISTS "order" integer DEFAULT 0,
  ADD COLUMN IF NOT EXISTS created_at timestamptz DEFAULT now(),
  ADD COLUMN IF NOT EXISTS updated_at timestamptz DEFAULT now();

-- 2) Constraints (best-effort, idempotent)
DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_constraint WHERE conname = 'members_member_status_check'
  ) THEN
    ALTER TABLE public.members
      ADD CONSTRAINT members_member_status_check
      CHECK (member_status IN ('active','inactive'));
  END IF;
END $$;

-- 3) Helpful indexes
CREATE INDEX IF NOT EXISTS members_order_idx ON public.members ("order");
CREATE INDEX IF NOT EXISTS members_created_at_idx ON public.members (created_at DESC);
CREATE INDEX IF NOT EXISTS members_role_idx ON public.members (role);
CREATE UNIQUE INDEX IF NOT EXISTS members_email_unique_idx ON public.members (email) WHERE email IS NOT NULL;

-- 4) updated_at trigger (matches other tables in this repo)
CREATE OR REPLACE FUNCTION public.set_updated_at()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM pg_trigger
    WHERE tgname = 'set_members_updated_at'
  ) THEN
    CREATE TRIGGER set_members_updated_at
    BEFORE UPDATE ON public.members
    FOR EACH ROW
    EXECUTE FUNCTION public.set_updated_at();
  END IF;
END $$;

