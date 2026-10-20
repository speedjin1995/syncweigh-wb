# Weighing screen (index.php) regression checklist

Run after any change to the weighing screen script. Use Chrome with DevTools open; a step passes only if it behaves as described and the console shows no errors.

Run the whole list twice: once as ADMIN (or SADMIN / MANAGER) and once as an operator role. Steps marked *manager* or *operator* apply to that role only.

Unit tests for the calculations: `node --test "tests/js/*.test.js"`.

## 1. Page load
- [ ] The weighing table and empty container table load without errors.
- [ ] The four summary cards show Pending / Complete / Cancel counts.
- [ ] *manager*: the Plant filter is visible. *operator*: it is hidden.
- [ ] The indicator badge reflects the local service (blue when connected, red when not).

## 2. Search filters
- [ ] Each filter (dates, type, customer/supplier, vehicle, weight type, status, product/raw material, plant, transaction id, container no, seal no, invoice/DO/PO) narrows the list after Search.
- [ ] Type Purchase or Internal Transfer: customer and product filters are hidden and reset; supplier and raw material are shown. Other types: the reverse.
- [ ] Clear resets every filter, and the status goes back to Pending.
- [ ] Container no and seal no search fields turn the text upper case as you type.

## 3. Summary cards
- [ ] Clicking Pending, Complete or Cancel on each card sets the type and status filters and reloads the table.
- [ ] The card counts don't drop to zero for the other cards after a click.

## 4. Table rows
- [ ] A single click on a normal row expands the details; a second click collapses them.
- [ ] A single click on a "Primer Mover + Container" row does nothing.
- [ ] Double-clicking a normal row opens it for edit.
- [ ] Double-clicking a "Primer Mover + Container" row opens the empty container record.
- [ ] *manager*: rows show Edit, Print (not on container rows) and Delete.
- [ ] *operator*: incomplete rows show Weight Out; complete rows show no edit button; there is no Delete.
- [ ] Select-all checkboxes tick every row in their own table.

## 5. Add a Normal weighing
- [ ] Add New opens a clean form: type Sales, weight type Normal, today's date, the user's plant.
- [ ] Gross and tare capture buttons copy the indicator reading; nett = |gross − tare|.
- [ ] With manual weight on, typing gross or tare updates nett, current and final weight, sets "weighed by", and sets the date/time.
- [ ] A reduce weight is taken off the final weight.
- [ ] Unit price × weight, 8% SST and the total update when the weight or product changes.
- [ ] Choosing a product fills its code, description and limits.
- [ ] Choosing a supplier, customer, transporter, destination, plant or raw material fills its code.
- [ ] Order weight (Sales) or supplier weight (Purchase / Local) updates the weight difference, the %, and the balance warning.
- [ ] Save with a required Select2 empty: it is outlined red with "Please fill in the field." and the form validation blocks the save.
- [ ] Save with everything filled: the page reloads and the record is listed.

## 6. Weight types
- [ ] Container: the pending container dropdown fills, the first card is hidden, container no 1 is required. Picking a container fills its first weighing and hides the capture buttons; "-" clears it again.
- [ ] Empty Container: the container no input is shown and required; the second card is hidden.
- [ ] Different Container: the label reads the pending bin text; the replacement container, second vehicle weight and replacement container/seal fields are shown. Gross 2 / tare 2 fill the empty container 2 weight and nett 2 = |tare 2 − vehicle 2 − nett 1|.
- [ ] Switching back to Normal restores the normal field set.
- [ ] Changing the transaction type with weight type Container reloads the container list.

## 7. Vehicles
- [ ] Picking vehicle 1 fills its customer (Sales / Misc) or supplier (Purchase / Local).
- [ ] Manual vehicle 1: the plate text box is shown and upper-cased, and the lookup runs while typing.
- [ ] Vehicle 2 (Different Container only) fills the second vehicle weight, from the dropdown or the manual box.
- [ ] An unknown plate shows the alert and clears the manual box of that vehicle (vehicle 2 clears its own box).

## 8. Edit, print and cancel
- [ ] Edit an incomplete record: all fields load and the capture buttons are shown.
- [ ] Edit a complete record: the capture buttons are hidden.
- [ ] Edit a Container record whose container is no longer pending: the container still shows in the dropdown.
- [ ] Save & Print: after saving, the pre-print modal opens; printing opens the print window.
- [ ] Print from a row opens the pre-print modal; a failed print hides the spinner and shows the error toast.
- [ ] Delete one record (lorry and empty container): confirm, enter a reason, both tables reload.
- [ ] Multi delete with nothing ticked shows the "select at least one" alert; with rows ticked it cancels them all.

## 9. Export
- [ ] Export PDF with no rows ticked uses the filters, including the to-date.
- [ ] Export PDF with rows ticked prints only those rows.
- [ ] Export Excel with and without ticked rows downloads the matching records.
- [ ] Export Excel with a filter value containing `&` or a space still works.
- [ ] A PDF export error shows the page's red toast.

## 10. Indicator
- [ ] Stop the local service: within a second the weight shows 0 and the badge turns red.
- [ ] Restart it: the reading updates about twice a second.

## 11. Deep link
- [ ] `index.php?weight=<id>` opens that record for edit.
- [ ] `index.php?weight=1);alert(1)//` does not run the alert.

## 12. Equivalence (after a refactor)
- [ ] For each weight type, fill the form the same way before and after the change and compare `$('#weightForm').serialize()` in the console, and the `save` request payload in the Network tab. They must be identical.
