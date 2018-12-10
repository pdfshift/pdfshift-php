<?php
/**
 * __/\\\\\\\\\\\\\____/\\\\\\\\\\\\_____/\\\\\\\\\\\\\\\___/\\\\\\\\\\\____/\\\______________________/\\\\\______________________
 * __\/\\\/////////\\\_\/\\\////////\\\__\/\\\///////////__/\\\/////////\\\_\/\\\____________________/\\\///______________________
 * ___\/\\\_______\/\\\_\/\\\______\//\\\_\/\\\____________\//\\\______\///__\/\\\__________/\\\_____/\\\_________/\\\____________
 * ____\/\\\\\\\\\\\\\/__\/\\\_______\/\\\_\/\\\\\\\\\\\_____\////\\\_________\/\\\_________\///___/\\\\\\\\\___/\\\\\\\\\\\______
 * _____\/\\\/////////____\/\\\_______\/\\\_\/\\\///////_________\////\\\______\/\\\\\\\\\\___/\\\_\////\\\//___\////\\\////______
 * ______\/\\\_____________\/\\\_______\/\\\_\/\\\___________________\////\\\___\/\\\/////\\\_\/\\\____\/\\\________\/\\\_________
 * _______\/\\\_____________\/\\\_______/\\\__\/\\\____________/\\\______\//\\\__\/\\\___\/\\\_\/\\\____\/\\\________\/\\\_/\\____
 * ________\/\\\_____________\/\\\\\\\\\\\\/___\/\\\___________\///\\\\\\\\\\\/___\/\\\___\/\\\_\/\\\____\/\\\________\//\\\\\____
 * _________\///______________\////////////_____\///______________\///////////_____\///____\///__\///_____\///__________\/////____
 *
 * Wrapper around the PDFShift API
 *
 * @category API
 * @package  PDFShift
 * @author   Cyril Nicodeme <contact@pdfshift.io>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  1.0.4
 * @link     https://pdfshift.io
 */

namespace PDFShift;

use PDFShift\Exceptions;

class PDFShift
{
    const WATERMARK_TEXT = 1;
    const WATERMARK_IMAGE = 2;
    const WATERMARK_PDF = 3;

    // @var string The PDFShift API key to be used for requests.
    private static $_apiKey = null;

    // @var string The base URL for the PDFShift API.
    private static $_apiBase = 'https://api.pdfshift.io/v2';

    /**
     * Returns the current api key
     *
     * @return string Api key
     */
    public static function getApiKey()
    {
        return self::$_apiKey;
    }

    /**
     * Set the API Key
     *
     * @param string $apiKey The api key
     *
     * @return null
     */
    public static function setApiKey($apiKey)
    {
        self::$_apiKey = $apiKey;
    }

    /**
     * Convert a $source to PDF using $options
     *
     * @param string $source  Source of data. Can be an URL or some HTML
     * @param array  $options Options to pass to the API.
     * @param string $output  Save result to file. If null, will return the binary
     *
     * @return string Binary PDF file
     */
    public static function convertTo($source, $options = array(), $output = null)
    {
        $instance = new self($options);
        $instance->convert($source);

        if (is_null($output)) {
            return $instance->getData();
        }

        return $instance->save($output);
    }

