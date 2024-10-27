<?php

namespace App\Extensions;

use Carbon\Carbon;

class CarbonExtension extends Carbon
{
    /**
     * Default format for DateTime
     */
    public function formatDefaultDateTime(): string
    {
        return $this->format('Y/m/d H:i');
    }

    /**
     * Default format for Date
     */
    public function formatDefaultDate(): string
    {
        return $this->format('Y/m/d');
    }
}
