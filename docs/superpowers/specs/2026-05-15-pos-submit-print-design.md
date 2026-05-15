# Design Spec - POS Submit & Print Button

Add a "Submit & Print" button to the POS checkout modal to allow users to save a sale and immediately view the receipt PDF.

## User Story
As a POS operator, I want to be able to save a sale and print the receipt in one click, so that I can serve customers more efficiently.

## Proposed Changes

### 1. Frontend: Checkout Modal
File: `resources/views/livewire/pos/includes/checkout-modal.blade.php`

- Add a new button in the `modal-footer` section.
- The button will be a `type="submit"` button with `name="print"` and `value="1"`.
- Style: `btn btn-info` (blue/teal) to distinguish it from the standard "Submit" button.

```html
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Submit</button>
    <button type="submit" name="print" value="1" class="btn btn-info">Submit & Print</button>
</div>
```

### 2. Backend: POS Controller
File: `Modules/Sale/Http/Controllers/PosController.php`

- Modify the `store` method to capture the created sale object.
- Check if the `print` parameter is present in the request.
- If `print` is present, redirect to the `sales.pos.pdf` route with the new sale's ID.
- Otherwise, maintain existing behavior (redirect to `sales.index`).

```php
public function store(StorePosSaleRequest $request) {
    $sale = DB::transaction(function () use ($request) {
        // ... existing creation logic ...
        return $sale;
    });

    toast('POS Sale Created!', 'success');

    if ($request->has('print')) {
        return redirect()->route('sales.pos.pdf', $sale->id);
    }

    return redirect()->route('sales.index');
}
```

## Success Criteria
1. Clicking "Submit" saves the sale and redirects to the Sales Index (existing behavior).
2. Clicking "Submit & Print" saves the sale and redirects directly to the POS PDF receipt page.
3. The sale data (including inventory and payments) is correctly recorded in both cases.
