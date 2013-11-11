<?php

namespace utility\authentication;

class AuthParamBuilderQuery
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
     * Set query to run against db to get user data
     *
     * @param $query
     * @param $bind
     * @return AuthParamBuilderPassword
     */
    public function setQuery( $query, $bind )
    {
        $this->auth_param->setQuery( $query, $bind );
        return new AuthParamBuilderPassword( $this->auth_param );
    }

}