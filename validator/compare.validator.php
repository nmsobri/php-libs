<?php

namespace utility\validator;

class CompareValidator extends ValidatorStrategy
{

    /**
     * Validation for comparing two entity for equality
     *
     * @param string $name
     * @param mixed $value
     * @param string $comparisonValue
     * @param string $comparisonField
     * @param array $attr
     *
     * bool $attr['required']
     * string $attr['field']
     * string $attr['errors']['empty']
     * string $attr['errors']['equal']
     *
     * new CompareValidator( 'repeat_pass' , $_POST['repeat_password'], $_POST['password'], 'Password' )
     */
    public function __construct( $name, $value, $comparisonValue, $comparisonField, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidatorGenericAttr( $name, $value, $comparisonValue, $comparisonField, $attr );
    }


    /**
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

        if( $this->data['value'] != $this->data['compare_value'] ){
            $this->messages = ( $this->data['errors']['equal'] ) ? $this->data['errors']['equal'] : $this->errorText( ValidatorStrategy::E_NOT_MATCH, array( $this->data['field'], $this->data['compare_field'] ) );
            return false;
        }
        return true;
    }


    /**
     * @param $name
     * @param $value
     * @param array $comparisonValue
     * @param $comparisonField
     * @param $attr
     */
    protected function configValidatorGenericAttr( $name, $value, $comparisonValue, $comparisonField, $attr )
    {
        parent::configValidatorGenericAttr( $name, $value, $attr );
        $this->data['compare_value'] = $comparisonValue;
        $this->data['compare_field'] = $comparisonField;
    }


    /**
     * @param array $attr
     * @return array
     */
    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'equal' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}