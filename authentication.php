<?php

/*
 * Class to authenticate a user
 * @author slier
 *
 */

namespace utility;

class Authentication
{

    /**
     * Store Db Object
     * @var Database
     */
    private $db = null;


    /**
     * Store Session Object
     * @var Session
     */
    private $session = null;


    /**
     * Store Cookie Object
     * @var Cookie
     */
    private $cookie = null;


    /**
     * Store stdClass object using for login
     * @var StdClass
     */
    private $login_param = null;


    /**
     * Store result from querying the database
     * @var mixed
     */
    private $login_data = null;


    /**
     * Default Session And Cookie Name For Grouping Purpose
     * @var string
     */
    private $auth_name = 'auth';


    /**
     * Store auth data from database;
     * @var mixed
     */
    private $auth_data = null;


    /**
     * Hash for encryption/decryption
     * @var string
     */
    private $encryption_key = '9D(v56xv0_7F15m8Y$ZuSQ5FG1#Mx^';


    /**
     * Hash for auth checking
     * @var string
     */
    private $hash = '6cSNeQPq8qkcWzUBUe/LF1wPyC3iKJpO';


    /**
     * Construction Method
     * @access public
     * @param Session $session
     * @param Cookie $cookie
     * @return Authentication
     */
    public function __construct( Session $session, Cookie $cookie )
    {
        $this->session = $session;
        $this->cookie = $cookie;
    }


    /**
     * @param Pdo $db
     * @param stdClass $obj
     * @param stdClass::query query to run
     * @param stdClass::bind array|single value to bind to placeholder inside query
     * @param stdClass::remember whether to use cookie to store auth result
     * @return boolean
     *
     * @example
     * $obj->query = "SELECT * FROM users WHERE username=? AND password=?"
     * $obj->bind = array($username, $password)
     *
     * @example
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
     * Check whether user is authenticate
     * @return boolean
     */
    public function isAuth()
    {
        if ( !is_null( $this->login_param ) ) {
            $this->login_data = $this->db->query( $this->login_param->query, $this->bind() )->execute();
            @list( $this->login_data ) = $this->login_data;

            if ( count( $this->login_data ) > 0 ) {
                $this->saveHash( $this->login_data );
                return true;
            }
        }
        else {
            if ( $this->session->check( $this->auth_name ) ) {
                return $this->checkHash();
            }

            if ( $this->cookie->check( $this->auth_name ) ) {
                if ( $this->checkHash() ) {
                    $this->session->set( $this->auth_name, $this->cookie->get( $this->auth_name ) );
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get auth data from session
     * @return mixed
     */
    public function getAuthData()
    {
        if ( $this->auth_data == null ) {
            $this->checkHash();
        }

        return $this->auth_data;
    }


    /**
     * Update/change auth data
     * Auth data usually is row from database
     * @example Auth data = ['id'=>1, 'level'=>'admin', 'dept'=>'hr']
     * @param str $key key in auth data
     * @param mixed $val
     * @return void
     */
    public function updateAuthData( $key, $val )
    {
        if ( $this->auth_data == null ) {
            $this->checkHash();
        }

        if ( array_key_exists( $key, $this->auth_data ) ) {
            $this->auth_data[$key] = $val;
            $this->saveHash( $this->auth_data );
        }
    }


    /**
     * Return login data
     * Typically database data
     * @return mixed
     */
    public function getLoginData()
    {
        return $this->login_data;
    }


    /**
     * Store hash for encryption/decryption
     * @param string $hash
     * @return void
     */
    public function setEncryptionKey( $hash )
    {
        $this->encryption_key = $hash;
    }


    /**
     * Store hash for auth checking
     * @param string $hash
     * @return void
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

        if ( !$this->session->check( $this->auth_name ) and !$this->cookie->check( $this->auth_name ) ) return true;
        else
            return false;
    }


    /**
     * Bind a value to Pdo placeholder
     * @return mixed
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
     * Save hash auth data to session/cookie
     * @param mixed $data
     * @return void
     */
    protected function saveHash( $data )
    {
        $this->session->set( $this->auth_name, $this->createHash( $data ) );

        if ( @$this->login_param->remember ) {
            $this->cookie->set( $this->auth_name, $this->createHash( $data ) );
        }
    }

    /**
     * Create hashing for auth data
     * @param mixed $data
     * @return mixed
     */
    protected function createHash( $data )
    {
        $auth_data = array();

        $auth_data['key'] = $this->encode( serialize( $data ) );
        $auth_data['hash'] = $this->encode( strrev( $this->hash . $auth_data['key'] . $this->hash ) );

        return $auth_data;
    }


    /**
     * Check auth hash from session/cookie against recreation hash
     * @return boolean
     */
    protected function checkHash()
    {
        if ( $this->session->check( $this->auth_name ) ) {
            $auth_data = $this->session->get( $this->auth_name );
            $auth_key = $auth_data['key'];
            $auth_hash = strrev( $this->decode( $auth_data['hash'] ) );

            if ( $this->hash . $auth_key . $this->hash == $auth_hash ) {
                $this->auth_data = unserialize( $this->decode( $auth_key ) );
                return true;
            }
        }

        if ( $this->cookie->check( $this->auth_name ) ) {
            $auth_data = $this->cookie->get( $this->auth_name );
            $auth_key = $auth_data['key'];
            $auth_hash = strrev( $this->decode( $auth_data['hash'] ) );

            if ( $this->hash . $auth_key . $this->hash == $auth_hash ) {
                $this->session->set( $this->auth_name, $this->cookie->get( $this->auth_name ) );
                $this->auth_data = unserialize( $this->decode( $auth_key ) );
                return true;
            }
        }
        $this->auth_data = null;
        return false;
    }


    /**
     * Encode string
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