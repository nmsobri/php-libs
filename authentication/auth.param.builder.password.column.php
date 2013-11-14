<?php

namespace utility\authentication;

class AuthParamBuilderPasswordColumn
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
     * Set db password column to check against provided password
     *
     * @param $password_column
     * @return AuthParamBuilderBuildRemember
     */
    public function setPasswordcolumn( $password_column )
    {
        $this->auth_param->setPasswordColumn( $password_column );
        return new AuthParamBuilderBuildRemember( $this->auth_param );
    }

}
