<?php

namespace PDFShift\Exceptions;

/**
 * Class RateLimitException
 *
 * @package PDFShift\PDFShift\Exceptions
 */
class RateLimitException extends PDFShiftException
{
    public function __construct($body)
    {
        parent::__construct('Please indicate a valid API Key.', 401, $body);
    }
}
