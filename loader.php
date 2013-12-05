<?php

namespace utility;

/**
 * Class AutoLoad
 * @package utility
 */
class Loader
{
    /**
     * Include dir
     * @var array
     */
    protected static $dirs = array();


    /**
     *Make it static
     */
    private function __construct()
    {

    }


    /**
     * Register autoload
     * @param $dirs
     */
    public static function register( $dirs )
    {
        self::setDirectory( $dirs );
        spl_autoload_register( array( __CLASS__, 'loader' ) );
    }


    /**
     * Unregister autoload
     */
    public static function unregister()
    {
        spl_autoload_unregister( array( __CLASS__, 'loader' ) );
    }


    /**
     * Set include directory
     * @param $dirs
     */
    protected static function setDirectory( $dirs )
    {
        if( is_array( $dirs ) || is_object( $dirs ) ){
            foreach( $dirs as $dir ){
                self::setDirectory( $dir );
            }
        }
        else if( is_string( $dirs ) ){
            if( !in_array( $dirs, self::$dirs ) ) self::$dirs[] = $dirs;
        }
    }


    /**
     * Class loader
     * @param string $class
     * @return mixed
     */
    protected static function loader( $class )
    {
        if( class_exists( $class, false ) ){
            return true;
        }

        $class = str_replace( '\\', '/', $class );

        foreach( self::$dirs as $dir ){
            $file = $dir . self::getClassPath( $class ) . self::camelToDot( self::getClassName( $class ) ) . '.php';
            if( is_readable( $file ) ){
                return require $file;
            }
        }
    }


    /**
     * Get path to class location
     * @param string $class
     * @return string
     */
    protected static function getClassPath( $class )
    {
        return substr( $class, 0, strrpos( $class, '/' ) + 1 );
    }


    /**
     * Get class name from class path
     * @param string $class
     * @return string
     */
    protected static function getClassName( $class )
    {
        return substr( $class, strrpos( $class, '/' ) + 1 );
    }


    /**
     * Convert camel case class name to dot(.)
     * @param string $class
     * @return string
     */
    protected static function camelToDot( $class )
    {
        return strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1.', $class ) );
    }

}