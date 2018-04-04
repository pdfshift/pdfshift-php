<?php

namespace PDFShift\Exceptions;

/**
 * Class PDFShiftException
 *
 * @package PDFShift\PDFShift\Exceptions
 */
class PDFShiftException extends \Exception
{
    private $body = null;

    public function __construct($message, $code, $body = null) {
        parent::__construct($message, $code);
        $this->body = $body;
    }

    public function getBody() {
        return $this->body;
    }
}
