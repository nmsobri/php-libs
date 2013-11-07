<?php

class EmailValidator extends ValidatorStrategy
{

    /**
     * Validation for email
     * @param string $name
     * @param mixed $value
     * @param mixed $attr
     * @param bool $attr ['required']
     * @param string $attr ['errors']['empty']
     * @param string $attr ['errors']['email']
     * @param mixed $attr ['field']
     *
     * @example new EmailValidator( 'email', $_POST['email'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidator( $name, $value, $attr );
    }


    /**
     * Perform validation
     * @return bool
     */
    public function isValid()
    {
        if( empty( $this->data['value'] ) ){
            if( $this->data['required'] ){
                $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_EMPTY, array( $this->data['field'] ) );
                return false;
            }
            return true;
        }

        if( !preg_match( "#^[^\W\d](?:\w+)(?:\.\w+|\-\w+)*@(?:\w+)(\.[a-z]{2,6})+$#i", $this->data['value'] ) ){
            $this->messages = ( $this->data['errors']['email'] ) ? $this->data['errors']['email'] : $this->errorText( ValidatorStrategy::E_INVALID_EMAIL, array( $this->data['field'] ) );
            return false;
        }

        return true;
    }


    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'email' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}