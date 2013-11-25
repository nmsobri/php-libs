<?php

namespace utility\validator;

/**
 * Class RadioValidator
 * @package utility\validator
 */
class RadioValidator extends ValidatorStrategy
{

    /**
     * Validation for radio
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * bool $attr['required']
     * string $attr['field']
     * string $attr['errors']['empty']
     *
     * new RadioValidator( 'gender', $_POST['gender'] )
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
            $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_NOT_MARK, array( $this->data['field'] ) );
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

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}