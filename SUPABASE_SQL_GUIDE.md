# How to Run SQL in Supabase - Step by Step Guide

## Quick Steps

1. **Go to Supabase Dashboard**
   - Visit: https://supabase.com
   - Sign in to your account
   - Select your project

2. **Open SQL Editor**
   - Look for "SQL Editor" in the left sidebar menu
   - Click on it

3. **Create New Query**
   - Click the "New query" button (usually at the top)
   - Or click the "+" icon to create a new SQL query

4. **Paste This SQL Code:**
   ```sql
   ALTER TABLE events 
   ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500);
   ```

5. **Run the Query**
   - Click the "Run" button (usually green, at the bottom right)
   - Or press `Ctrl + Enter` (Windows) or `Cmd + Enter` (Mac)
   - Wait for the success message

6. **Verify It Worked**
   - You should see a success message
   - The column has been added!

## After Adding the Column

Run this command in your terminal to update existing events:

```bash
cd volunteer-portal
php artisan events:update-photo-urls
```

This will automatically match event photos in storage with events in Supabase and update them.

## Troubleshooting

**If you see an error:**
- Make sure you're in the correct project
- Check that the `events` table exists
- The error might say the column already exists - that's okay! It means it's already there.

**If you can't find SQL Editor:**
- Look for "SQL" or "Query" in the sidebar
- It might be under "Database" or "Tools" menu

## What This Does

This SQL command adds a new column called `photo_url` to your `events` table. This column will store the path/URL to event photos so they can be displayed on your website.
