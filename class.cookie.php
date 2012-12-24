<?php

/**
 * Class For Handling Cookie
 * Cookie Always Exist In Next Request
 * But This Class Make Cookie Available In Current Request
 * @author Joddy
 */
class Cookie
{

    const OneDay = 86400;
    const SevenDays = 604800;
    const OneMonth = 2592000;
    const SixMonths = 15811200;
    const OneYear = 31536000;




    /**
     * Returns true if there is a cookie with this name
     * @access public
     * @param string $name
     * @return bool
     *
     */
    public function check( $name )
    {
        return isset( $_COOKIE[ $name ] );
    }




    /**
     * Get the value of the given cookie name. If the cookie does not exist the value of $default will be returned
     * @access public
     * @param string $name
     * @param string $default
     * @return mixed
     *
     */
    public function get( $name )
    {
        return ( isset( $_COOKIE[ $name ] ) ? $_COOKIE[ $name ] : null );
    }




    /**
     * Set a cookie. Silently does nothing if headers have already been sent.
     * @access public
     * @param string $name
     * @param string $value
     * @param mixed $expiry
     * @param string $path
     * @param string $domain
     * @example $obj->set('user','slier') single dimension cookie
     * @example $obj->set('user['name']','slier') multi dimension cookie
     * @example $obj->set('user['age']','28') single dimension cookie
     * @return bool
     */
    public function set( $name, $value, $expiry = self::OneYear, $path = '/', $domain = false )
    {
        $returnVal = false;
        if ( !headers_sent() )
        {
            if ( $domain === false )
            {
                $domain = $_SERVER[ 'HTTP_HOST' ];
            }

            if ( is_numeric( $expiry ) )
            {
                $expiry += time();
            }
            else
            {
                $expiry = strtotime( $expiry );
            }

            $returnVal = @setcookie( $name, $value, $expiry, $path, $domain );

            if ( $returnVal )
            {
                $_COOKIE[ $name ] = $value;
            }
        }
        return $returnVal;
    }




    /**
     * Delete a cookie.
     * @access public
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param bool $remove_from_global Set to true to remove this cookie from this request.
     * @return bool
     *
     */
    public function delete( $name, $remove_from_global = true, $path = '/', $domain = false )
    {
        $returnVal = false;
        if ( !headers_sent() )
        {
            if ( $domain === false )
            {
                $domain = $_SERVER[ 'HTTP_HOST' ];
            }

            $returnVal = setcookie( $name, '', time() - 3600, $path, $domain );
            if ( $remove_from_global )
            {
                unset( $_COOKIE[ $name ] );
            }
        }
        return $retval;
    }




}

?>
