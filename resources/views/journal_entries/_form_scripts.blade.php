<script>
document.addEventListener('DOMContentLoaded', function () {
    const linesContainer = document.getElementById('journal-lines-container');
    const addLineBtn = document.getElementById('add-line-btn');
    let lineRowIndex = {{ $lineIndex + 1 }}; // Ensure this is correctly initialized from PHP

    const totalDebitsEl = document.getElementById('total-debits');
    const totalCreditsEl = document.getElementById('total-credits');
    const balanceStatusEl = document.getElementById('balance-status').querySelector('span');

    function calculateTotals() {
        let debits = 0;
        let credits = 0;
        document.querySelectorAll('.journal-line-row').forEach(row => {
            const debitInput = row.querySelector('.debit-input');
            const creditInput = row.querySelector('.credit-input');
            if (debitInput && debitInput.value) {
                debits += parseFloat(debitInput.value) || 0;
            }
            if (creditInput && creditInput.value) {
                credits += parseFloat(creditInput.value) || 0;
            }
        });
        totalDebitsEl.textContent = '$' + debits.toFixed(2);
        totalCreditsEl.textContent = '$' + credits.toFixed(2);

        if (Math.abs(debits - credits) < 0.001 && (debits > 0 || credits > 0) ) { // Using a small tolerance for float comparison
            balanceStatusEl.textContent = __('journal_entries.Balanced');
            balanceStatusEl.classList.remove('text-danger');
            balanceStatusEl.classList.add('text-success');
        } else {
            balanceStatusEl.textContent = __('journal_entries.Out of Balance');
            balanceStatusEl.classList.remove('text-success');
            balanceStatusEl.classList.add('text-danger');
        }
    }

    if (addLineBtn) {
        addLineBtn.addEventListener('click', function () {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'align-items-center', 'mb-2', 'journal-line-row', 'border', 'p-2', 'rounded');
            newRow.innerHTML = `
                <div class="col-md-5 mb-2">
                    <label class="form-label">${__('journal_entries.Account Name')} <span class="text-danger">*</span></label>
                    <input type="text" name="lines[${lineRowIndex}][account_name]" class="form-control" placeholder="${__('journal_entries.e.g., Cash, Office Expense')}" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">${__('journal_entries.Debit')}</label>
                    <input type="number" step="0.01" name="lines[${lineRowIndex}][debit_amount]" class="form-control debit-input" placeholder="0.00">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">${__('journal_entries.Credit')}</label>
                    <input type="number" step="0.01" name="lines[${lineRowIndex}][credit_amount]" class="form-control credit-input" placeholder="0.00">
                </div>
                <div class="col-md-1 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-line-btn">&times;</button>
                </div>
            `;
            linesContainer.appendChild(newRow);
            lineRowIndex++;
            attachLineEventListeners(newRow);
            calculateTotals();
        });
    }

    function attachLineEventListeners(row) {
        row.querySelector('.remove-line-btn').addEventListener('click', function () {
            row.remove();
            calculateTotals();
        });
        row.querySelectorAll('.debit-input, .credit-input').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
    }

    document.querySelectorAll('.journal-line-row').forEach(attachLineEventListeners);
    calculateTotals(); // Initial calculation
});
</script>