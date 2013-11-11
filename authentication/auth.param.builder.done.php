<?php

namespace utility\authentication;

class AuthParamBuilderDone
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
     * @return $this
     */
    public function setRemember( $remember )
    {
        $this->auth_param->setRemember( $remember );
        return $this;
    }


    /**
     * Completing build the AuthParam object
     *
     * @return AuthParam
     */
    public function build()
    {
        return $this->auth_param;
    }

}

