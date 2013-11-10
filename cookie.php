<?php

/**
 * Class For Handling Cookie
 * Cookie Always Exist In Next Request
 * But This Class Make Cookie Available In Current Request
 * @author slier
 */

namespace utility;

class Cookie
{

    const OneDay = 86400;
    const SevenDays = 604800;
    const OneMonth = 2592000;
    const SixMonths = 15811200;
    const OneYear = 31536000;


    /**
     * Check key in cookie
     *
     * @param $name
     * @return bool
     */
    public function check( $name )
    {
        return isset( $_COOKIE[$name] );
    }


    /**
     * Get the value of the given cookie name.
     *
     * @param $name
     * @return null
     */
    public function get( $name )
    {
        return ( isset( $_COOKIE[$name] ) ? $_COOKIE[$name] : null );
    }


    /**
     * Set a cookie. Silently does nothing if headers have already been sent.
     *
     * @param $name
     * @param $value
     * @param int $expiry
     * @param string $path
     * @param bool $domain
     * @return bool
     *
     * set('user','slier') single dimension cookie
     * set('user['name']','slier') multi dimension cookie
     * set('user['age']','28') single dimension cookie
     */
    public function set( $name, $value, $expiry = self::OneYear, $path = '/', $domain = false )
    {
        $returnVal = false;
        if( !headers_sent() ){
            if( $domain === false ){
                $domain = $_SERVER['HTTP_HOST'];
            }

            if( is_numeric( $expiry ) ){
                $expiry += time();
            }
            else{
                $expiry = strtotime( $expiry );
            }

            $returnVal = @setcookie( $name, $value, $expiry, $path, $domain );

            if( $returnVal ){
                $_COOKIE[$name] = $value;
            }
        }
        return $returnVal;
    }


    /**
     * Delete cookie
     *
     * @param $name
     * @param bool $remove_from_global remove cookie from this request.
     * @param string $path
     * @param bool $domain
     * @return bool
     */
    public function delete( $name, $remove_from_global = true, $path = '/', $domain = false )
    {
        $return = false;
        if( !headers_sent() ){
            if( $domain === false ){
                $domain = $_SERVER['HTTP_HOST'];
            }

            $return = setcookie( $name, '', time() - 3600, $path, $domain );
            if( $remove_from_global ){
                unset( $_COOKIE[$name] );
            }
        }
        return $return;
    }


}

?>