    /**
     * Returns the Credits status from the API using the defined API Key
     *
     * @return null
     */
    public static function credits()
    {
        if (is_null(self::$_apiKey)) {
            throw new Exceptions\InvalidApiKeyException();
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$_apiBase.'/credits/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => self::$_apiKey.':'
        ));
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode === 200) {
            return json_decode($response, true);
        }

        return self::_handleError($response, $statusCode);
    }

    /**
     * Process the response from the server and act accordingly
     *
     * @param array $response The response from cURL
     *
     * @return null
     */
    private static function _handleError($response, $statusCode)
    {
        $body = json_decode($response, true);
        if (is_null($body)) {
            throw new Exceptions\PDFShiftException(
                'Invalid response from the server.',
                500
            );
        }

        switch ($statusCode) {
            case 400:
                if (!empty($body['message'])) {
                    throw new Exceptions\InvalidRequestException($body['message'], $body);
                }

                if (isset($body['error']) && is_string($body['error'])) {
                    throw new Exceptions\InvalidRequestException($body['error'], $body);
                }

                reset($body['errors']);
                $key = key($body['errors']);
                $message = $key.' : '.$body['errors'][$key][0];
                throw new Exceptions\InvalidRequestException($message, $body);
            case 401:
                throw new Exceptions\InvalidApiKeyException($body);
            case 403:
                throw new Exceptions\NoCreditsException($body);
            case 429:
                throw new Exceptions\RateLimitException($body);
            default:
                throw new Exceptions\ServerException($body);
        }
    }

    // @var array The array of options to pass to the API
    private $_options = array();

    // @var string Binary result (ie the PDF) from the call to the API
    private $_data = null;

    /**
     * Create a new PDFShift Instance
     *
     * @param array $options Options to pass to the API.
     */
    public function __construct($options = array())
    {
        $this->_options = $options;
    }

    /**
     * Generic setter for the options
     *
     * @param string $key   The key to the options
     * @param mixed  $value The value
     *
     * @return null
     */
    public function __set($key, $value)
    {
        $this->_options[$key] = $value;
    }

    /**
     * Generic getter for the options
     *
     * @param string $key The key to the options
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }

        return null;
    }

    /**
     * Add Margin details about the document
     *
     * @param array $margin Margins for Top, Left, Right and Bottom
     *
     * @return null
     */
    public function setMargin($margin)
    {
        $this->_options['margin'] = [
            'top'    => (isset($margin['top']) ? $margin['top'] : null),
            'right'  => (isset($margin['right']) ? $margin['right'] : null),
            'bottom' => (isset($margin['bottom']) ? $margin['bottom'] : null),
            'left'   => (isset($margin['left']) ? $margin['left'] : null),
        ];
    }

    /**
     * Handles basic auth for the destination (source URL)
     *
     * @param string $username Username used for the basic auth
     * @param string $password Password used for the basic auth
     *
     * @return null
     */
    public function setAuth($username, $password)
    {
        $this->_options['auth'] = [
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * Add cookies to the request
     *
     * @param array $cookies A list of cookies.
     *
     * @return null
     */
    public function setCookies($cookies)
    {
        foreach ($cookies as $cookie) {
            $this->addCookie(
                $cookie['name'],
                (isset($cookie['value']) ? $cookie['value'] : null),
                (isset($cookie['secure']) ? $cookie['secure'] : false),
                (isset($cookie['httpOnly']) ? $cookie['httpOnly'] : false)
            );
        }
    }

    /**
     * Add cookies, one by one
     *
     * @param string  $name     Name of the cookie
     * @param string  $value    Value for this cookie
     * @param boolean $secure   Works only when the request is using https
     * @param boolean $httpOnly This cookie will only works on http content
     *
     * @return null
     */
    public function addCookie($name, $value = null, $secure = false, $httpOnly = false)
    {
        if (!isset($this->_options['cookies'])) {
            $this->_options['cookies'] = array();
        }

        $this->_options['cookies'][] = [
            'name' => $name,
            'value' => $value,
            'secure' => $secure,
            'http_only' => $httpOnly
        ];
    }

    /**
     * Clear the cookies entry.
     *
     * @return null
     */
    public function clearCookies()
    {
        $this->_options['cookies'] = array();
    }

    /**
     * Set headers for the HTTP request
     *
     * @param array $headers An object of headers.
     *
     * @return null
     */
    public function setHTTPHeaders($headers)
    {
        foreach ($headers as $name=>$value) {
            $this->addHTTPHeader($name, $value);
        }
    }

    /**
     * Adding one header at a time
     *
     * @param string $name  The name of the header
     * @param string $value The value for this header
     *
     * @return null
     */
    public function addHTTPHeader($name, $value = null)
    {
        if (!isset($this->_options['http_headers'])) {
            $this->_options['http_headers'] = array();
        }

        $this->_options['http_headers'][$name] = $value;
    }

    /**
     * Clear the http headers entry.
     *
     * @return null
     */
    public function clearHTTPHeaders()
    {
        $this->_options['http_headers'] = array();
    }

    /**
     * Define the content that will be on the top of all pages
     *
     * @param string $source The content, can be either an URL or raw HTML data.
     * @param string $spacing The space between the top and the start of the document.
     *
     * @return null
     */
    public function setHeader($source, $spacing = null)
    {
        $this->_options['header'] = ['source' => $source, 'spacing' => $spacing];
    }

    /**
     * Define the content that will be on the bottom of all pages
     *
     * @param string $source The content, can be either an URL or raw HTML data.
     * @param string $spacing The space between the end of the document and the bottom
     *
     * @return null
     */
    public function setFooter($source, $spacing = null)
    {
        $this->_options['footer'] = ['source' => $source, 'spacing' => $spacing];
    }

    /**
     * Protect a document using password, and can set restrictions on what to do with this document
     * Including no copy, no modifications and no print.
     *
     * @param array $options A set of options
     *
     * @return null
     */
    public function protect($options)
    {
        $this->_options['protection'] = [
            'author'         => (isset($options['author']) ? $options['author'] : null),
            'user_password'  => (isset($options['userPassword']) ? $options['userPassword'] : null),
            'owner_password' => (isset($options['ownerPassword']) ? $options['ownerPassword'] : null),
            'no_print'       => (isset($options['noPrint']) ? $options['noPrint'] : null),
            'no_copy'        => (isset($options['noCopy']) ? $options['noCopy'] : null),
            'no_modify'      => (isset($options['noModify']) ? $options['noModify'] : null)
        ];
    }

    /**
     * Add a watermark to the document.
     *
     * @param array  $options A set of options, including either "image", "text" or "source"
     *
     * @return null
     */
    public function watermark($options)
    {
        $this->_options['watermark'] = $options;
    }

    /**
     * Calls the /convert/ endpoint at PDFShift.io API
     * And converts the source to a PDF file.
     *
     * @param string $source URL or raw HTML data to be converted
     *
     * @return null
     *
     * @throws Exception
     */
    public function convert($source)
    {
        $this->_options['source'] = $source;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$_apiBase.'/convert/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($this->_options),
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_USERPWD => self::$_apiKey.':'
        ));
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);


        if ($statusCode === 200) {
            if (isset($this->_options['filename'])) {
                /** 
                 * "filename" will save the resulting PDF to Amazon S3 for 2 days,
                 * and will return a JSON response
                 */
                $this->data = json_decode($response, true);
            } else {
                $this->data = $response;
            }
            return null;
        }

        return self::_handleError($response, $statusCode);
    }

    /**
     * Returns the binary PDF once it has been converted
     *
     * @return string PDF file in binary format
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Save the resulting PDF To file
     *
     * @param string $filepath The path to save the data
     *
     * @return null
     *
     * @throws PDFShiftException
     */
    public function save($filepath)
    {
        if (is_null($this->getData())) {
            throw new Exceptions\PDFShiftException('A fatal error occured while trying to save the file to disk.', 500);
        }

        return file_put_contents($filepath, $this->getData());
    }
}
