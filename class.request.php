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
     * @throws Exception if upload file size exceed php.ini post_max_size
     * @return bool
     */
    public function isPost()
    {
        if ( intval( @$_SERVER['CONTENT_LENGTH'] ) > 0 && count( $_POST ) === 0 ) {
            #change this to save $_SESSION data and return false, then check in the else block of existence of the $_SESSION data for proper error message instead of Exception
            throw new Exception( 'PHP discarded POST data because of request exceeding post_max_size.' );
        }
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
     * @throws Exception if upload file size exceed php.ini post_max_size
     * @return bool
     */
    public function postToGet()
    {
        if ( $_FILES ) {
            return $this->isPost();
        }

        #Quickly delete session data if this $_GET data do not exist (make session only available on this page and only when GET data is exist)
        if ( !@$_GET['post'] ) {
            unset( $_SESSION[$_SERVER['PHP_SELF'] . 'POST'] );
        }

        if ( count( $_POST ) > 0 || count( $_FILES ) > 0 ) {
            #Unique identification to this flash data
            $this->session->flash( $_SERVER['PHP_SELF'] . 'POST', $_POST );
            $path = ( $_SERVER['QUERY_STRING'] != '' ) ? ( isset( $_GET['post'] ) ) ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'] . '&post=t' : $_SERVER['REQUEST_URI'] . '?post=t';
            header( 'Location: ' . $path );
            exit;
        }
        elseif ( $this->session->check( $_SERVER['PHP_SELF'] . 'POST' ) || $this->session->check( $_SERVER['PHP_SELF'] . 'FILES' ) ) {
            $_POST = $this->session->get( $_SERVER['PHP_SELF'] . 'POST' ); //just a convenience so validation object can acces to post data
            $this->session->keepFlash( $_SERVER['PHP_SELF'] . 'POST' );
            return true;
        }
        elseif ( intval( @$_SERVER['CONTENT_LENGTH'] ) > 0 && count( $_POST ) === 0 ) {
            #change this to save $_SESSION data and return false, then check in the else block of existence of the $_SESSION data for proper error message instead of Exception
            throw new Exception( 'PHP discarded POST data because of request exceeding post_max_size.' );
        }
        else {
            return false;
        }
    }


}

?>
