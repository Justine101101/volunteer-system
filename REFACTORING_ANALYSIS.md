# Single Source of Truth Refactoring Analysis

## Current State: Dual-Write Pattern

### Overview
The application currently implements a dual-write pattern where data is written to both:
1. **Local MySQL Database** (via Eloquent models)
2. **Supabase PostgreSQL** (via DatabaseQueryService)

### Dual-Write Locations

#### 1. EventController
- **MySQL Write**: `Event::create()` and `$event->update()` (lines 97, 171)
- **Supabase Write**: `$this->queryService->createEvent()` and `updateEvent()` (lines 108, 184)
- **Issue**: ID mismatch (MySQL integers vs Supabase UUIDs) causes update/delete failures

#### 2. RegisteredUserController
- **MySQL Write**: `User::create()` (line 44)
- **Supabase Write**: `$this->queryService->upsertUser()` (line 52)
- **Issue**: Password not synced (Supabase uses placeholder)

#### 3. ContactController
- **MySQL Write**: `Contact::create()` (line 27)
- **Supabase Write**: `$this->queryService->upsertContact()` (line 34)

#### 4. SettingsController
- **MySQL Write**: `$user->save()` (line 44)
- **Supabase Write**: `$queryService->upsertUser()` (line 47)

### Read Operations

Most controllers read from **MySQL only**:
- `Event::orderBy()->get()` (EventController)
- `Member::orderBy()->get()` (MemberController)
- `EventRegistration::where()->first()` (EventRegistrationController)
- Dashboard queries use MySQL directly

### Problems with Current Approach

1. **Data Inconsistency**: Data can get out of sync between MySQL and Supabase
2. **ID Mismatch**: MySQL uses integer IDs, Supabase uses UUIDs - updates/deletes fail silently
3. **Silent Failures**: Supabase operations are "best-effort" and log errors but don't throw exceptions
4. **No Single Source of Truth**: Unclear which database is authoritative
5. **Maintenance Burden**: Two code paths to maintain
6. **Performance**: Unnecessary duplicate writes

## Refactoring Strategy: Supabase as Single Source of Truth

### Phase 1: Analysis & Planning ✅
- [x] Review DatabaseQueryService.php
- [x] Identify all dual-write locations
- [x] Document current read operations
- [x] Create refactoring plan

### Phase 2: Refactor Write Operations
1. **EventController**
   - Remove `Event::create()` and `$event->update()`
   - Use only `DatabaseQueryService` methods
   - Handle Supabase responses properly

2. **RegisteredUserController**
   - Remove `User::create()`
   - Use only `DatabaseQueryService->upsertUser()`
   - Handle authentication with Supabase user IDs

3. **ContactController**
   - Remove `Contact::create()`
   - Use only `DatabaseQueryService->upsertContact()`

4. **SettingsController**
   - Remove `$user->save()`
   - Use only `DatabaseQueryService->upsertUser()`

5. **EventRegistrationController**
   - Remove `EventRegistration::create()` and `update()`
   - Add methods to `DatabaseQueryService` for registrations
   - Use only Supabase operations

6. **MemberController**
   - Add `upsertMember()` calls to `DatabaseQueryService`
   - Remove direct MySQL writes

### Phase 3: Refactor Read Operations
1. Update all controllers to read from Supabase via `DatabaseQueryService`
2. Replace Eloquent model queries with `DatabaseQueryService` methods
3. Update views to handle Supabase response format

### Phase 4: DatabaseQueryService Enhancements
1. Add missing methods:
   - `getMembers()` with pagination
   - `updateMember()`
   - `deleteMember()`
   - `getEventRegistrations()` (enhance existing)
   - `deleteEventRegistration()`

2. Improve error handling:
   - Return consistent response format
   - Throw exceptions for critical failures
   - Better logging

### Phase 5: Model Updates
1. Option A: Keep models for relationships/validation only (read-only)
2. Option B: Remove models entirely and use DTOs/arrays
3. Update authentication to work with Supabase user IDs

### Phase 6: Testing & Migration
1. Test all CRUD operations
2. Verify data consistency
3. Create migration script to sync existing MySQL data to Supabase
4. Update documentation

## Recommended Approach

### Supabase as Primary Database
- **Pros**:
  - Already has comprehensive DatabaseQueryService
  - Better scalability
  - Row Level Security (RLS) policies
  - Real-time capabilities
  - Better suited for production

- **Cons**:
  - Requires network calls (can be slower)
  - More complex error handling
  - Need to refactor authentication

### Implementation Notes

1. **ID Handling**: Supabase uses UUIDs, so we need to:
   - Store Supabase IDs in responses
   - Update route model binding to work with UUIDs
   - Or maintain a mapping table

2. **Authentication**: Laravel Auth uses MySQL users table. Options:
   - Keep MySQL users for auth only
   - Migrate to Supabase Auth
   - Hybrid approach

3. **Relationships**: Eloquent relationships won't work with Supabase. Need to:
   - Use `DatabaseQueryService` joins
   - Handle relationships in service layer
   - Use Supabase foreign key queries

## Next Steps

1. Start with EventController (most complex)
2. Add missing methods to DatabaseQueryService
3. Refactor one controller at a time
4. Test thoroughly before moving to next
5. Update documentation as we go
