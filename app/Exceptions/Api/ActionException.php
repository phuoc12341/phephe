<?php

namespace App\Exceptions\Api;

use Symfony\Component\HttpFoundation\Response;

class ActionException extends ApiException
{
    public function __construct(string $action = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $message = $action ? __('exception.' . $action) : __('http_message.' . Response::HTTP_INTERNAL_SERVER_ERROR);

        parent::__construct($message, $statusCode);
    }
}
