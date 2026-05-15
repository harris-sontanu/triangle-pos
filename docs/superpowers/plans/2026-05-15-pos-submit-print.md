# POS Submit & Print Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a "Submit & Print" button to the POS checkout modal that saves the sale and redirects directly to the receipt PDF.

**Architecture:** Frontend modification to the Livewire component view to include a new submit button with a specific name/value, and backend modification to the POS controller to handle the conditional redirect based on that value.

**Tech Stack:** Laravel, Livewire, Blade, Bootstrap.

---

### Task 1: Update Frontend Modal

**Files:**
- Modify: `resources/views/livewire/pos/includes/checkout-modal.blade.php`

- [ ] **Step 1: Add "Submit & Print" button**

Add the new button to the `modal-footer` div.

```html
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Submit</button>
    <button type="submit" name="print" value="1" class="btn btn-info">Submit & Print</button>
</div>
```

- [ ] **Step 2: Verify button presence**

Check the file content to ensure the button is added correctly.

---

### Task 2: Update Controller Logic

**Files:**
- Modify: `Modules/Sale/Http/Controllers/PosController.php`

- [ ] **Step 1: Capture Sale object in store method**

Modify the `store` method to return the `$sale` object from the transaction and use it for the redirect.

```php
    public function store(StorePosSaleRequest $request) {
        $sale = DB::transaction(function () use ($request) {
            // ... existing code ...
            
            // At the end of transaction:
            return $sale;
        });

        toast('POS Sale Created!', 'success');

        if ($request->has('print')) {
            return redirect()->route('sales.pos.pdf', $sale->id);
        }

        return redirect()->route('sales.index');
    }
```

- [ ] **Step 2: Apply changes to PosController.php**

Use `replace` to update the `store` method. Ensure the `$sale` variable is returned from the closure.

---

### Task 3: Manual Verification

- [ ] **Step 1: Test standard Submit**
Open POS, add items, click "Proceed", click "Submit". Verify it redirects to Sales Index.

- [ ] **Step 2: Test Submit & Print**
Open POS, add items, click "Proceed", click "Submit & Print". Verify it redirects to the PDF view (route `sales.pos.pdf`).
