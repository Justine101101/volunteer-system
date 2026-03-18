# Step-by-Step Guide: Adding President Role to Supabase

## Overview
The "president" role has been added to your Laravel application. Supabase typically uses a `text` or `varchar` column for roles (not an enum), so it should automatically accept the "president" value. However, you may want to verify this.

---

## Step 1: Verify Supabase Users Table Role Column

### Option A: Check Current Column Type (Recommended)

1. **Go to Supabase Dashboard**
   - Navigate to: https://app.supabase.com/
   - Select your project

2. **Open SQL Editor**
   - Click on **"SQL Editor"** in the left sidebar
   - Click **"New Query"**

3. **Check the Role Column Type**
   ```sql
   SELECT column_name, data_type, character_maximum_length
   FROM information_schema.columns
   WHERE table_name = 'users' AND column_name = 'role';
   ```

4. **Expected Result:**
   - If `data_type` is `text` or `character varying` (varchar), you're good! ✅
   - The "president" role will work automatically
   - No changes needed

---

## Step 2: If Role Column is ENUM (Rare Case)

If your Supabase `role` column is an ENUM type (unlikely but possible), you'll need to update it:

### Using SQL Editor:

1. **Go to SQL Editor** → **New Query**

2. **Run this SQL to change ENUM to TEXT:**
   ```sql
   -- First, alter the column to text type
   ALTER TABLE users 
   ALTER COLUMN role TYPE TEXT;
   ```

3. **Verify the change:**
   ```sql
   SELECT column_name, data_type
   FROM information_schema.columns
   WHERE table_name = 'users' AND column_name = 'role';
   ```

---

## Step 3: Test the President Role

1. **Create a test user with president role:**
   - Go to Admin → Manage Users → Add New User
   - Select "President" from the role dropdown
   - Create the user

2. **Verify in Supabase:**
   - Go to Table Editor → `users` table
   - Find the user you just created
   - Check that `role` column shows "president"

---

## Summary

✅ **What you need to do:**
1. Verify the `role` column in Supabase is `text` or `varchar` (not enum)
2. If it's already text/varchar, you're done! ✅
3. If it's enum, run the SQL to change it to text

✅ **What the code does automatically:**
- Adds "president" to Laravel database enum
- Includes "president" in role validation
- Shows "president" in role dropdowns
- Grants president admin-level access (can access admin dashboard)
- Syncs president role to Supabase when creating/updating users

---

## Role Hierarchy

- **Admin** - Highest level access
- **Admin** - Full admin access
- **President** - Admin-level access (treated like admin)
- **Volunteer** - Standard user access

---

## Additional Notes

- President role has the same access as Admin (can access admin dashboard)
- President role appears in user management with a purple badge
- President role is included in admin statistics count
- No Supabase changes needed if role column is already text/varchar type
