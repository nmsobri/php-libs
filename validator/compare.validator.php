<?php

class CompareValidator extends ValidatorStrategy
{

    /**
     * Validation to compare two field for equality
     * @param string $name
     * @param mixed $value
     * @param string $comparisonValue
     * @param string $comparisonField
     * @param bool $attr ['required']
     * @param string $attr ['field']
     * @param string $attr ['compare_field']
     * @param string $attr ['errors']['empty']
     * @param string $attr ['errors']['equal']
     *
     * @example new CompareValidator( 'repeat_pass' , $_POST['repeat_password'], $_POST['password'], 'Password', array( 'empty_message' => 'Repeat password is empty', 'unmatch_message' => 'Repeat password dosent match password' ) )
     */
    public function __construct( $name, $value, $comparisonValue, $comparisonField, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidator( $name, $value, $comparisonValue, $comparisonField, $attr );
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
    protected function configValidator( $name, $value, $comparisonValue, $comparisonField, $attr )
    {
        parent::configValidator( $name, $value, $attr );
        $this->data['compare_value'] = $comparisonValue;
        $this->data['compare_field'] = ( array_key_exists( 'compare_field', $attr ) ) ? $attr['compare_field'] : $comparisonField;
    }


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