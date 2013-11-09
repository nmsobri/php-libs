<?php

namespace utility\validator;

class SelectValidator extends ValidatorStrategy
{
    /**
     * Validation for select
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * bool $attr['required']
     * string $attr['field']
     * string $attr['errors']['empty']
     *
     * new SelectValidator( 'country' , $_POST['country'] )
     */
    public function __construct( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidatorGenericAttr( $name, $value, $attr );
    }


    /**
     * Perform validation
     * @return bool
     */
    public function isValid()
    {
        if( empty( $this->data['value'] ) ){
            if( $this->data['required'] ){
                $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_NOT_SELECT, array( $this->data['field'] ) );
                return false;
            }
        }
        return true;
    }


    /**
     * @param array $attr
     * @return array
     */
    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}