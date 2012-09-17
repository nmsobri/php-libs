<?php

/**
 *
 * Session Class
 * @author slier
 */
class Session
{

    /**
     *
     * @var array
     * @access private
     */
    private $_sess;




    /**
     *
     * Constructor method
     * @access public
     * @param string $sessionLimiter
     * @param int $sessionExpired
     *
     */
    public function __construct( $sessionLimiter = 'nocache', $sessionExpired = 30 )
    {
        $this->setSessionLimiter( $sessionLimiter );
        $this->setSessionExpired( $sessionExpired );
        $this->startSession();
        $this->initFlashData();
    }




    /**
     * Free all session variables
     * @access public
     */
    public function unsetSession()
    {
        session_unset();
    }




    /**
     * Destroys A Session And All Data Registered To A Session
     * @access public
     */
    public function destroySession()
    {
        session_destroy();
    }




    /**
     * Get The Current Session Id
     * @access public
     */
    public function getSessionId()
    {
        return session_id();
    }




    /**
     *
     * Regenerate Session Id
     * @access public
     */
    public function regenerateSessionId()
    {
        session_regenerate_id();
    }




    /**
     *
     * Setting session data
     * Produce $_SESSION[$name]=$value
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function set( $name, $value )
    {
        $this->_sess[ $name ] = $value;
    }




    /**
     * 
     * Set session so that only exist on one request
     * @access public
     * @param mixed $keys
     * @param mixed $val
     */
    public function flash( $key, $val )
    {
        $_SESSION[ 'flash' ][ $key ] = 'new';
        $this->set( $key, $val );
    }




    /**
     * 
     * Set session so it keep exist on only for next request
     * If this method is call without parameter,all flash session var will be keep for next request
     * @access public
     * @param mixed $keys
     */
    public function keepFlash( $keys = null )
    {
        $keys = ( $keys === null ) ? array_keys( $_SESSION[ 'flash' ] ) : func_get_args();

        foreach ( $keys as $key )
        {
            if ( isset( $_SESSION[ 'flash' ][ $key ] ) )
            {
                $_SESSION[ 'flash' ][ $key ] = 'new';
            }
        }
    }




    /**
     *
     * Delete Session Variable
     * @access public
     * @param string $name
     */
    public function del( $name )
    {
        unset( $this->_sess[ $name ] );
    }




    /**
     *
     * Get Session Variable
     * @access public
     * @param mixed $name
     * @return mixed
     */
    public function get( $name )
    {
        if ( isset( $this->_sess[ $name ] ) )
        {
            return $this->_sess[ $name ];
        }
    }




    /**
     *
     * Check For Existence Of Session Variable
     * @access public
     * @param mixed $name
     * @return bool
     */
    public function check( $name )
    {
        return isset( $this->_sess[ $name ] ) ? true : false;
    }




    /**
     *
     * Get All Session List
     * @access public
     * @return array
     */
    public function getSessionList()
    {
        return array_keys( $this->_sess );
    }




    /**
     *
     * Get Total Count Of Session Variable
     * @access public
     * @return int
     */
    public function getSessionCount()
    {
        $i = 0;
        if ( is_dir( $this->_path ) )
        {
            if ( $dir = opendir( $this->_path ) )
            {
                while ( false !== ( $file = readdir( $dir ) ) )
                {
                    // cek apakah prefix dari nama = sess_ dan yang pasti size nya > 0
                    if ( eregi( "sess_", $file ) )
                    {
                        $i++;
                    }
                }
            }
        }
        return $i;
    }




    /**
     *
     * Start the session
     * @access private
     */
    private function startSession()
    {
        session_start();
        $this->_sess = &$_SESSION;
    }




    /**
     * 
     * Set how long cached page in client cache should be store
     * Dosent affect session lifetime
     * Use in conjunction with session_cache_limiter != 'nocache'
     * @access private
     * @param mixed $delay
     */
    private function setSessionExpired( $delay )
    {
        session_cache_expire( $delay );
    }




    /**
     *
     * Set session cache limiter wether to permit client to cache page content or not
     * @possible value public, private, private_no_expire, nocache
     * @access private
     * @param mixed $limit
     */
    private function setSessionLimiter( $limit )
    {
        session_cache_limiter( $limit );
    }




    /**
     *
     * Init session flash data
     * @access private
     */
    private function initFlashData()
    {
        if ( !isset( $_SESSION[ 'flash' ] ) )
        {
            $_SESSION[ 'flash' ] = array( );
        }
        $this->expireFlash();
    }




    /**
     * Expire flash session data
     * @access private
     */
    private function expireFlash()
    {
        static $run;

        //Method can only be run once
        if ( $run === TRUE )
            return;

        if ( !empty( $_SESSION[ 'flash' ] ) )
        {
            foreach ( $_SESSION[ 'flash' ] as $key => $state )
            {
                if ( $state === 'old' )
                {
                    #Flash has expired
                    unset( $_SESSION[ 'flash' ][ $key ], $_SESSION[ $key ] );
                }
                else
                {
                    #Mark it old,so it expire in next request
                    $_SESSION[ 'flash' ][ $key ] = 'old';
                }
            }
        }

        #Method has been run
        $run = TRUE;
    }




    /**
     *
     * Redirect User
     * @access public
     * @param mixed $path
     */
    public function redirect( $path )
    {
        header( 'Location: ' . $path );
        exit();
    }




}

?>