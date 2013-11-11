<?php
/**
 * Class AuthParam
 * Just to make sure class AuthParam is not directly instantiate
 * Only instantiable through class AuthParamBuilder
 */

namespace utility\authentication;

abstract class AuthParamPrivate
{

    abstract protected function __construct();
}