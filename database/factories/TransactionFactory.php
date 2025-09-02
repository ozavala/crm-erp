<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\OwnerCompany;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\JournalEntry;
use App\Models\CrmUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'owner_company_id' => OwnerCompany::factory(),
            'type' => $this->faker->randomElement(['sale', 'purchase', 'payment', 'receipt', 'adjustment']),
            'date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => 'USD',
            'description' => $this->faker->sentence(),
            'supplier_id' => Supplier::factory(),
            'customer_id' => Customer::factory(),
            'invoice_id' => Invoice::factory(),
            'bill_id' => Bill::factory(),
            'payment_id' => Payment::factory(),
            'journal_entry_id' => JournalEntry::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'created_by_user_id' => CrmUser::factory(),
        ];
    }
}
