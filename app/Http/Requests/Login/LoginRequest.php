<?php

namespace App\Http\Requests\Login;

use App\Http\Requests\BaseApiRequest;
use App\Rules\CheckMailRFC;
use App\Rules\MaxLength;

class LoginRequest extends BaseApiRequest
{
    /* Custom rules
     * */
    public function rules()
    {
        return
        [
            'email' => [
                'required',
                new CheckMailRFC(),
                new MaxLength(200),
            ],
            'password' => [
                'required',
                new MaxLength(100),
            ]
        ];
    }

    /* Custom attributes
    * */
    public function attributes()
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
