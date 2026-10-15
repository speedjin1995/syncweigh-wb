# Plan: Vehicle Entry Limit — `limit_date` → `start_date` / `end_date`

## Context

Today the Planned Schedule Excel import expands every row's STARTDATE–ENDDATE into **one `Vehicle_Entry_Limit` row per day** (`PlannedScheduleService::saveDailyLimits`). A 1-year schedule for 200 vehicles is about 73,000 rows, the list shows one line per day, and re-imports touch every day.

Goal: store **one row per Excel range** (`start_date`, `end_date`). The weighing screen looks up the row whose range covers the weighing date.

Decisions (confirmed):
- **Limit stays per day.** `max_entry` = trips allowed on each day inside the range (Excel column "JUMLAH TRIP KE PS PER HARI"). Counting in `Vehicle_Entry_Log` stays per `entry_date`, so the "used today" message is unchanged.
- **Overlap rule: newest row wins.** If the exact same range already exists for the vehicle, update its `max_entry`. Otherwise insert a new row. The lookup picks the latest active row (`ORDER BY id DESC`) that covers the date.
- **Remove the Entry Limit modal from `vehicle.php`.** Planned Schedule becomes the only way to set date limits.

---

## 1. Database — `SQL/deploy.sql` (append at the end)

```sql
-- Vehicle_Entry_Limit: single date -> date range
ALTER TABLE `Vehicle_Entry_Limit`
  ADD COLUMN IF NOT EXISTS `start_date` date NULL AFTER `veh_number`,
  ADD COLUMN IF NOT EXISTS `end_date`   date NULL AFTER `start_date`;
UPDATE `Vehicle_Entry_Limit` SET start_date = limit_date, end_date = limit_date WHERE start_date IS NULL;
ALTER TABLE `Vehicle_Entry_Limit`
  MODIFY `start_date` date NOT NULL, MODIFY `end_date` date NOT NULL,
  DROP INDEX IF EXISTS `idx_vehicle_limit_date`,
  DROP COLUMN IF EXISTS `limit_date`,
  ADD KEY `idx_vehicle_range` (`vehicle_id`, `start_date`, `end_date`);

-- Same for the log table (history kept through the backfill)
ALTER TABLE `Vehicle_Entry_Limit_Log`
  ADD COLUMN IF NOT EXISTS `start_date` date NULL AFTER `veh_number`,
  ADD COLUMN IF NOT EXISTS `end_date`   date NULL AFTER `start_date`;
UPDATE `Vehicle_Entry_Limit_Log` SET start_date = limit_date, end_date = limit_date WHERE start_date IS NULL;
ALTER TABLE `Vehicle_Entry_Limit_Log` DROP COLUMN IF EXISTS `limit_date`;
```

- Recreate `TRG_INS_VEH_ENTRY_LIMIT` and `TRG_UPD_VEH_ENTRY_LIMIT` (`CREATE OR REPLACE TRIGGER`) with `start_date, end_date` in place of `limit_date`. **Build them from the live trigger text** (`SHOW CREATE TRIGGER ...` on `u245956566_alamflora`), not from the copy at deploy.sql:1470-1500, because local DBs have drifted.
- Run the trigger recreate **in the same deploy step** as the column drop. Otherwise every insert/update fails while the old trigger still references `limit_date`.
- Re-run note: the `UPDATE ... limit_date` lines fail once `limit_date` is gone. On a DB that is already migrated, skip this block. A guarded procedure isn't worth it for one migration.
- Existing per-day rows become 1-day ranges. They are not merged (see Skipped).
- `Vehicle_Entry_Log` is **not** changed. It stays per day.
- New label: none needed. `start_date_code` / `end_date_code` already exist (deploy.sql:1740-1741).

## 2. Weighing lookup — `php/modules/vehicle/vehicleEntry.php`

Only `getVehicleDateLimitRow()` changes (line 13):

```php
"SELECT id, max_entry FROM Vehicle_Entry_Limit WHERE vehicle_id = ? AND start_date <= ? AND end_date >= ? AND status = '0' ORDER BY id DESC LIMIT 1"
// bind_param('sss', $vehicleId, $date, $date)
```

- Update the header comment (line 3) to say "active row whose range covers the date".
- Everything downstream keeps working without other changes: `getVehicleEntryLimit` → `getVehicleEntryStatus` → `php/weight.php:576` (save + override) and `php/checkVehicleEntry.php:38` (weighing screen pre-check popup). The fallback to `Vehicle.max_entry_per_day` stays.
- `index.php` needs no change, because it only consumes `checkVehicleEntry.php` output (`max` / `count`).

## 3. Import — `php/services/PlannedScheduleService.php`

