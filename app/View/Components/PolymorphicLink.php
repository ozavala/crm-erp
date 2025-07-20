<?php

namespace App\View\Components;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class PolymorphicLink extends Component
{
    public ?string $href = null;
    public string $text;

    /**
     * Create a new component instance.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model The polymorphic model instance (e.g., $contact->contactable)
     * @param string $fallbackText The text to display if the model is null.
     */
    public function __construct(?Model $model, string $fallbackText = 'N/A')
    {
        $this->text = $fallbackText;

        if (!$model) {
            return;
        }

        if ($model instanceof Customer) {
            $this->href = route('customers.show', $model->customer_id);
            $this->text = $model->company_name ?: $model->full_name;
        } elseif ($model instanceof Supplier) {
            $this->href = route('suppliers.show', $model->supplier_id);
            $this->text = $model->name;
        }
    }

    public function render()
    {
        return view('components.polymorphic-link');
    }
}