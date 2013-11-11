<?php

namespace utility\authentication;

use utility\Database;

Class AuthParam extends AuthParamPrivate
{

    /**
     * @var Database resource
     */
    protected $database = null;

    /**
     * @var string
     */
    protected $query = null;

    /**
     * @var array
     */
    protected $query_bind = null;

    /**
     * @var string
     */
    protected $password = null;

    /**
     * @var string
     */
    protected $hash_column = null;

    /**
     * @var boolean
     */
    protected $remember = null;


    protected function __construct()
    {

    }


    /**
     * @param Database $db
     */
    public function setDatabase( Database $db )
    {
        $this->database = $db;
    }


    /**
     * Set query
     *
     * @param $query
     * @param array $bind
     *
     * query = "SELECT * FROM users WHERE username=?"
     * bind = array($username)
     *
     * query = "SELECT * FROM users WHERE username=:username"
     * bind = array(':username'=>$username)
     */
    public function setQuery( $query, array $bind = null )
    {
        $this->query = $query;
        $this->query_bind = $bind ? : [ ];
    }


    /**
     * Set password
     *
     * @param $password
     */
    public function setPassword( $password )
    {
        $this->password = $password;
    }


    /**
     * Set db password column name
     *
     * @param $hash_column
     */
    public function setPasswordColumn( $hash_column )
    {
        $this->hash_column = $hash_column;
    }


    /**
     * Set persistent login
     *
     * @param $remember
     */
    public function setRemember( $remember )
    {
        $this->remember = (bool)$remember;
    }


    /**
     * Get databse
     *
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }


    /***
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * Get query bind
     *
     * @return array
     */
    public function getQueryBind()
    {
        return $this->query_bind;
    }


    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * Get db password column name
     *
     * @return string
     */
    public function getPasswordColumn()
    {
        return $this->hash_column;
    }


    /**
     * Get remember status
     * @return null
     */
    public function getRemember()
    {
        return $this->remember;
    }
}