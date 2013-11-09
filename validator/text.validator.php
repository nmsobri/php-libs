<?php

class TextValidator extends AlnumValidatorStrategy
{

    /**
     * Validation for text
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * bool $attr ['required']
     * string $attr ['field']
     * bool $attr ['allow_num']
     * bool $attr ['allow_space']
     * int|array $attr ['length']
     * string $attr['errors']['empty'] error if empty
     * string $attr['errors']['text'] eror if not text
     * string $attr['errors']['text_fixed'] error if not text or have exact length
     * string $attr['errors']['text_range'] error if not text or length not in between range
     * string $attr['errors']['text_number'] error if not text or number
     * string $attr['errors']['text_space'] error if not text or space
     * string $attr['errors']['text_number_fixed'] error if not text or number or have exact length
     * string $attr['errors']['text_space_fixed'] error if not text or space or have exact length
     * string $attr['errors']['text_number_range'] error if not text or number or length not in between range
     * string $attr['errors']['text_space_range'] error if not text or space or length not in between range
     * string $attr['errors']['text_number_space'] error if not text or number or space
     * string $attr['errors']['text_number_space_fixed'] error if not test or number or space or have exact length
     * string $attr['errors']['text_number_space_range'] error if not text or number or space or length in between range
     *
     * new TextValidator( 'name', $_POST['name'], array( 'length'=>10) ) check for text that length equal to 10
     * new TextValidator( 'name', $_POST['name'], array( 'length'=>array('min'=>10)) ) check for text that length equal to 10
     * new TextValidator( 'name', $_POST['name'], array( 'length'=>array(3,10) ) ) check for text that length in between 3 and 10
     * new TextValidator( 'name', $_POST['name'], array( 'length'=>array( 'min'=>3,'max'=> 10) ) ) check for text that length between 3 and 10
     * new TextValidator( 'name', $_POST['name'], array( 'length'=>array( 'max'=> 8) ) ) check for text that length in between 1 and 8
     * new TextValidator( 'name', $_POST['name'], array( 'length'=>array(5,7), 'allow_num' => true ) ) check for text that length between 5 to 7 and can contain number
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
            return $this->checkFixText();
        }

        if( $this->data['min_length'] >= 1 && ( $this->data['max_length'] > $this->data['min_length'] ) ){
            return $this->checkRangeText();
        }

        return $this->checkText();
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
    protected function checkFixText()
    {
        if( $this->data['allow_num'] ){
            return $this->checkFixTextWithNumber();
        }

        return $this->checkFixTextWithoutNumber();
    }


    /**
     * @return bool
     */
    protected function checkRangeText()
    {
        if( $this->data['allow_num'] ){
            return $this->checkRangeTextWithNumber();
        }

        return $this->checkRangeTextWithoutNumber();
    }


    /**
     * @return bool
     */
    protected function checkText()
    {
        if( $this->data['allow_num'] ){
            return $this->checkTextWithNumber();
        }

        return $this->checkTextWithoutNumber();
    }


    /**
     * @return bool
     */
    protected function checkFixTextWithNumber()
    {
        if( $this->data['allow_space'] ){
            if( preg_match( '/^[a-zA-Z0-9\s]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['text_number_space_fixed'] ) ? $this->data['errors']['text_number_space_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_SPACE_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
            return false;
        }

        if( preg_match( '/^[a-zA-Z0-9]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['text_number_fixed'] ) ? $this->data['errors']['text_number_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkFixTextWithoutNumber()
    {
        if( $this->data['allow_space'] ){
            if( preg_match( '/^[a-zA-Z\s]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['text_space_fixed'] ) ? $this->data['errors']['text_space_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_SPACE_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
            return false;
        }

        if( preg_match( '/^[a-zA-Z]{' . $this->data['min_length'] . '}$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['text_fixed'] ) ? $this->data['errors']['text_fixed'] : $this->errorText( ValidatorStrategy::E_TEXT_FIXED, array( $this->data['field'], $this->data['min_length'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkRangeTextWithNumber()
    {
        if( $this->data['allow_space'] ){
            if( preg_match( '/^[a-zA-Z0-9\s]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['text_number_space_range'] ) ? $this->data['errors']['text_number_space_range'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_SPACE_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
            return false;
        }

        if( preg_match( '/^[a-zA-Z0-9]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['text_number_range'] ) ? $this->data['errors']['text_number_range'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkRangeTextWithoutNumber()
    {
        if( $this->data['allow_space'] ){
            if( preg_match( '/^[a-zA-Z\s]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['text_space_range'] ) ? $this->data['errors']['text_space_range'] : $this->errorText( ValidatorStrategy::E_TEXT_SPACE_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
            return false;
        }

        if( preg_match( '/^[a-zA-Z]{' . $this->data['min_length'] . ',' . $this->data['max_length'] . '}$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['text_range'] ) ? $this->data['errors']['text_range'] : $this->errorText( ValidatorStrategy::E_TEXT_RANGE, array( $this->data['field'], $this->data['min_length'], $this->data['max_length'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkTextWithNumber()
    {
        if( $this->data['allow_space'] ){
            if( preg_match( '/^[a-zA-Z0-9\s]+$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['text_number_space'] ) ? $this->data['errors']['text_number_space'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER_SPACE, array( $this->data['field'] ) );
            return false;
        }

        if( preg_match( '/^[a-zA-Z0-9]+$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['text_number'] ) ? $this->data['errors']['text_number'] : $this->errorText( ValidatorStrategy::E_TEXT_NUMBER, array( $this->data['field'] ) );
        return false;
    }


    /**
     * @return bool
     */
    protected function checkTextWithoutNumber()
    {
        if( $this->data['allow_space'] ){

            if( preg_match( '/^[a-zA-Z\s]+$/i', $this->data['value'] ) ){
                return true;
            }

            $this->messages = ( $this->data['errors']['text_space'] ) ? $this->data['errors']['text_space'] : $this->errorText( ValidatorStrategy::E_TEXT_SPACE, array( $this->data['field'] ) );
            return false;
        }

        if( preg_match( '/^[a-zA-Z]+$/i', $this->data['value'] ) ){
            return true;
        }

        $this->messages = ( $this->data['errors']['text'] ) ? $this->data['errors']['text'] : $this->errorText( ValidatorStrategy::E_TEXT, array( $this->data['field'] ) );
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
        $this->data['allow_num'] = isset( $attr['allow_num'] ) ? ( boolean )$attr['allow_num'] : false;
        $this->data['allow_space'] = isset( $attr['allow_space'] ) ? ( boolean )$attr['allow_space'] : false;
    }


    /**
     * @param array $attr
     * @return array
     */
    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'text' => null, 'text_fixed' => null, 'text_range' => null,
            'text_number' => null, 'text_space' => null, 'text_number_fixed' => null,
            'text_space_fixed' => null, 'text_number_range' => null, 'text_space_range' => null,
            'text_number_space' => null, 'text_number_space_fixed' => null, 'text_number_space_range' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}
