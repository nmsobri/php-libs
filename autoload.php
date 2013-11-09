<?php

namespace utility;

class AutoLoad
{

    protected $include_dir = null;


    /**
     * @param string $include_dir
     */
    public function __construct( $include_dir )
    {
        $this->include_dir = $include_dir;
        spl_autoload_register( array( $this, 'loader' ) );
    }


    /**
     * @param string $class
     * @return mixed
     */
    public function loader( $class )
    {
        if( class_exists( $class, false ) ){
            return true;
        }

        $class = str_replace( '\\', '/', $class );
        $file = $this->include_dir . $this->getClassPath( $class ) . $this->camelToDashed( $this->getClassName( $class ) ) . '.php';

        if( is_readable( $file ) ){
            include( $file );
        }
    }


    /**
     * @param string $class
     * @return string
     */
    protected function getClassPath( $class )
    {
        return substr( $class, 0, strrpos( $class, '/' ) + 1 );
    }


    /**
     * @param string $class
     * @return string
     */
    protected function getClassName( $class )
    {
        return substr( $class, strrpos( $class, '/' ) + 1 );
    }


    /**
     * @param string $class
     * @return string
     */
    function camelToDashed( $class )
    {
        return strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1.', $class ) );
    }

} 