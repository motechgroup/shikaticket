# Scanner Device Management System

## Overview
The enhanced scanner system allows organizers to create multiple scanner devices for their events, with admin-controlled assignments and detailed scan tracking. This improves efficiency and provides better event management capabilities.

## Key Features

### 1. Organizer Scanner Device Management
- **Create Scanner Devices**: Organizers can create multiple scanner devices (e.g., "Main Entrance", "VIP Section", "Back Gate")
- **Unique Device Codes**: Each device gets an 8-character unique code (e.g., `ABC12345`)
- **Device Status**: Enable/disable devices as needed
- **Device Management**: Edit device names, activate/deactivate devices

### 2. Admin Scanner Assignment
- **Event Assignment**: Admin assigns specific scanner devices to specific events
- **Multi-Device Support**: Multiple scanners can be assigned to one event
- **Assignment Tracking**: Track which admin assigned which device to which event
- **Assignment Management**: Unassign devices from events

### 3. Device-Based Scanner Authentication
- **Device Code Login**: Staff login using device code instead of organizer credentials
- **Event Validation**: Device can only scan tickets for assigned events
- **No Password Required**: Simplified login process for event staff
- **Session Management**: Device stays logged in during event

### 4. Enhanced Scan Tracking
- **Device Identification**: Every scan records which device performed it
- **Admin Visibility**: Admin can see which device scanned each ticket
- **Event Attribution**: Scans are tied to specific events and devices
- **Time Tracking**: Precise timestamp for each scan

## Database Schema

### New Tables

#### scanner_devices
```sql
CREATE TABLE scanner_devices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organizer_id INT UNSIGNED NOT NULL,
  device_name VARCHAR(191) NOT NULL,
  device_code VARCHAR(32) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### scanner_assignments
```sql
CREATE TABLE scanner_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  scanner_device_id INT UNSIGNED NOT NULL,
  assigned_by INT UNSIGNED NOT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);
```

#### Enhanced tickets table
```sql
ALTER TABLE tickets ADD COLUMN scanner_device_id INT UNSIGNED NULL;
```

## User Workflows

### 1. Organizer Workflow
1. **Create Scanner Devices**
   - Go to Organizer Dashboard → Scanner Devices
   - Create devices like "Main Entrance Scanner", "VIP Section Scanner"
   - Each device gets a unique code (e.g., `ABC12345`)

2. **Share Device Codes**
   - Provide device codes to event staff
   - Staff use these codes to login to scanner app

### 2. Admin Workflow
1. **Assign Scanners to Events**
   - Go to Admin Dashboard → Scanner Assignments
   - Select event and scanner device
   - Assign multiple scanners to one event if needed

2. **Monitor Scan Activity**
   - View Admin Dashboard → Scans
   - See which device scanned each ticket
   - Track event attendance by device

### 3. Event Staff Workflow
1. **Login to Scanner**
   - Go to `/scanner/login`
   - Enter device code provided by organizer
   - Device must be assigned to an active event

2. **Scan Tickets**
   - Use QR scanner or manual entry
   - Device automatically validates tickets for assigned events
   - Scan results show ticket type and event details

## Benefits

### For Organizers
- **Multiple Entry Points**: Deploy scanners at different event locations
- **Staff Management**: No need to share organizer credentials
- **Device Control**: Enable/disable devices as needed
- **Event-Specific**: Devices can be reused for multiple events

### For Event Staff
- **Simple Login**: Just device code, no passwords
- **Event-Specific**: Can only scan tickets for assigned events
- **No Logout Needed**: Device stays logged in during event
- **Clear Attribution**: Know exactly which device they're using

### For Admins
- **Complete Control**: Assign any device to any event
- **Detailed Tracking**: See which device scanned each ticket
- **Event Management**: Better understanding of entry patterns
- **Security**: Prevent unauthorized scanning

## Technical Implementation

### Authentication Flow
1. Staff enters device code at `/scanner/login`
2. System validates device code and active status
3. System checks if device is assigned to any active event
4. If valid, creates scanner session with device info
5. Scanner can only validate tickets for assigned events

### Scan Validation
1. Staff scans ticket or enters code manually
2. System looks up ticket and validates it's for assigned event
3. System records scan with device information
4. Returns ticket details including type and event info

### Database Relationships
```
Organizers → Scanner Devices → Scanner Assignments → Events
Tickets → Scanner Devices (for scan tracking)
```

## API Endpoints

### Organizer Endpoints
- `GET /organizer/scanner-devices` - List scanner devices
- `POST /organizer/scanner-devices` - Create scanner device
- `POST /organizer/scanner-devices/update` - Update scanner device
- `POST /organizer/scanner-devices/delete` - Delete scanner device

### Admin Endpoints
- `GET /admin/scanner-assignments` - View scanner assignments
- `POST /admin/scanner-assignments/assign` - Assign scanner to event
- `POST /admin/scanner-assignments/unassign` - Unassign scanner from event
- `GET /admin/scans` - View scan history with device info

### Scanner Endpoints
- `GET /scanner/login` - Scanner login form
- `POST /scanner/login` - Authenticate with device code
- `GET /scanner` - Scanner interface
- `GET/POST /scanner/verify` - Verify ticket with device tracking

## Security Features

1. **Device Code Validation**: Only active devices with valid codes can login
2. **Event Assignment**: Devices can only scan tickets for assigned events
3. **Organizer Isolation**: Devices can only be managed by their creator
4. **Admin Control**: Only admins can assign devices to events
5. **Session Security**: Device sessions are isolated from organizer sessions

## Migration Instructions

1. **Run Database Migrations**:
   ```sql
   SOURCE database/migrations/2025_09_24_0020_create_scanner_devices.sql;
   ```

2. **Update Existing System**:
   - Existing scans will show "Manual" for device tracking
   - New scans will include device information
   - Organizers can start creating scanner devices immediately

3. **Admin Setup**:
   - Assign scanner devices to events as needed
   - Monitor scan activity with new device tracking

## Future Enhancements

- **Real-time Scan Monitoring**: Live dashboard showing current scan activity
- **Device Performance Analytics**: Track scanning speed and accuracy by device
- **Geolocation Tracking**: Track where devices are being used
- **Bulk Device Assignment**: Assign multiple devices to events at once
- **Device Usage Reports**: Detailed analytics on device performance
- **Mobile App**: Dedicated mobile app for scanner devices
- **Offline Mode**: Continue scanning when internet is unavailable

## Troubleshooting

### Common Issues

1. **"Device not assigned to any event"**
   - Admin needs to assign device to an active event
   - Check Admin → Scanner Assignments

2. **"Invalid or inactive device code"**
   - Device may be disabled by organizer
   - Check Organizer → Scanner Devices

3. **"Ticket not for assigned event"**
   - Device is assigned to different event
   - Admin needs to assign device to correct event

### Best Practices

1. **Device Naming**: Use descriptive names like "Main Entrance Scanner"
2. **Event Assignment**: Assign devices before event day
3. **Staff Training**: Ensure staff understand device codes
4. **Backup Devices**: Create multiple devices for redundancy
5. **Regular Monitoring**: Check scan activity during events
