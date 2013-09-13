<?php
/**
 * @author vpak
 * @date 2013-09-13 10:07:50
 */

namespace Miao\Office\DataHelper;

class Url
{
    protected $_hrefBaseUrl;
    protected $_srcBaseUrl;

    public function __construct( $hrefBaseUrl, $srcBaseUrl )
    {
        $this->_hrefBaseUrl = $hrefBaseUrl;
        $this->_srcBaseUrl = $srcBaseUrl;
    }

    /**
     * @param mixed $picsBaseUrl
     */
    public function setSrcBaseUrl( $picsBaseUrl )
    {
        $this->_srcBaseUrl = self::_scheme( $picsBaseUrl );
    }

    /**
     * @return mixed
     */
    public function getSrcBaseUrl()
    {
        return $this->_srcBaseUrl;
    }

    /**
     * @param mixed $srcBaseUrl
     */
    public function setHrefBaseUrl( $srcBaseUrl )
    {
        $this->_hrefBaseUrl = self::_scheme( $srcBaseUrl );
    }

    /**
     * @return mixed
     */
    public function getHrefBaseUrl()
    {
        return $this->_hrefBaseUrl;
    }

    public function src( $path, $query= '' )
    {
        $result = self::build( $this->getSrcBaseUrl(), $path, $query );
        return $result;
    }

    public function href( $path, $query = '', $fragment = '' )
    {
        $result = self::build( $this->getHrefBaseUrl(), $path, $query, $fragment );
        return $result;
    }

    static public function build( $url, $path, $query = '', $fragment = '' )
    {
        $resultUrl = array(
            'scheme' => '',
            'host' => '',
            'port' => '',
            'user' => '',
            'pass' => '',
            'path' => '',
            'query' => '',
            'fragment' => ''
        );

        $originalUrl = parse_url( $url );
        $resultUrl = array_merge( $resultUrl, $originalUrl );

        if ( !empty( $path ) )
        {
            if ( $path[ 0 ] == '/' )
            {
                $resultUrl[ 'path' ] = $path;
            }
            else
            {
                $resultUrl[ 'path' ] .= '/' . $path;
            }
        }

        $result = ( $resultUrl[ 'scheme' ] ? $resultUrl[ 'scheme' ] : 'http' ) . '://';
        if ( !empty( $resultUrl[ 'user' ] ) )
        {
            $result .= $resultUrl[ 'user' ];
            if ( !empty( $resultUrl[ 'pass' ] ) )
            {
                $result .= ':' . $resultUrl[ 'pass' ];
            }
            $result .= '@';
        }
        $result .= $resultUrl[ 'host' ];
        if ( !empty( $resultUrl[ 'port' ] ) )
        {
            $result .= ':' . $resultUrl[ 'port' ];
        }
        if ( !empty( $resultUrl[ 'path' ] ) )
        {
            $result .= $resultUrl[ 'path' ];
        }

        // query -------------------
        if ( isset( $originalUrl[ 'query' ] ) )
        {
            parse_str( $originalUrl[ 'query' ], $originalQuery );
        }
        else
        {
            $originalQuery = array();
        }
        $resultQuery = array();
        if ( is_string( $query ) )
        {
            parse_str( $query, $resultQuery );
        }
        else if ( is_array( $query ) )
        {
            $resultQuery = $query;
        }
        if ( is_array( $originalQuery ) )
        {
            $resultQuery = array_replace_recursive(
                $originalQuery, $resultQuery
            );
        }
        $resultUrl[ 'query' ] = self::queryString( $resultQuery );
        if ( !empty( $resultUrl[ 'query' ] ) )
        {
            $result .= '?' . $resultUrl[ 'query' ];
        }
        // -------------------

        // fragment -------------------
        if ( !empty( $fragment ) )
        {
            $fragment = ltrim( $fragment, '#' );
            $result .= '#' . $fragment;
        }
        else if ( !empty( $resultUrl[ 'fragment' ] ) )
        {
            $result .= '#' . $resultUrl[ 'fragment' ];
        }
        // -------------------

        $check = self::check( $result );
        if ( !$check )
        {
            $msg = sprintf( 'Invalid url %s', $result );
            throw new \Miao\Office\DataHelper\Url\Exception( $msg );
        }

        return $result;
    }

    static public function queryString( array $params, $name = null )
    {
        $pieces = array();
        foreach ( $params as $key => $val )
        {
            if ( is_array( $val ) )
            {
                if ( $name == null )
                {
                    $pieces[ ] = self::queryString( $val, $key );
                }
                else
                {
                    $pieces[ ] = self::queryString( $val, $name . "[$key]" );
                }
            }
            else
            {
                if ( $name != null )
                {
                    $pieces[ ] = $name . "[$key]" . "=$val";
                }
                else
                {
                    $pieces[ ] = "$key=$val";
                }
            }
        }
        $result = implode( '&', $pieces );
        return $result;
    }

    static public function check( $url )
    {
        $check = filter_var( $url, FILTER_VALIDATE_URL );

        $result = true;
        if ( !$check )
        {
            $result = false;
        }

        return $result;
    }

    static protected function _scheme( $url )
    {
        $scheme = parse_url( $url, PHP_URL_SCHEME );
        $result = $scheme ? $url : 'http://' . $url;
        return $result;
    }
}