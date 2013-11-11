<?php

/*
 * Class to authenticate a user
 * @author slier
 *
 */

namespace utility;

use utility\authentication\AuthParam;

class Authentication
{

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
     * Store utility\authentication AuthParam object using for login
     * @var AuthParam
     */
    private $login = null;


    /**
     * Store result from querying the database
     * @var mixed
     */
    private $login_result = null;


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
     * Attempt login
     *
     * @param AuthParam $login
     *
     * (new AuthParamBuilder())->setDatabase( $db )
     * ->setQuery( 'SELECT * FROM users WHERE username=?', [ $_POST['username'] ] )
     * ->setPassword( $_POST['password'] )
     * ->setPasswordcolumn( 'password' )->build();
     *
     * @return boolean
     */
    public function login( AuthParam $login )
    {
        $this->login = $login;
        return $this->isAuth();
    }


    /**
     * Check whether user is authenticate
     *
     * @return boolean
     */
    public function isAuth()
    {
        if( !is_null( $this->login ) ){
            return $this->doLogin();
        }

        if( $this->session->check( $this->auth_name ) ){
            return $this->checkHash();
        }

        if( $this->cookie->check( $this->auth_name ) ){
            if( $this->checkHash() ){
                $this->session->set( $this->auth_name, $this->cookie->get( $this->auth_name ) );
                return true;
            }
        }

        return false;
    }


    /**
     * Get auth data (query result from database)
     *
     * @return mixed
     */
    public function getAuthData()
    {
        if( is_null( $this->auth_data ) ){

            if( !is_null( $this->login_result ) ){
                return $this->login_result;
            }
            $this->checkHash();
        }
        return $this->auth_data;
    }


    /**
     * Edit auth data
     * Auth data is a row from database represent a user
     * Auth data = ['id'=>1, 'level'=>'admin', 'dept'=>'hr']
     *
     * @param string $key key in auth data
     * @param mixed $val
     * @return void
     */
    public function updateAuthData( $key, $val )
    {
        if( $this->auth_data == null ){
            $this->checkHash();
        }

        if( array_key_exists( $key, $this->auth_data ) ){
            $this->auth_data[$key] = $val;
            $this->saveHash( $this->auth_data );
        }
    }


    /**
     * Hash plain password (mostly use in register to hash password to store on db)
     * @param $password
     * @return bool|string
     */
    public function hashPassword( $password )
    {
        return password_hash( $password, PASSWORD_DEFAULT );
    }


    /**
     * Store hash for encryption/decryption
     *
     * @param string $hash
     * @return void
     */
    public function setEncryptionKey( $hash )
    {
        $this->encryption_key = $hash;
    }


    /**
     * Store hash for auth checking
     *
     * @param string $hash
     * @return void
     */
    public function setHashKey( $hash )
    {
        $this->hash = $hash;
    }


    /**
     * Method To Logout User
     *
     * @access public
     * @return boolean
     */
    public function logout()
    {
        $this->session->delete( $this->auth_name );
        $this->session->unsetSession();
        $this->cookie->delete( $this->auth_name );

        if( !$this->session->check( $this->auth_name ) and !$this->cookie->check( $this->auth_name ) )
            return true;
        else
            return false;
    }


    /**
     * Attempt login
     *
     * @return bool
     * @throws \Exception
     */
    protected function doLogin()
    {
        $this->login_result = $this->login->getDatabase()->query( $this->login->getQuery(), $this->login->getQueryBind() )->execute();

        @list( $this->login_result ) = $this->login_result; #flatten the array

        if( count( $this->login_result ) > 0 && !isset( $this->login_result[$this->login->getPasswordColumn()] ) ){
            throw new \Exception( sprintf( 'Column %s does not exist in the query %s', $this->login->getPasswordColumn(), $this->login->getQuery() ) );
        }

        if( count( $this->login_result ) > 0 ){
            if( password_verify( $this->login->getPassword(), @$this->login_result[$this->login->getPasswordColumn()] ) ){
                $this->saveHash( $this->login_result );
                return true;
            }
        }
        return false;
    }


    /**
     * Save hash auth data to session/cookie
     *
     * @param mixed $data
     * @return void
     */
    protected function saveHash( $data )
    {
        $this->session->set( $this->auth_name, $this->createHash( $data ) );
        if( $this->login->getRemember() ){
            $this->cookie->set( $this->auth_name, $this->createHash( $data ) );
        }
    }


    /**
     * Create hashing for auth data
     *
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
     *
     * @return boolean
     */
    protected function checkHash()
    {
        if( $this->session->check( $this->auth_name ) ){
            $auth_data = $this->session->get( $this->auth_name );
            $auth_key = $auth_data['key'];
            $auth_hash = strrev( $this->decode( $auth_data['hash'] ) );

            if( $this->hash . $auth_key . $this->hash == $auth_hash ){
                $this->auth_data = unserialize( $this->decode( $auth_key ) );
                return true;
            }
        }

        if( $this->cookie->check( $this->auth_name ) ){
            $auth_data = $this->cookie->get( $this->auth_name );
            $auth_key = $auth_data['key'];
            $auth_hash = strrev( $this->decode( $auth_data['hash'] ) );

            if( $this->hash . $auth_key . $this->hash == $auth_hash ){
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