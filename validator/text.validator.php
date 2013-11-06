<?php

class TextValidator extends ValidatorStrategy
{

    /**
     * Validation for text field
     * @param string $name
     * @param mixed $value
     * @param mixed $attr ['field']
     * @param string $attr ['required']
     * @param string $attr ['allow_num']
     * @param string $attr ['allow_space']
     * @param string $attr ['min_length']
     * @param string $attr ['max_length']
     * @param string $attr ['errors']['empty']
     * @param string $attr ['errors']['text']
     * @param string $attr ['errors']['text_fixed']
     * @param string $attr ['errors']['text_range']
     * @param string $attr ['errors']['text_number']
     * @param string $attr ['errors']['text_space']
     * @param string $attr ['errors']['text_number_fixed']
     * @param string $attr ['errors']['text_space_fixed']
     * @param string $attr ['errors']['text_number_range']
     * @param string $attr ['errors']['text_space_range']
     * @param string $attr ['errors']['text_number_space']
     * @param string $attr ['errors']['text_number_space_fixed']
     * @param string $attr ['errors']['text_number_space_range']
     *
     * @example new TextValidator( 'name', $_POST['name'], array( 'min_length' => 10, 'max_length' => 0 ) ) check for text that length equal to 10
     * @example new TextValidator( 'name', $_POST['name'], array( 'min_length' => 3, 'max_length' => 10 ) ) check for text that length in the range of 3-10
     * @example new TextValidator( 'name', $_POST['name'], array( 'min_length' => 3, 'max_length' => 10, 'allow_num' => true ) ) check for text that length equal to 10 and can contain number in it
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
     * Check field for existence
     * @return bool
     */
    protected function checkRequired()
    {
        if( $this->data['required'] ){
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
        if( $this->data['allow_num'] ){
            if( $this->data['allow_space'] ){
                if( preg_match( '/^[a-zA-Z0-9\s]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_number_space_fixed'] ) ? $this->data['errors']['text_number_space_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_SPACE_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
                    return false;
                }
            }
            else{
                if( preg_match( '/^[a-zA-Z0-9]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_number_fixed'] ) ? $this->data['errors']['text_number_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
                    return false;
                }
            }
        }
        else{
            if( $this->data['allow_space'] ){
                if( preg_match( '/^[a-zA-Z\s]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_space_fixed'] ) ? $this->data['errors']['text_space_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_SPACE_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
                    return false;
                }
            }
            else{
                if( preg_match( '/^[a-zA-Z]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_fixed'] ) ? $this->data['errors']['text_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
                    return false;
                }
            }
        }
    }


    /**
     * Check field for range of length
     * @return bool
     */
    protected function checkVariableLength()
    {
        if( $this->data['allow_num'] ){
            if( $this->data['allow_space'] ){
                if( preg_match( '/^[a-zA-Z0-9\s]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_number_space_range'] ) ? $this->data['errors']['text_number_space_range'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_SPACE_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
                    return false;
                }
            }
            else{
                if( preg_match( '/^[a-zA-Z0-9]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_number_range'] ) ? $this->data['errors']['text_number_range'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
                    return false;
                }
            }
        }
        else{
            if( $this->data['allow_space'] ){
                if( preg_match( '/^[a-zA-Z\s]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_space_range'] ) ? $this->data['errors']['text_space_range'] : $this->errorText( ValidatorStrategy::E_TEXT_SPACE_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
                    return false;
                }
            }
            else{
                if( preg_match( '/^[a-zA-Z]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_range'] ) ? $this->data['errors']['text_range'] : $this->errorText( ValidatorStrategy::E_TEXT_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
                    return false;
                }
            }
        }
    }


    /**
     * Check field for infinite length
     * @return bool
     */
    protected function checkInfiniteLength()
    {
        if( $this->data['allow_num'] ){
            if( $this->data['allow_space'] ){
                if( preg_match( '/^[a-zA-Z0-9\s]+$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_number_space'] ) ? $this->data['errors']['text_number_space'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_SPACE, array( $this->data['field'] ) );
                    return false;
                }
            }
            else{
                if( preg_match( '/^[a-zA-Z0-9]+$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_number'] ) ? $this->data['errors']['text_number'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER, array( $this->data['field'] ) );
                    return false;
                }
            }
        }
        else{
            if( $this->data['allow_space'] ){

                if( preg_match( '/^[a-zA-Z\s]+$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text_space'] ) ? $this->data['errors']['text_space'] : $this->errorText( ValidatorStrategy::E_TEXT_SPACE, array( $this->data['field'] ) );
                    return false;
                }
            }
            else{
                if( preg_match( '/^[a-zA-Z]+$/i', $this->data['value'] ) ){
                    return true;
                }
                else{
                    $this->messages = ( $this->data['errors']['text'] ) ? $this->data['errors']['text'] : $this->errorText( ValidatorStrategy::E_TEXT, array( $this->data['field'] ) );
                    return false;
                }
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
        $this->data['allow_num'] = ( array_key_exists( 'allow_num', $attr ) ) ? ( boolean )$attr['allow_num'] : false;
        $this->data['allow_space'] = ( array_key_exists( 'allow_space', $attr ) ) ? ( boolean )$attr['allow_space'] : false;
    }


    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'text' => null,
            'text_fixed' => null, 'text_range' => null,
            'text_number' => null, 'text_space' => null,
            'text_number_fixed' => null, 'text_space_fixed' => null,
            'text_number_range' => null, 'text_space_range' => null,
            'text_number_space' => null, 'text_number_space_fixed' => null,
            'text_number_space_range' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }
        else{
            return $cfg;
        }
    }


}
