<?php

class Request
{

    /**
     *
     * @var Session
     */
    protected $session;


    /**
     * @param Session $session
     */
    public function __construct( Session $session )
    {
        $this->session = $session;
    }


    /**
     * Check either post data exist
     * @return bool
     */
    public function isPost()
    {
        return $_POST;
    }


    /**
     * Check either get data exist
     * @return bool
     */
    public function isGet()
    {
        return $_GET;
    }


    /**
     * Check either an ajax request
     * @return bool
     */
    public function isAjax()
    {
        return ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
    }


    /**
     * Convert post request to a get request
     * @throws Exception if upload file size exceed php.ini upload_max_filesize
     * @return bool
     */
    public function postToGet()
    {
        #Quickly delete session data if this $_GET data do not exist (make session only available on this page and only when GET data is exist)
        if ( !@$_GET['post'] ) {
            unset( $_SESSION[$_SERVER['PHP_SELF'] . 'POST'] );
            unset( $_SESSION[$_SERVER['PHP_SELF'] . 'FILES'] );
        }

        if ( count( $_POST ) > 0 || count( $_FILES ) > 0 ) {
            #Unique identification to this flash data
            $this->session->flash( $_SERVER['PHP_SELF'] . 'POST', $_POST );
            $this->session->flash( $_SERVER['PHP_SELF'] . 'FILES', $_FILES );
            $path = ( $_SERVER['QUERY_STRING'] != '' ) ? ( isset( $_GET['post'] ) ) ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'] . '&post=t' : $_SERVER['REQUEST_URI'] . '?post=t';
            header( 'Location: ' . $path );
            exit;
        }
        elseif ( $this->session->check( $_SERVER['PHP_SELF'] . 'POST' ) || $this->session->check( $_SERVER['PHP_SELF'] . 'FILES' ) ) {
            $_POST = $this->session->get( $_SERVER['PHP_SELF'] . 'POST' ); //just a convenience so validation object can acces to post data
            $_FILES = $this->session->get( $_SERVER['PHP_SELF'] . 'FILES' ); //just a convenience so we can acces upload file through $FILES
            $this->session->keepFlash( $_SERVER['PHP_SELF'] . 'POST' );
            $this->session->keepFlash( $_SERVER['PHP_SELF'] . 'FILES' );
            return true;
        }
        elseif ( intval( @$_SERVER['CONTENT_LENGTH'] ) > 0 && count( $_POST ) === 0 ) {
            #change this to save $_SESSION data and return false, then check in the else block of existence of the $_SESSION data for proper error message instead of Exception
            $this->session->flash( 'upload_error', true );
            return false;
        }
        else {
            return false;
        }
    }


}

?>
