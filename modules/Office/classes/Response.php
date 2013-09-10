<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 17:13
 */

namespace Miao\Office;

class Response
{
    /**
     * @var string
     */
    protected $_version;

    protected $_header;

    protected $_content;

    protected $_statusCode;

    protected $_statusText;

    /**
     * Status codes translation table.
     * The list of codes is complete according to the
     * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol (HTTP) Status Code Registry}
     * (last updated 2012-02-13).
     * Unless otherwise noted, the status code is defined in RFC2616.
     * @var array
     */
    static protected $_statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // RFC4918
        208 => 'Already Reported', // RFC5842
        226 => 'IM Used', // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect', // RFC-reschke-http-status-308-07
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC2324
        422 => 'Unprocessable Entity', // RFC4918
        423 => 'Locked', // RFC4918
        424 => 'Failed Dependency', // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal', // RFC2817
        426 => 'Upgrade Required', // RFC2817
        428 => 'Precondition Required', // RFC6585
        429 => 'Too Many Requests', // RFC6585
        431 => 'Request Header Fields Too Large', // RFC6585
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)', // RFC2295
        507 => 'Insufficient Storage', // RFC4918
        508 => 'Loop Detected', // RFC5842
        510 => 'Not Extended', // RFC2774
        511 => 'Network Authentication Required', // RFC6585
    );

    public function __construct()
    {
        $this->_header = new \Miao\Office\Header();
        $this->setProtocolVersion( '1.1' );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = sprintf(
                'HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText
            ) . "\r\n" . $this->header . "\r\n" . $this->getContent();
        return $result;
    }

    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     * @param string $version The HTTP protocol version
     * @return $this
     */
    public function setProtocolVersion( $version )
    {
        $this->_version = $version;
        return $this;
    }

    /**
     * Gets the HTTP protocol version.
     * @return string The HTTP protocol version
     */
    public function getProtocolVersion()
    {
        return $this->version;
    }

    /**
     * @param $content
     * @return $this
     * @throws Response\Exception\UnexpectedValue
     */
    public function setContent( $content )
    {
        if ( null !== $content && !is_string( $content ) && !is_numeric( $content )
            && !is_callable(
                array( $content, '__toString' )
            )
        )
        {
            throw new \Miao\Office\Response\Exception\UnexpectedValue( sprintf(
                'The Response content must be a string or object implementing __toString(), "%s" given.',
                gettype( $content )
            ) );
        }

        $this->_content = (string)$content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->_content;
    }

    public function setStatusCode( $code, $text = null )
    {
        if ( !$this->checkStatusCode( $code ) )
        {
            throw new \Miao\Office\Response\Exception\InvalidArgument( sprintf(
                'The HTTP status code "%s" is not valid.', $code
            ) );
        }
        $this->_statusCode = $code = (int)$code;
        $this->_statusText = $text;
        if ( is_null( $text ) )
        {
            $this->_statusText = isset( self::$_statusTexts[ $code ] ) ? self::$_statusTexts[ $code ] : '';
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    public function getStatusText()
    {
        return $this->_statusText;
    }

    /**
     * @param mixed $content
     * @return $this
     */
    public function setJsonContent( $content )
    {
        $this->_header->set( "Content-Type", "application/json" );
        $this->setContent( json_decode( $content ) );
        return $this;
    }

    public function checkStatusCode( $code )
    {
        return $code >= 100 && $code < 600;
    }

    /**
     * Sends HTTP headers.
     *
     * @return $this
     * @throws Header\Exception\AlreadySended
     */
    public function sendHeaders()
    {
        // headers have already been sent by the developer
        if ( headers_sent() )
        {
            throw new \Miao\Office\Response\Exception\AlreadySended();
        }
        // status
        header( sprintf( 'HTTP/%s %s %s', $this->_version, $this->_statusCode, $this->_statusText ) );
        $list = $this->_header->getList();
        foreach( $list as $item )
        {
            header( $item );
        }
        return $this;
    }

    /**
     * Send content
     * @return $this
     */
    public function sendContent()
    {
        echo $this->getContent();
        return $this;
    }

    /**
     * Send headers and content
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }
}