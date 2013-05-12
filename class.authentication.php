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
    private $db = null;


    /**
     * Store Session Object
     * @var Session
     * @access private
     */
    private $session = null;


    /**
     * Store Cookie Object
     * @var Cookie
     * @access private
     */
    private $cookie = null;


    /**
     * Store stdClass object using for login
     * @access private
     * @var StdClass
     */
    private $login_param = null;


    /**
     * Store result from querying the database
     * @access private
     */
    private $login_data = null;


    /**
     * Default Session And Cookie Name For Grouping Purpose
     * @var string
     * @access private
     */
    private $auth_name = 'auth';


    /**
     * Store hash for encryption/decryption
     * @var type
     */
    private $encryption_key = '9D(v56xv0_7F15m8Y$ZuSQ5FG1#Mx^';


    /**
     * Store hashing, for auth checking
     * @access private
     * @var string
     */
    private $hash = '6cSNeQPq8qkcWzUBUe/LF1wPyC3iKJpO';


    /**
     * Construction Method
     * @access public
     * @param Session $session instance of session class
     * @param Cookie $cookie instance of cookie class
     * @return Authentication
     */
    public function __construct( Session $session, Cookie $cookie )
    {
        $this->session = $session;
        $this->cookie = $cookie;
    }


    /**
     *
     * @access public
     * @param Pdo $db Pdo object
     * @param stdClass $obj       instance of stdClass
     * @param stdClass::query     query to run
     * @param stdClass::bind      array|single value to bind to placeholder inside query
     * @param stdClass::remember  whether to use cookie to store auth result
     * @return boolean
     *
     * @example1
     * $obj->query = "SELECT * FROM users WHERE username=? AND password=?"
     * $obj->bind = array($username, $password)

     * @example2
     * $obj->query = "SELECT * FROM users WHERE username=:age AND password=:password"
     * $obj->bind = array(':username'=>$username, ':password'=>$password)
     */
    public function login( PDO $db, stdClass $obj )
    {
        $this->db = $db;
        $this->login_param = $obj;

        if ( !isset( $this->login_param->bind ) ) {
            $this->login_param->bind = array();
        }

        if ( !isset( $this->login_param->remember ) ) {
            $this->login_param->remember = false;
        }

        return $this->isAuth();
    }


    /**
     * Check wether is authenticated
     * @access public
     * @return boolean
     */
    public function isAuth()
    {
        if ( !is_null( $this->login_param ) ) {
            $this->login_data = $this->db->query( $this->login_param->query, $this->bind() )->execute();

            if ( count( $this->login_data ) == 1 ) {
                $this->saveHash();
                return true;
            }
        } else {
            if ( $this->session->check( $this->auth_name ) and $this->checkHash() ) {
                return true;
            }

            if ( $this->cookie->check( $this->auth_name ) and $this->checkHash() ) {
                $this->session->set( $this->auth_name, $this->cookie->get( $this->auth_name ) );
                return true;
            }
        }
        return false;
    }


    /**
     * Return login data
     * Typically database data
     * @access public
     * @return mixed
     */
    public function getLoginData()
    {
        return $this->login_data;
    }


    /**
     * Store hash for encryption/decryption
     * @access public
     * @param string $hash
     */
    public function setEncryptionKey( $hash )
    {
        $this->encryption_key = $hash;
    }


    /**
     * Store hash for auth checking
     * @access public
     * @param type $hash
     */
    public function setHashKey( $hash )
    {
        $this->hash = $hash;
    }


    /**
     * Method To Logout User
     * @access public
     * @return boolean
     */
    public function logout()
    {
        $this->session->delete( $this->auth_name );
        $this->session->unsetSession();
        $this->cookie->delete( $this->auth_name );

        if ( !$this->session->check( $this->auth_name ) and !$this->cookie->check( $this->auth_name ) ) return true; else
            return false;
    }


    /**
     * Bind a value to Pdo placeholder
     * @access protected
     * @return array
     */
    protected function bind()
    {
        $bind = NULL;

        if ( is_array( $this->login_param->bind ) ) {
            $bind = $this->login_param->bind;
        }
        else {
            $bind = array( $this->login_param->bind );
        }

        return $bind;
    }


    /**
     * Create hashing for auth data
     * @access protected
     * @return array
     */
    protected function createHash()
    {
        $auth_key = '';

        foreach ( $this->login_data[0] as $data ) {
            $auth_key .= $data;
        }

        $data = array();
        $data['key'] = $this->encode( $auth_key );
        $data['hash'] = $this->encode( strrev( $this->hash . $auth_key . $this->hash ) );

        return $data;
    }


    /**
     * Save hash auth data to session/cookie
     * @access protected
     * @return void
     */
    protected function saveHash()
    {

        $this->session->set( $this->auth_name, $this->createHash() );

        if ( $this->login_param->remember ) {
            $this->cookie->set( $this->auth_name, $this->createHash() );
        }
    }


    /**
     * Check auth hash from session/cookie against recreation hash
     * @access protected
     * @return boolean
     */
    protected function checkHash()
    {

        if ( $this->session->check( $this->auth_name ) ) {
            $auth_data = $this->session->get( $this->auth_name );
            $auth_key = $this->decode( $auth_data['key'] );
            $auth_hash = strrev( $this->decode( $auth_data['hash'] ) );

            if ( $this->hash . $auth_key . $this->hash == $auth_hash ) {
                return true;
            }
        }

        if ( $this->cookie->check( $this->auth_name ) and $this->checkHash() ) {
            $auth_data = $this->cookie->get( $this->auth_name );
            $auth_key = $this->decode( $auth_data['key'] );
            $auth_hash = strrev( $this->decode( $auth_data['hash'] ) );

            if ( $this->hash . $auth_key . $this->hash == $auth_hash ) {
                $this->session->set( $this->auth_name, $this->cookie->get( $this->auth_name ) );
                return true;
            }
        }
        return false;
    }


    /**
     * Encode string
     * @access protected
     * @param string $string
     * @return string
     */
    protected function encode( $string )
    {
        $encrypted = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $this->encryption_key ), $string, MCRYPT_MODE_CBC, md5( md5( $this->encryption_key ) ) ) );
        return $encrypted;
    }


    /**
     * Decode string
     * @param string $string
     * @return string
     */
    public function decode( $string )
    {
        $decrypted = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $this->encryption_key ), base64_decode( $string ), MCRYPT_MODE_CBC, md5( md5( $this->encryption_key ) ) ), "\0" );
        return $decrypted;
    }


}


?>