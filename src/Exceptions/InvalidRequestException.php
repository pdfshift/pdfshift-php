<?php

namespace PDFShift\Exceptions;

/**
 * Class InvalidRequestException
 *
 * @package PDFShift\PDFShift\Exceptions
 */
class InvalidRequestException extends PDFShiftException
{
    public function __construct($message, $body)
    {
        parent::__construct($message, 400, $body);
    }
}
