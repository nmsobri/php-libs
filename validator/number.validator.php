<?php

class NumberValidator extends AlnumValidatorStrategy
{
    /**
     * Validation for number
     *
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr
     *
     * bool $attr['required']
     * string $attr['field']
     * int $attr['decimal']
     * int|array $attr['length']
     * string $attr['errors']['empty'] error if empty
     * string $attr['errors']['number'] error if not whole number
     * string $attr['errors']['number_fixed'] error if not whole number or have exact length
     * string $attr['errors']['number_range'] error if not whole number or not length not in between range
     * string $attr['errors']['number_decimal'] error if not floating point
     * string $attr['errors']['number_decimal_fixed'] error if not floating point or have exact length
     * string $attr['errors']['number_decimal_range'] error if not floating point or length not in between range
     *
     * new NumberValidator( 'name', $_POST['salary'], array( 'length'=>10) ) check for text that length equal to 10
     * new NumberValidator( 'name', $_POST['salary'], array( 'length'=>array('min'=>10)) ) check for text that length equal to 10
     * new NumberValidator( 'name', $_POST['salary'], array( 'length'=>array(3,10) ) ) check for text that length in between 3 and 10
     * new NumberValidator( 'name', $_POST['salary'], array( 'length'=>array( 'min'=>3,'max'=> 10) ) ) check for text that length between 3 and 10
     * new NumberValidator( 'name', $_POST['salary'], array( 'length'=>array( 'max'=> 8) ) ) check for text that length in between 1 and 8
     * new NumberValidator( 'name', $_POST['salary'], array( 'length'=>array(5,7), 'allow_num' => true ) ) check for text that length between 5 to 7 and can contain number
     */
    public function __construct( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidatorGenericAttr( $name, $value, $attr );
    }


    /**
     * @return bool
     */
    public function isValid()
    {
        if( empty( $this->data['value'] ) ){
            return $this->checkRequired();
        }

        if( $this->data['min_length'] >= 1 && $this->data['max_length'] == 0 ){
            return $this->checkFixNumber();
        }

        if( $this->data['min_length'] >= 1 && ( $this->data['max_length'] > $this->data['min_length'] ) ){
            return $this->checkRangeNumber();
        }

        return $this->checkNumber();
    }


    /**
     * @return bool
     */
    protected function checkRequired()
    {
        if( $this->data['required'] ){
            $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_EMPTY, array( $this->data['field'] ) );
            return false;
        }
        return true;
    }


    /**
     * @return bool
     */
    protected function checkFixNumber()
    {
        if( $this->data['decimal'] > 0 ){
            if( preg_match( '/^[0-9]{' . $this->data['min_length'] . '}\.[0-9]{' . $this->data['decimal'] . '}$/i', $this->data['value'] ) ){
                return true;
            }
            $this->messages = ( $this->data['errors']['number_decimal_fixed'] ) ? $this->data['errors']['number_decimal_fixed'] : $this->errorText( ValidatorStrategy::E_NUMBER_DECIMAL_FIXED, array( $this->data['field'], $this->data['decimal'], $this->data['min_length'] ) );
            return false;
        }

        if( preg_match( '/^[0-9]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['number_fixed'] ) ? $this->data['errors']['number_fixed'] : $this->errorText( ValidatorStrategy::E_NUMBER_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkRangeNumber()
    {
        if( $this->data['decimal'] > 0 ){
            if( preg_match( '/^[0-9]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}\.[0-9]{' . $this->data['decimal'] . '}$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['number_decimal_range'] ) ? $this->data['errors']['number_decimal_range'] : $this->errorText( ValidatorStrategy::E_NUMBER_DECIMAL_RANGE, array( $this->data['field'], $this->data['decimal'], $this->data['min_length'], $this->data['max_length'] ) );
            return false;
        }

        if( preg_match( '/^[0-9]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['number_range'] ) ? $this->data['errors']['number_range'] : $this->errorText( ValidatorStrategy::E_NUMBER_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkNumber()
    {
        if( $this->data['decimal'] > 0 ){
            if( preg_match( '/^[0-9]+\.[0-9]{' . $this->data['decimal'] . '}$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['number_decimal'] ) ? $this->data['errors']['number_decimal'] : $this->errorText( ValidatorStrategy::E_NUMBER_DECIMAL, array( $this->data['field'], $this->data['decimal'] ) );
            return false;
        }

        if( preg_match( '/^[0-9]+$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['number'] ) ? $this->data['errors']['number'] : $this->errorText( ValidatorStrategy::E_NUMBER, array( $this->data['field'] ) );
        return false;
    }


    /**
     * @param $name
     * @param $value
     * @param array $attr
     */
    protected function configValidatorGenericAttr( $name, $value, $attr )
    {
        parent::configValidatorGenericAttr( $name, $value, $attr );
        $this->configValidatorLengthAttr( @$attr['length'] );
        $this->data['decimal'] = isset( $attr['decimal'] ) ? $attr['decimal'] : 0;
    }


    /**
     * @param array $attr
     * @return array
     */
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

        return $cfg;
    }


}