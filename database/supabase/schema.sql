-- Supabase Database Schema for Volunteer Portal
-- Run this SQL in your Supabase SQL Editor

-- Enable necessary extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Drop existing tables if they exist (to avoid conflicts)
DROP TABLE IF EXISTS messages CASCADE;
DROP TABLE IF EXISTS settings CASCADE;
DROP TABLE IF EXISTS members CASCADE;
DROP TABLE IF EXISTS contacts CASCADE;
DROP TABLE IF EXISTS event_registrations CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- Drop existing functions if they exist
DROP FUNCTION IF EXISTS get_event_stats(UUID);
DROP FUNCTION IF EXISTS get_user_dashboard(UUID);
DROP FUNCTION IF EXISTS update_updated_at_column();

-- Create users table
CREATE TABLE users (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'volunteer' CHECK (role IN ('superadmin', 'admin', 'volunteer')),
    notification_pref BOOLEAN DEFAULT true,
    dark_mode BOOLEAN DEFAULT false,
    email_verified_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create events table
CREATE TABLE events (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    max_participants INTEGER,
    event_status VARCHAR(50) DEFAULT 'active' CHECK (event_status IN ('active', 'inactive', 'cancelled')),
    created_by UUID REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create event_registrations table
CREATE TABLE event_registrations (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    event_id UUID REFERENCES events(id) ON DELETE CASCADE,
    registration_status VARCHAR(50) DEFAULT 'pending' CHECK (registration_status IN ('pending', 'approved', 'rejected', 'cancelled')),
    notes TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(user_id, event_id)
);

-- Create contacts table
CREATE TABLE contacts (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    contact_status VARCHAR(50) DEFAULT 'new' CHECK (contact_status IN ('new', 'read', 'replied', 'closed')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create members table
CREATE TABLE members (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    address TEXT,
    skills TEXT[],
    availability TEXT,
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(50),
    member_status VARCHAR(50) DEFAULT 'active' CHECK (member_status IN ('active', 'inactive', 'suspended')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create settings table
CREATE TABLE settings (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    setting_key VARCHAR(255) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'string' CHECK (setting_type IN ('string', 'number', 'boolean', 'json')),
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create messages table
CREATE TABLE messages (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    sender_id UUID REFERENCES users(id) ON DELETE CASCADE,
    receiver_id UUID REFERENCES users(id) ON DELETE CASCADE,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    read_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create indexes for better performance
CREATE INDEX idx_events_date ON events(event_date);
CREATE INDEX idx_events_status ON events(event_status);
CREATE INDEX idx_events_created_by ON events(created_by);
CREATE INDEX idx_event_registrations_user_id ON event_registrations(user_id);
CREATE INDEX idx_event_registrations_event_id ON event_registrations(event_id);
CREATE INDEX idx_event_registrations_status ON event_registrations(registration_status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_contacts_status ON contacts(contact_status);
CREATE INDEX idx_members_status ON members(member_status);
CREATE INDEX idx_messages_sender_id ON messages(sender_id);
CREATE INDEX idx_messages_receiver_id ON messages(receiver_id);
CREATE INDEX idx_messages_read_at ON messages(read_at);

-- Create updated_at trigger function
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_events_updated_at BEFORE UPDATE ON events
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_event_registrations_updated_at BEFORE UPDATE ON event_registrations
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_contacts_updated_at BEFORE UPDATE ON contacts
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_members_updated_at BEFORE UPDATE ON members
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_settings_updated_at BEFORE UPDATE ON settings
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_messages_updated_at BEFORE UPDATE ON messages
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Enable Row Level Security (RLS)
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE events ENABLE ROW LEVEL SECURITY;
ALTER TABLE event_registrations ENABLE ROW LEVEL SECURITY;
ALTER TABLE contacts ENABLE ROW LEVEL SECURITY;
ALTER TABLE members ENABLE ROW LEVEL SECURITY;
ALTER TABLE settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE messages ENABLE ROW LEVEL SECURITY;

-- Create RLS policies
-- Users can view their own data and admins can view all
-- Note: 'admin' and 'superadmin' are treated as equivalent
CREATE POLICY "Users can view own data" ON users
    FOR SELECT USING (auth.uid() = id OR auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

CREATE POLICY "Users can update own data" ON users
    FOR UPDATE USING (auth.uid() = id);

-- Events are public for viewing, only admins can modify
CREATE POLICY "Events are viewable by all" ON events
    FOR SELECT USING (true);

CREATE POLICY "Only admins can modify events" ON events
    FOR ALL USING (auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

-- Event registrations
CREATE POLICY "Users can view own registrations" ON event_registrations
    FOR SELECT USING (auth.uid() = user_id OR auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

CREATE POLICY "Users can create own registrations" ON event_registrations
    FOR INSERT WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own registrations" ON event_registrations
    FOR UPDATE USING (auth.uid() = user_id);

CREATE POLICY "Admins can manage all registrations" ON event_registrations
    FOR ALL USING (auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

-- Contacts are public for creation, admin for viewing
CREATE POLICY "Anyone can create contacts" ON contacts
    FOR INSERT WITH CHECK (true);

CREATE POLICY "Only admins can view contacts" ON contacts
    FOR SELECT USING (auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

-- Members
CREATE POLICY "Members are viewable by all authenticated users" ON members
    FOR SELECT USING (auth.role() = 'authenticated');

CREATE POLICY "Only admins can modify members" ON members
    FOR ALL USING (auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

-- Settings
CREATE POLICY "Settings are viewable by all authenticated users" ON settings
    FOR SELECT USING (auth.role() = 'authenticated');

CREATE POLICY "Only admins can modify settings" ON settings
    FOR ALL USING (auth.jwt() ->> 'role' = 'superadmin' OR auth.jwt() ->> 'role' = 'admin');

-- Messages
CREATE POLICY "Users can view own messages" ON messages
    FOR SELECT USING (auth.uid() = sender_id OR auth.uid() = receiver_id);

CREATE POLICY "Users can send messages" ON messages
    FOR INSERT WITH CHECK (auth.uid() = sender_id);

CREATE POLICY "Users can update own messages" ON messages
    FOR UPDATE USING (auth.uid() = sender_id);

CREATE POLICY "Users can mark messages as read" ON messages
    FOR UPDATE USING (auth.uid() = receiver_id);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Volunteer Portal', 'string', 'Name of the volunteer portal'),
('site_description', 'A platform for managing volunteer events and registrations', 'string', 'Description of the volunteer portal'),
('max_event_participants', '100', 'number', 'Maximum number of participants per event'),
('registration_approval_required', 'true', 'boolean', 'Whether event registrations require approval'),
('email_notifications_enabled', 'true', 'boolean', 'Whether email notifications are enabled'),
('maintenance_mode', 'false', 'boolean', 'Whether the site is in maintenance mode')
ON CONFLICT (setting_key) DO NOTHING;

-- Create a function to get event statistics
CREATE OR REPLACE FUNCTION get_event_stats(event_id UUID)
RETURNS JSON AS $$
DECLARE
    result JSON;
BEGIN
    SELECT json_build_object(
        'total_registrations', COUNT(*),
        'pending_registrations', COUNT(*) FILTER (WHERE registration_status = 'pending'),
        'approved_registrations', COUNT(*) FILTER (WHERE registration_status = 'approved'),
        'rejected_registrations', COUNT(*) FILTER (WHERE registration_status = 'rejected')
    ) INTO result
    FROM event_registrations
    WHERE event_registrations.event_id = get_event_stats.event_id;
    
    RETURN result;
END;
$$ LANGUAGE plpgsql;

-- Create a function to get user dashboard data
CREATE OR REPLACE FUNCTION get_user_dashboard(user_id UUID)
RETURNS JSON AS $$
DECLARE
    result JSON;
BEGIN
    SELECT json_build_object(
        'total_events_registered', COUNT(*),
        'upcoming_events', COUNT(*) FILTER (WHERE e.event_date >= CURRENT_DATE),
        'past_events', COUNT(*) FILTER (WHERE e.event_date < CURRENT_DATE),
        'pending_approvals', COUNT(*) FILTER (WHERE er.registration_status = 'pending')
    ) INTO result
    FROM event_registrations er
    JOIN events e ON er.event_id = e.id
    WHERE er.user_id = get_user_dashboard.user_id;
    
    RETURN result;
END;
$$ LANGUAGE plpgsql;
