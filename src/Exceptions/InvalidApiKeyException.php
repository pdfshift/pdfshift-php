<?php

namespace PDFShift\Exceptions;

/**
 * Class InvalidApiKeyException
 *
 * @package PDFShift\PDFShift\Exceptions
 */
class InvalidApiKeyException extends PDFShiftException
{
    public function __construct($body)
    {
        parent::__construct('Please indicate a valid API Key.', 401, $body);
    }
}
