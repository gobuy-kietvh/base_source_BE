<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Return a response to view PDF on browser
     *
     * @param string $content PDF string content
     * @param string $filename
     * @return mixed
     */
    public function showPdfResponse($content, $filename) {
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }
}
