<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class CustomProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record) {
        $record['extra']['session_id'] = session()->getId() ?? '-';
        $record['extra']['user_id'] = auth()->id() ?? '-';

        return $record;
    }
}
