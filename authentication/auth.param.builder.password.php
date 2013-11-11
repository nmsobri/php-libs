<?php

namespace utility\authentication;

class AuthParamBuilderPassword
{

    /**
     * @var null|AuthParam
     */
    protected $auth_param = null;


    /**
     * @param AuthParam $auth_param
     */
    public function __construct( AuthParam $auth_param )
    {
        $this->auth_param = $auth_param;
    }


    /**
     * Set password to check against hash password on the db
     *
     * @param $password
     * @return AuthParamBuilderPasswordColumn
     */
    public function setPassword( $password )
    {
        $this->auth_param->setPassword( $password );
        return new AuthParamBuilderPasswordColumn( $this->auth_param );
    }

}