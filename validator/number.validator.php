<?php

class NumberValidator extends ValidatorStrategy
{

    /**
     * Validation for number field
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr
     * @param mixed $attr ['field']
     * @param bool $attr ['required']
     * @param int $attr ['decimal']
     * @param int $attr ['min_length']
     * @param int $attr ['max_length']
     * @param mixed $attr ['errors']['empty']
     * @param mixed $attr ['errors']['number']
     * @param mixed $attr ['errors']['number_fixed']
     * @param mixed $attr ['errors']['number_range']
     * @param mixed $attr ['errors']['number_decimal']
     * @param mixed $attr ['errors']['number_decimal_fixed']
     * @param mixed $attr ['errors']['number_decimal_range']
     *
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 2, 'max_length' => 0 ) ) check for number that length equal to 2
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 7, 'max_length' => 9 ) ) check for number that length in the range of 7-9
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 3, 'max_length' => 0 , 'decimal' => 2 ) ) check for number that length equal to 3 and have 2 decimal place (190.30)
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 2, 'max_length' => -1 ) ) check for number that length have at least 2 length and beyond (ignore the maxLength)
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
            return $this->checkRequired();
        }
        else{
            if( $this->data['min_length'] >= 1 && $this->data['max_length'] == 0 ){
                return $this->checkExactLength();
            }
            elseif( $this->data['min_length'] >= 1 && ( $this->data['max_length'] > $this->data['min_length'] ) ){
                return $this->checkVariableLength();
            }
            else{
                return $this->checkInfiniteLength();
            }
        }
    }


    /**
     * Check for field existence
     * @return bool
     */
    protected function checkRequired()
    {
        if( $this->data['required'] == true ){
            $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_EMPTY, array( $this->data['field'] ) );
            return false;
        }
        else{
            return true;
        }
    }


    /**
     * Check field for exact length
     * @return bool
     */
    protected function checkExactLength()
    {
        if( $this->data['decimal'] > 0 ){
            if( preg_match( '/^[0-9]{' . $this->data['min_length'] . '}\.[0-9]{' . $this->data['decimal'] . '}$/i', $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['number_decimal_fixed'] ) ? $this->data['errors']['number_decimal_fixed'] : $this->errorText( ValidatorStrategy::E_NUMBER_DECIMAL_FIXED, array( $this->data['field'], $this->data['decimal'], $this->data['min_length'] ) );
                return false;
            }
        }
        else{
            if( preg_match( '/^[0-9]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['number_fixed'] ) ? $this->data['errors']['number_fixed'] : $this->errorText( ValidatorStrategy::E_NUMBER_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
                return false;
            }
        }
    }


    /**
     * Check field for range of length
     * @return bool
     */
    protected function checkVariableLength()
    {
        if( $this->data['decimal'] > 0 ){
            if( preg_match( '/^[0-9]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}\.[0-9]{' . $this->data['decimal'] . '}$/i', $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['number_decimal_range'] ) ? $this->data['errors']['number_decimal_range'] : $this->errorText( ValidatorStrategy::E_NUMBER_DECIMAL_RANGE, array( $this->data['field'], $this->data['decimal'], $this->data['min_length'], $this->data['max_length'] ) );
                return false;
            }
        }
        else{
            if( preg_match( '/^[0-9]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['number_range'] ) ? $this->data['errors']['number_range'] : $this->errorText( ValidatorStrategy::E_NUMBER_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
                return false;
            }
        }
    }


    /**
     * Check field for infinite length
     * @return bool
     */
    protected function checkInfiniteLength()
    {
        if( $this->data['decimal'] > 0 ){
            if( preg_match( '/^[0-9]+\.[0-9]{' . $this->data['decimal'] . '}$/i', $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['number_decimal'] ) ? $this->data['errors']['number_decimal'] : $this->errorText( ValidatorStrategy::E_NUMBER_DECIMAL, array( $this->data['field'], $this->data['decimal'] ) );
                return false;
            }
        }
        else{
            if( preg_match( '/^[0-9]+$/i', $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['number'] ) ? $this->data['errors']['number'] : $this->errorText( ValidatorStrategy::E_NUMBER, array( $this->data['field'] ) );
                return false;
            }
        }
    }


    /**
     * @param $name
     * @param $value
     * @param array $attr
     */
    protected function configValidator( $name, $value, $attr )
    {
        parent::configValidator( $name, $value, $attr );
        $this->configLength( @$attr['length'] );
        $this->data['decimal'] = ( array_key_exists( 'decimal', $attr ) ) ? $attr['decimal'] : 0;
    }


    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'number' => null, 'number_decimal' => null,
            'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
            'number_decimal_range' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }
        else{
            return $cfg;
        }
    }


}