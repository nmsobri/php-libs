<?php

class Request
{
    
    protected $session;

    public function __construct( Session $session )
    {
        $this->session = $session;
    }

    public function isPost()
    {
        return isset( $_POST );
    }

    public function isGet()
    {
        return isset( $_GET );
    }

    public function isAjax()
    {
        return (!empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) ? true : false;
    }

    public function postToGet()
    {
        /*quickly delete session data iff this GET data don exist (make session only available on this page and only when GET data is exist)*/
        if ( !$_GET[ 'post' ] ) 
        {
            unset( $_SESSION[ $_SERVER[ 'PHP_SELF' ] . 'POST' ] );
            unset( $_SESSION[ $_SERVER[ 'PHP_SELF' ] . 'FILES' ] );
        }

        if ( count( $_POST ) > 0 || count( $_FILES ) > 0 )
        {
            /*unique identification to this flash data*/
            $this->session->flash( $_SERVER[ 'PHP_SELF' ] . 'POST', $_POST ); 
            $this->session->flash( $_SERVER[ 'PHP_SELF' ] . 'FILES', $_FILES );
            $path = ( $_SERVER[ 'QUERY_STRING' ] != '' ) ? ( isset( $_GET[ 'post' ] ) ) ? $_SERVER[ 'REQUEST_URI' ] : $_SERVER[ 'REQUEST_URI' ] . '&post=t'  : $_SERVER[ 'REQUEST_URI' ] . '?post=t';
            header( 'Location: ' . $path );
            exit();
        }
        elseif ( $this->session->check( $_SERVER[ 'PHP_SELF' ] . 'POST' ) || $this->session->check( $_SERVER[ 'PHP_SELF' ] . 'FILES' ) )
        {
            $_POST = $this->session->get( $_SERVER[ 'PHP_SELF' ] . 'POST' ); //just a convenience so validation object can acces to post data
            $_FILES = $this->session->get( $_SERVER[ 'PHP_SELF' ] . 'FILES' ); //just a convenience so we can acces upload file through $FILES
            $this->session->keepFlash( $_SERVER[ 'PHP_SELF' ] . 'POST' );
            $this->session->keepFlash( $_SERVER[ 'PHP_SELF' ] . 'FILES' );
            return true;
        }
        else
        {
            return false;
        }
    }

}

?>
