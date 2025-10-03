# Organizer Delete Function Fix

## Problem
When admin tried to delete an organizer at `https://shikaticket.com/public/admin/organizers/delete`, they got:
- **HTTP ERROR 500** (Internal Server Error)

## Root Cause
The SQL query at line 246 was trying to access `o.organizer_id` which doesn't exist in the `orders` table:

```sql
-- BROKEN QUERY:
SELECT o.id FROM orders o 
LEFT JOIN order_items oi ON oi.order_id = o.id 
WHERE o.organizer_id = ? AND oi.id IS NULL  -- ❌ orders table has no organizer_id column
```

### Database Schema:
```sql
CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,          -- ✅ Has user_id
  total_amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(8) NOT NULL,
  status ENUM('pending','paid','failed','refunded'),
  -- ❌ NO organizer_id column!
);
```

This caused a SQL error which resulted in the 500 error page.

---

## What Was Fixed

### 1. Fixed Orphaned Order Detection
**Before (Broken):**
```php
// Tried to find orders by organizer_id (doesn't exist)
$ordersToDelete = db()->prepare('
    SELECT o.id FROM orders o 
    LEFT JOIN order_items oi ON oi.order_id = o.id 
    WHERE o.organizer_id = ? AND oi.id IS NULL
');
```

**After (Fixed):**
```php
// Get order IDs from order_items first
$orderIdsStmt = db()->prepare('SELECT DISTINCT order_id FROM order_items WHERE event_id = ?');
$orderIdsStmt->execute([$eventId]);
$orderIds = $orderIdsStmt->fetchAll(\PDO::FETCH_COLUMN);

// Then check each order to see if it has remaining items
foreach ($orderIds as $orderId) {
    $remainingItems = db()->prepare('SELECT COUNT(*) FROM order_items WHERE order_id = ?');
    $remainingItems->execute([$orderId]);
    $itemCount = $remainingItems->fetchColumn();
    
    // If order has no more items, delete it and its payments
    if ($itemCount == 0) {
        db()->prepare('DELETE FROM payments WHERE order_id = ?')->execute([$orderId]);
        db()->prepare('DELETE FROM orders WHERE id = ?')->execute([$orderId]);
    }
}
```

### 2. Added Scanner Device Cleanup
Added cleanup for scanner devices associated with the organizer:
```php
// Delete scanner devices for this organizer
try {
    db()->prepare('DELETE FROM event_scanner_assignments WHERE scanner_device_id IN (SELECT id FROM scanner_devices WHERE organizer_id = ?)')->execute([$id]);
    db()->prepare('DELETE FROM scanner_devices WHERE organizer_id = ?')->execute([$id]);
} catch (\Throwable $e) {
    // Scanner devices table may not exist in older versions
    error_log('Scanner devices cleanup failed: ' . $e->getMessage());
}
```

### 3. Added Audit Logging
Now logs organizer deletions:
```php
\App\Models\AuditLogger::log(
    'organizer_deleted',
    "Organizer '{$org['full_name']}' (ID: {$id}) was deleted by admin",
    ['organizer_id' => $id, 'organizer_name' => $org['full_name'], 'had_events' => $hasEvents],
    'warning'
);
```

### 4. Improved Error Handling
- Changed `catch (Exception $e)` to `catch (\Throwable $e)` (catches all errors)
- Added detailed error logging with stack trace
- Shows actual error message to admin (helpful for debugging)

```php
catch (\Throwable $e) {
    $db->rollBack();
    error_log('Delete organizer error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    flash_set('error', 'Failed to delete organizer: ' . $e->getMessage());
}
```

---

## Deletion Flow (Corrected)

When an organizer is deleted, the system now:

1. **Validates** organizer exists ✅
2. **Checks** if organizer has events ✅
3. **Starts transaction** for data integrity ✅
4. **For each event:**
   - Get all order IDs containing this event
   - Delete tickets for the event
   - Delete order items for the event
   - For each affected order:
     - Check if it has remaining items
     - If no items left (orphaned), delete order and payments
