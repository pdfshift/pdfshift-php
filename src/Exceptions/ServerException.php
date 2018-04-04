<?php

namespace PDFShift\Exceptions;

/**
 * Class ServerException
 *
 * @package PDFShift\PDFShift\Exceptions
 */
class ServerException extends PDFShiftException
{
    public function __construct($body)
    {
        parent::__construct('A fatal error occured.', 500, $body);
    }
}
