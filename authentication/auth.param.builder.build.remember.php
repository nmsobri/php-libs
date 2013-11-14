<?php

namespace utility\authentication;

/**
 * Class AuthParamBuilderBuildRemember
 * @package utility\authentication
 */
class AuthParamBuilderBuildRemember
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
     * Set for persistent login
     *
     * @param $remember
     * @return AuthParamBuilderDone
     */
    public function setRemember( $remember )
    {
        $this->auth_param->setRemember( $remember );
        return new AuthParamBuilderDone( $this->auth_param );
    }


    /**
     * Completing build the AuthParam object
     */
    public function build()
    {
        return $this->auth_param;
    }

}

