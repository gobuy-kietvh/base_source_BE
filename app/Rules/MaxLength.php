<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxLength implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param int $max
     * @return void
     */
    public function __construct(
        private int $max,
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = str_replace("\r\n", "\n", $value);
        $currentLength = mb_strlen($value);

        if (! ($currentLength <= $this->max)) {
            $message = trans('validation.max_length', [
                'attribute' => ":attribute",
                'max' => $this->max,
                'current' => $currentLength,
            ]);
            $fail($message);
        }
    }
}
