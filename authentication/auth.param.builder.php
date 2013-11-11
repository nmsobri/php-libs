<?php

namespace utility\authentication;

use utility\Database;

class AuthParamBuilder extends AuthParamPrivate
{

    /**
     * @var null|AuthParam
     */
    protected $auth_param = null;


    public function __construct()
    {
        $this->auth_param = new AuthParam();
    }


    /**
     * Set db to query for user data
     *
     * @param Database $db
     * @return AuthParamBuilderQuery
     */
    public function setDatabase( Database $db )
    {
        $this->auth_param->setDatabase( $db );
        return new AuthParamBuilderQuery( $this->auth_param );
    }
}

?>