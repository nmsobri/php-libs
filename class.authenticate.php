<?php

/*
 * Class to authenticate a user
 * @author slier
 * 
 */

class Authentication
{

    /**
     * Store Db Object
     * @var Crud
     * @access private
     */
    private $db = false;

    /**
     * Store Session Object
     * @var Session
     * @access private
     */
    private $ses = false;

    /**
     * Store Cookie Object
     * @var Cookie
     * @access private
     */
    private $cookie = false;

    /**
     * Store Table Name Used For Authentication
     * @var string
     * @access private
     */
    private $table = null;

    /**
     * Default Session And Cookie Name For Grouping Purpose
     * @var string
     * @access private
     */
    private $cookSesName = 'auth';

    /**
     * Cached passed in username
     * @var string
     * @var access private
     */
    private $username = null;

    /**
     * Cached passed in password
     * @var string
     * @var access private
     */
    private $password = null;

    /**
     * Store Username Coulmn In The Database
     * @var string
     * @access private
     */
    public $usernameField = 'username';

    /**
     * Store Password Column In The Database
     * @var string
     * @access private
     */
    public $passwordField = 'password';

    /**
     * Construction Method
     * @access public
     * @param Crud $db instance of crud class
     * @param Session $ses instance of session class
     * @param Cookie $cookie instance of cookie class
     * @param mixed $tableName table name use to store user credential
     * @return Authentication
     */
    public function __construct( Crud $db, Session $ses, Cookie $cookie, $tableName )
    {
        $this->db = $db;
        $this->ses = $ses;
        $this->cookie = $cookie;
        $this->table = $tableName;
    }

    /**
     * Method To Perform Login And Then Call isAuthenticated To Verify Wether User Who Try To Login is Authenticated
     * @access public
     * @param mixed $username
     * @param mixed $password
     * @param mixed $rememberMe
     * @return boolean
     */
    public function login( $username, $password, $rememberMe = false )
    {
        $this->username = $username;
        $this->password = $password;

        if ( $this->isAuthenticated() )
        {
            $userId = $this->db->query( "Select id from users where username='{$this->username}'" )->execute();
            $userId = $userId[ 0 ][ 'id' ];

            $this->ses->regenerateSessionId();
            $this->ses->set( $this->cookSesName, array( 'userId' => $userId, 'username' => $username, 'password' => $password ) );

            if ( $rememberMe )
            {
                $cookieData = serialize( array( 'username' => $username, 'password' => $password ) );  /* Need to serialize cause cookie cant store an array (aka cast it to string) */
                $this->cookie->set( $this->cookSesName, $cookieData );
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Method To Check Wether User Is Verified To Access Resource
     * @access public
     * @return boolean
     */
    public function isAuthenticated()
    {
        if ( $this->cookie->check( $this->cookSesName ) ) //come from cookie
        {
            if ( !$this->ses->check( $this->cookSesName ) )
            {
                $cookieData = $this->cookie->get( $this->cookSesName );
                $cookieData = unserialize( $cookieData );
                $this->ses->set( $this->cookSesName, $cookieData );
            }
        }

        if ( $this->ses->check( $this->cookSesName ) ) //come from session
        {
            $sessionData = $this->ses->get( $this->cookSesName );
            $username = $sessionData[ 'username' ];
            $password = $sessionData[ 'password' ];
        }

        if ( $this->username && $this->password ) //come from login form
        {
            $username = $this->username;
            $password = $this->password;
        }

        if ( !$username and !$password )
        {
            return false; //return false in none of the above variable dosent exist
        }

        $recordFound = $this->db->select( $this->table )->where( "{$this->usernameField}='{$username}' and {$this->passwordField}='{$password}'" )->total_row()->execute();

        if ( $recordFound == 1 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Method To Logout User
     * @access public
     * @return boolean
     */
    public function logout()
    {
        $this->ses->del( $this->cookSesName );
        $this->ses->unsetSession();
        $this->cookie->del( $this->cookSesName );

        if ( !$this->ses->check( $this->cookSesName ) and !$this->cookie->check( $this->cookSesName ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}

?>