<?php

namespace PDFShift\Exceptions;

/**
 * Class NoCreditsException
 *
 * @package PDFShift\PDFShift\Exceptions
 */
class NoCreditsException extends PDFShiftException
{
    public function __construct($body)
    {
        parent::__construct('No remaining credits left.', 403, $body);
    }
}
