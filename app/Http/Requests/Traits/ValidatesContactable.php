<?php

namespace App\Http\Requests\Traits;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Validation\Rule;

trait ValidatesContactable
{
    /**
     * Get the validation rules for the contactable polymorphic relationship.
     *
     * @return array
     */
    protected function contactableRules(): array
    {
        $rules = [
            'contactable_type' => ['required', 'string', Rule::in([Customer::class, Supplier::class])],
        ];

        $contactableType = $this->input('contactable_type');

        if (in_array($contactableType, [Customer::class, Supplier::class])) {
            $model = new $contactableType;
            $rules['contactable_id'] = ['required', 'integer', Rule::exists($model->getTable(), $model->getKeyName())];
        } else {
            // This will fail the 'contactable_type' rule first, but it's a good fallback.
            $rules['contactable_id'] = ['required', 'integer'];
        }

        return $rules;
    }
}