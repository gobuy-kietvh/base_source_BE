<?php

namespace App\Enums;

enum HttpResponseCode: int
{
    case OK = 200;                    // Successful response
    case CREATED = 201;               // Resource created successfully
    case BAD_REQUEST = 400;           // Client error
    case UNAUTHORIZED = 401;          // Authentication required
    case FORBIDDEN = 403;             // Client does not have access rights
    case NOT_FOUND = 404;             // Resource not found
    case INTERNAL_SERVER_ERROR = 500; // Server error
    case SERVICE_UNAVAILABLE = 503;   // Service unavailable

    public function message(): string
    {
        return match($this) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        };
    }
}
