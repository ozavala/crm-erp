<?php

namespace App\View\Components;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class BackToParentLink extends Component
{
    public string $href;
    public string $text;

    /**
     * Create a new component instance.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $parent The parent model instance (e.g., $contact->contactable)
     * @param string $fallbackRoute The route name to use if no parent is found.
     * @param string $fallbackText The text to use for the fallback route.
     */
    public function __construct(?Model $parent, string $fallbackRoute, string $fallbackText)
    {
        if ($parent instanceof Customer) {
            $this->href = route('customers.show', $parent->customer_id);
            $this->text = class_basename($parent);
        } elseif ($parent instanceof Supplier) {
            $this->href = route('suppliers.show', $parent->supplier_id);
            $this->text = class_basename($parent);
        } else {
            $this->href = route($fallbackRoute);
            $this->text = $fallbackText;
        }
    }

    public function render()
    {
        return <<<'blade'
            <a {{ $attributes->merge(['href' => $href]) }}>Back to {{ $text }}</a>
        blade;
    }
}