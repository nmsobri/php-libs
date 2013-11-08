<?php

abstract class ValidatorStrategy
{
    const E = 'Please correct following errors:';
    const E_EMPTY = 'Field %s is empty';
    const E_NOT_MATCH = 'Field %s does not match field %s';
    const E_INVALID_DATE = 'Field %s contains invalid date.Only date formatted as %s are allowed';
    const E_INVALID_EMAIL = 'Field %s contains invalid email';
    const E_INVALID_CHARACTER = 'Field %s contain invalid character';
    const E_NOT_CHECK = 'Checkbox %s is not checked';
    const E_NOT_SELECT = 'Selection %s is not selected';
    const E_NOT_MARK = 'Radio %s is not marked';
    const E_TEXT_NUMBER_SPACE_FIXED = 'Field %s contains error.Only text, number, space and length equal to %s are allowed';
    const E_TEXT_SPACE_FIXED = 'Field %s contains error.Only text, space and length equal to %s are allowed';
    const E_TEXT_FIXED = 'Field %s contains error.Only text and length equal to %s are allowed';
    const E_TEXT_NUMBER_FIXED = 'Field %s contains error.Only text, number and length equal to %s are allowed';
    const E_TEXT_SPACE_RANGE = 'Field %s contains error.Only text, space and length between %s and %s are allowed';
    const E_TEXT_NUMBER_SPACE_RANGE = 'Field %s contains error.Only text, number, space and length between %s and %s are allowed';
    const E_TEXT_RANGE = 'Field %s contains error.Only text and length between %s and %s are allowed';
    const E_TEXT_NUMBER_RANGE = 'Field %s contains error.Only text, number and length between %s and %s are allowed';
    const E_TEXT_NUMBER_SPACE = 'Field %s contains error.Only text, number and space are allowed';
    const E_TEXT_NUMBER = 'Field %s contains error.Only text and number are allowed';
    const E_TEXT_SPACE = 'Field %s contains error.Only text and space are allowed';
    const E_TEXT = 'Field %s contains error.Only text are allowed';
    const E_NUMBER_FIXED = 'Field %s contains error.Only number without decimal places and length equal to %s are allowed';
    const E_NUMBER_RANGE = 'Field %s contains error.Only number without decimal places and length between %s and %s are allowed';
    const E_NUMBER_DECIMAL_FIXED = 'Field %s contains error.Only number with %s decimal places and length equal to %s are allowed';
    const E_NUMBER_DECIMAL_RANGE = 'Field %s contains error.Only number with %s decimal places and length between %s and %s are allowed';
    const E_NUMBER_DECIMAL = 'Field %s contains error.Only number with %s decimal places are allowed';
    const E_NUMBER = 'Field %s contains error.Only numbers without decimal places are allowed';
    const E_FILE_EMPTY = 'File %s is empty';
    const E_INVALID_EXTENSION = 'File %s contains error.Only file with extension %s are allowed';


    /**
     * Store validation name
     * @var string
     */
    protected $name = null;


    /**
     * Store element attributes
     * @var string
     */
    protected $data = array();


    /**
     * Store error msg
     * @var string
     */
    protected $messages = null;


    /**
     * Abstract function, implemented in child class
     */
    public abstract function isValid();


    /**
     * Abstract function, implemented in child class
     */
    protected abstract function configErrors( array $attr );


    /**
     * Get error message
     * @return array|null
     */
    public function getMessage()
    {
        return $this->messages;
    }


    /**
     * Set error message
     * @param string $message
     * @return void
     */
    public function setMessage( $message )
    {
        $this->messages = $message;
    }


    /**
     * @param $error
     * @param array $attr
     * @return string
     */
    protected function errorText( $error, array $attr = null )
    {
        return vsprintf( $error, $attr );
    }


    /**
     * @param $name
     * @param $value
     * @param array $attr
     */
    protected function configValidatorGenericAttr( $name, $value, array $attr )
    {
        $data = array(
            'value' => $value,
            'errors' => $this->configErrors( $attr ),
            'required' => isset( $attr['required'] ) ? (bool)$attr['required'] : true,
            'field' => isset( $attr['field'] ) ? $attr['field'] : $name
        );
        $this->data = $data;
    }
}