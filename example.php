<?php

require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

$pdfshift = new PDFShift();
$pdfshift->setHTTPHeaders([
    'X-Original-Header' => 'Awesome value'
]);
$pdfshift->addHTTPHeader('user-agent', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0');
$pdfshift->convert('https://pdfshift.io/documentation');
$pdfshift->save('documentation.pdf');
