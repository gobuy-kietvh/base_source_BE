<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckMailRFC implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $isValidEmail = preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $value);
        $containsInvalidChars = preg_match('/^[a-zA-Z0-9~`!@#$%^&*()\-_=+<>?,.\/:;"\'{}]*$/', $value);

        if (!$isValidEmail || !$containsInvalidChars) {
            $message =  trans('validation.check_mail_RFC');
            $fail($message);
        }
    }
}