- Replace `saveDailyLimits()` (lines 241-264) with `saveRangeLimit($vehicleId, $plate, $startDate, $endDate, $trips)`:
  - `fetchOne("SELECT id, max_entry FROM Vehicle_Entry_Limit WHERE vehicle_id = ? AND start_date = ? AND end_date = ? AND status = '0' ORDER BY id DESC LIMIT 1")`
  - none → `INSERT (vehicle_id, veh_number, start_date, end_date, max_entry, created_by, modified_by)`, return 1
  - exists with a different `max_entry` → `UPDATE ... SET max_entry, modified_by`, return 1
  - same → return 0
- The 366-day cap check (line 139) now only exists to catch Excel typos. Keep it unless asked to remove it.
- `importRows()` (line 157): call the new method. Rename the `days` counter → `ranges` in the return value **and** in the controller/JS message that prints it (check `PlannedScheduleController` and the upload success handler in `plannedSchedule.php`).
- Remove `require_once ... vehicleEntry.php` (line 3) if nothing else in the service uses it after the change.

## 4. List — `PlannedScheduleService::getAll()` + `plannedSchedule.php`

- Date filter becomes **range overlap**:
  - no filter: `l.end_date >= today` (ranges still running or upcoming)
  - fromDate: `l.end_date >= fromDate`; toDate: `l.start_date <= toDate`
- SELECT / ORDER BY: `l.start_date, l.end_date ... ORDER BY l.start_date, v.veh_number`. Output `start_date` and `end_date` as `d/m/Y`.
- `plannedSchedule.php:186` header: replace the `limit_date_code` `<th>` with two `<th>` using `start_date_code` and `end_date_code`.
- `plannedSchedule.php:383` columns: `{ data: 'start_date' }, { data: 'end_date' }`.
- Overlapping rows will show side by side in the list. That matches newest-wins, and the higher `id` is the one in force.

## 5. Remove Vehicle Entry Limit modal — `vehicle.php`

Delete:
- the `#entryLimitModal` markup (~lines 202-280)
- the "Entry Limit" dropdown item in the action column render (~line 497)
- vars `entryLimitDatePicker`, `entryLimitTable` (~433-434)
- the flatpickr init, `#submitEntryLimit` handler and `#entryLimitTable` view-log handler (~558-620)
- functions `entryLimit()`, `loadEntryLimitTable()`, `formatEntryLimitLog()`, `loadEntryLimit()` (~813-910)

Delete files (only these two callers exist, both in vehicle.php):
- `php/modules/vehicle/vehicleEntryLimit.php`
- `php/modules/vehicle/loadVehicleEntryLimit.php`

Read each range before deleting; the line numbers above are approximate.

Keep `vehicleEntry.php`, which the weighing flow uses.

## Critical files

| File | Change |
|---|---|
| `SQL/deploy.sql` | migration + triggers (append) |
| `php/modules/vehicle/vehicleEntry.php` | range lookup (1 query) |
| `php/services/PlannedScheduleService.php` | range import + list query |
| `plannedSchedule.php` | 2 date columns |
| `vehicle.php` | remove entry limit modal + JS |
| `php/modules/vehicle/vehicleEntryLimit.php`, `loadVehicleEntryLimit.php` | delete |
| `php/classes/PlannedScheduleController.php` | `days` → `ranges` in message (if it reads it) |

## Verification

1. Run the deploy block on `u245956566_alamflora`. Check `DESCRIBE Vehicle_Entry_Limit` / `_Log` and `SHOW TRIGGERS LIKE 'Vehicle_Entry_Limit'`. The old rows should have `start_date = end_date`.
2. Import the Planned Schedule Excel with one row 01/11/2026–30/11/2026, trips 2. Expect **1** new row and 1 `_Log` row with action 1. Re-import the same file: no change. Change trips to 3: same row updated, `_Log` action 2.
3. Import an overlapping row 10/11–12/11, trips 0. On the weighing screen with today's date mocked (or a range that includes today): on 11/11 the vehicle is blocked and the popup asks for a password. On 13/11 the 2-trip limit applies.
4. Weighing: with trips 2 for today, the 3rd weighing triggers the "Insufficient entry (2/2 used today)" popup. The override saves, and `Vehicle_Entry_Log` stays one row per weighing.
5. Planned Schedule list: no filter shows ranges ending today or later. A from/to filter shows the overlapping ranges.
6. Vehicle page: the action dropdown has no Entry Limit item, and the browser console shows no JS errors.

## Skipped / risks

- **Not merged:** legacy 1-day rows are not merged into ranges. They still work. Re-importing the Excel creates proper ranges, and newest wins over them.
- **`Vehicle.max_entry_per_day`:** once the modal is gone, this column has no UI. Existing values still act as the fallback limit on days without a range. If no fallback is wanted, clear it with SQL (`UPDATE Vehicle SET max_entry_per_day = NULL`).
- **No manual edit:** to fix one vehicle/day, you now re-upload an Excel row (newest wins).