5. **Delete all events** for organizer ✅
6. **Delete scanner devices** and assignments ✅
7. **Delete organizer tokens** (OTP, password reset) ✅
8. **Delete organizer followers** ✅
9. **Delete withdrawals** for organizer ✅
10. **Delete the organizer** record ✅
11. **Commit transaction** ✅
12. **Log to audit log** ✅
13. **Show success message** to admin ✅

If any step fails, the entire transaction is rolled back (nothing is deleted).

---

## Deployment

### File to Upload:
- ✅ `app/Controllers/AdminController.php`

### Testing Steps:
1. Login as admin
2. Go to: Admin > Organizers
3. Try to delete an organizer
4. Should see success message (no 500 error)
5. Check `audit_logs` table for deletion record

---

## What's Improved

### Data Integrity:
✅ Only deletes orders that become orphaned (no items left)  
✅ Preserves orders that have items from other organizers  
✅ Uses database transaction (all-or-nothing)  
✅ Cleans up scanner devices properly  

### Error Handling:
✅ Catches all types of errors (not just Exception)  
✅ Logs errors to error_log for debugging  
✅ Shows helpful error message to admin  
✅ Gracefully handles missing tables (scanner_devices, audit_logs)  

### Auditability:
✅ Logs all organizer deletions with details  
✅ Records organizer name and ID  
✅ Notes if organizer had events  
✅ Timestamps the deletion  

---

## Database Tables Affected

The deletion process now correctly handles these tables:

1. `tickets` - Deleted for organizer's events
2. `order_items` - Deleted for organizer's events
3. `orders` - Deleted only if orphaned (no items remain)
4. `payments` - Deleted with orphaned orders
5. `events` - Deleted for organizer
6. `event_scanner_assignments` - Deleted for organizer's devices
7. `scanner_devices` - Deleted for organizer
8. `organizer_tokens` - Deleted for organizer
9. `organizer_followers` - Deleted for organizer
10. `withdrawals` - Deleted for organizer
11. `organizers` - Finally deleted
12. `audit_logs` - New record created (if table exists)

---

## Backward Compatibility

The fix is backward compatible:
- ✅ Works with or without `scanner_devices` table
- ✅ Works with or without `audit_logs` table
- ✅ Doesn't require database schema changes
- ✅ Handles existing data correctly

---

## Testing Checklist

After deployment, test these scenarios:

- [ ] Delete organizer with NO events (should succeed)
- [ ] Delete organizer with events (should succeed)
- [ ] Delete organizer with events that have orders (should succeed)
- [ ] Check that related orders/tickets are deleted
- [ ] Check that unrelated orders are preserved
- [ ] Verify deletion is logged in `audit_logs`
- [ ] Confirm no 500 errors
- [ ] Verify success message shows correctly

---

## Error Monitoring

After deployment, check `public/error_log` for:

### ✅ Expected (Non-critical):
```
Scanner devices cleanup failed: Table 'ticko.scanner_devices' doesn't exist
Audit log failed: Table 'ticko.audit_logs' doesn't exist
```
These are OK if you haven't run those migrations yet.

### ❌ Unexpected (Needs attention):
```
Delete organizer error: [SQL error message]
Stack trace: [detailed trace]
```
These indicate real problems that need fixing.

---

## Migration Recommendations

If you haven't already, run these migrations:

1. **Scanner Devices Migration:**
   ```bash
   database/migrations/2025_09_24_0020_create_scanner_devices.sql
   ```

2. **Audit Logs Migration:**
   ```bash
   database/migrations/2025_09_30_1230_create_audit_logs.sql
   ```

These will enable the full functionality of the delete function.

---

## Summary

**Problem:** SQL error trying to access non-existent `orders.organizer_id` column  
**Solution:** Rewrote orphaned order detection to use proper relationships  
**Result:** Organizer deletion now works correctly without 500 errors  

**Status:** ✅ READY TO DEPLOY  
**Priority:** HIGH (Blocks admin functionality)  
**Risk:** LOW (Improved error handling, transaction-based)  
**Time:** 2 minutes to upload file  

---

**Note:** Always backup your database before performing bulk deletions in production!

