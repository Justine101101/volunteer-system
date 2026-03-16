# Step-by-Step Guide: Adding Google OAuth Support to Supabase

## Overview
This guide will help you add the `google_id` column to your Supabase `users` table to support Google OAuth authentication.

---

## Step 1: Add `google_id` Column to Supabase Users Table

### Option A: Using Supabase Dashboard (Recommended)

1. **Go to Supabase Dashboard**
   - Navigate to: https://app.supabase.com/
   - Select your project

2. **Open Table Editor**
   - Click on **"Table Editor"** in the left sidebar
   - Find and click on the **`users`** table

3. **Add New Column**
   - Click the **"Add Column"** button (usually at the top or bottom of the table)
   - Or click the **"+"** icon next to existing columns

4. **Configure the Column**
   - **Column Name:** `google_id`
   - **Type:** `text` or `varchar(255)`
   - **Nullable:** ✅ **Yes** (check this box - it's important!)
   - **Default Value:** Leave empty
   - **Is Unique:** ❌ No (unchecked)
   - **Is Primary Key:** ❌ No (unchecked)

5. **Save the Column**
   - Click **"Save"** or **"Add Column"** button

---

### Option B: Using SQL Editor (Alternative)

1. **Go to Supabase Dashboard**
   - Navigate to: https://app.supabase.com/
   - Select your project

2. **Open SQL Editor**
   - Click on **"SQL Editor"** in the left sidebar
   - Click **"New Query"**

3. **Run the SQL Command**
   ```sql
   ALTER TABLE users 
   ADD COLUMN google_id TEXT NULL;
   ```

4. **Execute the Query**
   - Click the **"Run"** button (or press `Ctrl+Enter` / `Cmd+Enter`)
   - You should see a success message: "Success. No rows returned"

---

## Step 2: Verify the Column Was Added

1. **Go back to Table Editor**
   - Click on **"Table Editor"** → **`users`** table

2. **Check the Column**
   - You should now see `google_id` column in the table
   - It should be marked as **nullable** (can be NULL)

---

## Step 3: Update DatabaseQueryService (Code Update)

The code needs to be updated to sync `google_id` to Supabase. This has been done automatically, but verify:

**File:** `app/Services/DatabaseQueryService.php`

**Method:** `upsertUser()`

The `google_id` field should be included in the payload when syncing users to Supabase.

---

## Step 4: Test Google OAuth

1. **Make sure your `.env` file has Google OAuth credentials:**
   ```env
   GOOGLE_CLIENT_ID=your-client-id-here
   GOOGLE_CLIENT_SECRET=your-client-secret-here
   GOOGLE_REDIRECT_URI=http://your-domain.test/auth/google/callback
   ```

2. **Test the Login Flow:**
   - Go to your login page
   - Click "Continue with Google"
   - Complete Google authentication
   - Check if user is created/updated in Supabase with `google_id`

3. **Verify in Supabase:**
   - Go to Table Editor → `users` table
   - Find the user who logged in with Google
   - Check that `google_id` column has a value (Google user ID)

---

## Troubleshooting

### Error: "Column google_id does not exist"
- **Solution:** Make sure you added the column in Step 1
- Verify the column name is exactly `google_id` (lowercase, with underscore)

### Error: "Column google_id cannot be null"
- **Solution:** Make sure the column is set to **nullable** (allows NULL values)
- Some users may not have Google accounts, so NULL is expected

### Users not syncing to Supabase
- **Solution:** Check Laravel logs: `storage/logs/laravel.log`
- Verify Supabase credentials in `.env` file
- Check that `upsertUser()` method includes `google_id` in the payload

---

## Summary

✅ **What you need to do:**
1. Add `google_id` column to Supabase `users` table (nullable text/varchar)
2. Verify the column exists
3. Test Google OAuth login

✅ **What the code does automatically:**
- Stores `google_id` in Laravel database
- Syncs `google_id` to Supabase when creating/updating users
- Links existing users by email if they log in with Google later

---

## Additional Notes

- The `google_id` column stores Google's unique user identifier
- Users can have both email/password AND Google login (linked by email)
- If a user logs in with Google and their email already exists, the accounts will be linked
- The `google_id` is optional - users without Google accounts will have NULL
