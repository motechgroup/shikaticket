# Ticket Type Scanner Feature

## Overview
The scanner now identifies and displays ticket types (VIP, Regular, Early Bird, etc.) to help with guest direction, seating arrangements, and attendance classification.

## Features Added

### 1. Database Changes
- Added `tier` field to `tickets` table to store ticket type
- Added `tier` field to `order_items` table to track ticket tier during checkout
- Migrations: `2025_09_24_0018_add_tier_to_tickets.sql` and `2025_09_24_0019_add_tier_to_order_items.sql`

### 2. Scanner Enhancements
- **Enhanced Ticket Verification**: Scanner now returns ticket type information
- **Visual Display**: Shows ticket type in scan popup (VIP Ticket, Regular Ticket, etc.)
- **Last Scan Info**: Displays last scanned ticket type on scanner interface
- **Event Context**: Shows event title and venue information

### 3. Admin Features
- **Scan History**: Admin can view scan history with ticket types
- **Ticket Classification**: Easy identification of VIP vs Regular attendees

## How It Works

### 1. Ticket Purchase
When a user selects a ticket tier (VIP, Regular, Early Bird, etc.) during checkout:
- The tier information is stored in `order_items.tier`
- When payment is confirmed, tickets are generated with the tier information

### 2. Ticket Scanning
When a ticket is scanned:
- Scanner looks up ticket with tier information
- Returns formatted response with ticket type
- Displays ticket type in popup and interface

### 3. Guest Management
Event staff can now:
- **Direct VIP guests** to premium areas
- **Classify seating arrangements** based on ticket type
- **Track attendance by category** for analytics

## Usage Examples

### Scanner Interface
```
✅ Confirmed
VIP Ticket
Event: Summer Music Festival
Venue: Central Park
```

### Admin Dashboard
| Ticket Code | Event | Ticket Type | Organizer | Redeemed At |
|-------------|-------|-------------|-----------|-------------|
| 123456 | Summer Music Festival | VIP | John Doe | 2025-01-24 14:30:00 |
| 789012 | Summer Music Festival | Regular | John Doe | 2025-01-24 14:25:00 |

## Benefits

1. **Improved Guest Experience**: Clear direction for different ticket holders
2. **Better Event Management**: Easy identification of VIP vs Regular attendees
3. **Analytics**: Track attendance patterns by ticket type
4. **Seating Arrangements**: Staff can direct guests to appropriate areas
5. **Revenue Insights**: Understand which ticket types are most popular

## Technical Implementation

### Database Schema
```sql
-- Tickets table now includes tier
ALTER TABLE tickets ADD COLUMN tier VARCHAR(50) NOT NULL DEFAULT 'regular';

-- Order items store tier during checkout
ALTER TABLE order_items ADD COLUMN tier VARCHAR(50) NOT NULL DEFAULT 'regular';
```

### API Response
```json
{
  "ok": true,
  "msg": "Ticket valid & redeemed",
  "ticket_type": "VIP",
  "event_title": "Summer Music Festival",
  "venue": "Central Park"
}
```

## Migration Instructions

1. Run the database migrations:
   ```sql
   -- Run these in order
   SOURCE database/migrations/2025_09_24_0018_add_tier_to_tickets.sql;
   SOURCE database/migrations/2025_09_24_0019_add_tier_to_order_items.sql;
   ```

2. Existing tickets will default to 'regular' tier
3. New tickets will automatically include tier information
4. Scanner will immediately start showing ticket types

## Future Enhancements

- **Color-coded badges** for different ticket types
- **Audio announcements** for VIP ticket scans
- **Seating zone recommendations** based on ticket type
- **Analytics dashboard** showing ticket type distribution
