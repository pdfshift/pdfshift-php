# PDFShift PHP Package

This PHP package provides a simplified way to interact with the [PDFShift](https://pdfshift.io) API.

## Documentation

See the full documentation on [PDFShift's documentation](https://pdfshift.io/documentation).

## Requirements

PHP 5.4.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require pdfshift/pdfshift-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/pdfshift/pdfshift-php/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/pdfshift-php/init.php');
```

## Usage

This library needs to be configured with your `api_key` received when creating an account.
Setting it is easy as:

```php
\PDFShift\PDFShift::setApiKey('your_api_key');
```


### Basic example

#### With an URL

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');
PDFShift::convertTo('https://www.example.com', null, 'result.pdf');
```

#### With inline HTML data:

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

$data = file_get_content('invoice.html');
PDFShift::convertTo(data, null, 'result.pdf');
```

### Custom CSS

#### Loading CSS from an URL:

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

$data = file_get_content('invoice.html');
PDFShift::convertTo(data, ['css' => 'https://www.example.com/public/css/print.css'], 'result.pdf');
```

#### Loading CSS from a string:

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

$data = file_get_content('invoice.html');
PDFShift::convertTo(data, ['css' => 'a {text-decoration: underline; color: blue}'], 'result.pdf');
```

### Custom HTTP Headers

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

// We use an instance of PDFShift instead of the ::convertTo to easily handle advanced configuration
$pdfshift = new PDFShift();
$pdfshift->setHTTPHeaders(['X-Original-Header' => 'Awesome value']);
$pdfshift->addHTTPHeader('user-agent', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0'); // Also works like this
$pdfshift->convert('https://httpbin.org/headers');
$pdfshift->save('result.pdf');
```

### Accessing secured pages

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

// We use an instance of PDFShift instead of the ::convertTo to easily handle advanced configuration
$pdfshift = new PDFShift();
$pdfshift->auth('user', 'passwd');
$pdfshift->convert('https://httpbin.org/basic-auth/user/passwd');
$pdfshift->save('result.pdf');
```

### Using cookies

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

// We use an instance of PDFShift instead of the ::convertTo to easily handle advanced configuration
$pdfshift = new PDFShift();
$pdfshift->addCookie('session', '4cb496a8-a3eb-4a7e-a704-f993cb6a4dac');
$pdfshift->convert('https://httpbin.org/cookies');
$pdfshift->save('result.pdf');
```

### Adding Watermark (Oh hi Mark!)

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

// We use an instance of PDFShift instead of the ::convertTo to easily handle advanced configuration
$pdfshift = new PDFShift();
$pdfshift->watermark([
    'image' => 'https://pdfshift.io/static/img/logo.png',
    'offsetX' => 50,
    'offsetY' => '100px',
    'rotate' => 45
])
$pdfshift->convert('https://www.example.com');
$pdfshift->save('result.pdf');
```

### Custom Header (or Footer)

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

// We use an instance of PDFShift instead of the ::convertTo to easily handle advanced configuration
$pdfshift = new PDFShift();
$pdfshift->setFooter('<div>Page {{page}} of {{total}}</div>', '50px');
$pdfshift->convert('https://www.example.com');
$pdfshift->save('result.pdf');
```

### Protecting the generated PDF

```php
require_once('vendor/autoload.php');
use \PDFShift\PDFShift;

PDFShift::setApiKey('your_api_key');

// We use an instance of PDFShift instead of the ::convertTo to easily handle advanced configuration
$pdfshift = new PDFShift();
$pdfshift->protect([
    'userPassword' => 'user',
    'ownerPassword' => 'owner',
    'noPrint' => true
]);
$pdfshift->convert('https://www.example.com');
$pdfshift->save('result.pdf');
```


## Contributing

Please see [CONTRIBUTING](https://github.com/pdfshift/pdfshift-php/blob/master/CONTRIBUTING.md) for details.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
