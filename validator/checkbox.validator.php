<?php

namespace utility\validator;

/**
 * Class CheckboxValidator
 * @package utility\validator
 */
class CheckboxValidator extends ValidatorStrategy
{

    /**
     * Validation for checkbox
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * bool $attr['required']
     * string $attr['field']
     * string $attr['errors']['empty']
     *
     * new CheckBoxValidator( 'subscribe' , $_POST['subscribe'] )
     */
    public function __construct( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidatorGenericAttr( $name, $value, $attr );
    }


    /**
     * Perform validation
     *
     * @return bool
     */
    public function isValid()
    {
        if( isset( $this->data['value'] ) ){
            return true;
        }

        if( $this->data['required'] ){
            $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_NOT_CHECK, array( $this->data['field'] ) );
            return false;
        }

        return true;
    }


    /**
     * Config validator error attr
     *
     * @param array $attr
     * @return array
     */
    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null
        );

        if( isset( $attr['errors'] ) && is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}