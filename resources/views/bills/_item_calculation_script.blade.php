<script>
    document.addEventListener('DOMContentLoaded', function () {
        const billItemsBody = document.getElementById('bill-items-body');
        const taxAmountInput = document.getElementById('tax_amount');
        const subtotalDisplay = document.getElementById('subtotal-display');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        const addItemButton = document.getElementById('add-item');

        let itemIndex = billItemsBody.children.length; // Start index from existing rows

        function calculateTotals() {
            let subtotal = 0;
            const itemRows = billItemsBody.querySelectorAll('tr');

            itemRows.forEach(row => {
                const quantityInput = row.querySelector('.item-qty');
                const unitPriceInput = row.querySelector('.item-price');
                const itemTotalInput = row.querySelector('.item-total');

                const quantity = parseFloat(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                const itemTotal = quantity * unitPrice;

                itemTotalInput.value = itemTotal.toFixed(2);
                subtotal += itemTotal;
            });

            const taxAmount = parseFloat(taxAmountInput.value) || 0;
            const totalAmount = subtotal + taxAmount;

            subtotalDisplay.textContent = '$' + subtotal.toFixed(2);
            totalAmountDisplay.textContent = '$' + totalAmount.toFixed(2);
        }

        // Event listener for input changes on quantity, unit price, and tax
        billItemsBody.addEventListener('input', function(event) {
            if (event.target.classList.contains('item-qty') || event.target.classList.contains('item-price')) {
                calculateTotals();
            }
        });
        taxAmountInput.addEventListener('input', calculateTotals);

        // Event listener for removing an item row
        billItemsBody.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                event.target.closest('tr').remove();
                calculateTotals(); // Recalculate after removing a row
            }
        });

        // Event listener for adding a new item row (for standalone bills)
        if (addItemButton) {
            addItemButton.addEventListener('click', function() {
                const newRow = `
                    <tr>
                        <input type="hidden" name="items[${itemIndex}][bill_item_id]" value="">
                        <input type="hidden" name="items[${itemIndex}][purchase_order_item_id]" value="">
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="">
                        <td><input type="text" name="items[${itemIndex}][item_name]" class="form-control" required></td>
                        <td><input type="text" name="items[${itemIndex}][item_description]" class="form-control"></td>
                        <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty" value="1" required></td>
                        <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-price" step="0.01" value="0.00" required></td>
                        <td><input type="text" class="form-control item-total" value="0.00" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-item">X</button></td>
                    </tr>
                `;
                billItemsBody.insertAdjacentHTML('beforeend', newRow);
                itemIndex++; // Increment for the next new item
                calculateTotals(); // Recalculate after adding a row
            });
        }

        // Initial calculation on page load
        calculateTotals();
    });
</script>