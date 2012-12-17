<?php

abstract class ValidatorStrategy
{

    /**
     *
     * Store validation name
     * @access protected
     * @var string
     */
    protected $name = null;



    /**
     *
     * Store form attributes
     * @access protected
     * @var string
     */
    protected $data = array( );



    /**
     *
     * Store error msg
     * @access protected
     * @var string
     */
    protected $messages = null;



    /**
     *
     * Abstract function, implemented in child class
     */
    public abstract function isValid();

    /**
     *
     * Get error message
     * @access public
     */
    public function getMessage()
    {
        return (!is_null( $this->messages ) ) ? $this->messages : null;
    }



    /**
     *
     * Set error message
     * @access public
     */
    public function setMessage( $field, $message )
    {
        $this->messages = ( $message == null ) ? ( $this->data[ 'field' ] ) ? $this->errorText( 3, $this->data[ 'field' ] ) : $this->errorText( 3, $field )  : $message;
    }



    /**
     *
     * Method use to return appropriate error message
     * @param int $num
     * @param string $field
     * @param string $fieldname2
     * @return string of message
     */
    protected function errorText( $num, $field = null, $fieldname2 = null )
    {
        $msg[ 0 ] = 'Please correct following errors:';
        $msg[ 1 ] = 'Field ' . $field . ' is empty.';
        $msg[ 2 ] = 'Field ' . $field . ' does not match field  ' . $fieldname2 . '.';
        $msg[ 3 ] = 'Field ' . $field . ' contains error.';
        $msg[ 4 ] = 'Date in field ' . $field . ' is invalid.';
        $msg[ 5 ] = 'Email in field ' . $field . ' is invalid.';
        $msg[ 6 ] = 'Field ' . $field . ' is invalid.';
        $msg[ 7 ] = 'Field ' . $field . ' is too long.';
        $msg[ 8 ] = 'Url in field ' . $field . ' is invalid.';
        $msg[ 9 ] = 'Html code detected in field ' . $field . '.';
        $msg[ 10 ] = 'Field ' . $field . ' does not match required length.';
        $msg[ 11 ] = 'Checkbox ' . $field . ' is not marked.';
        $msg[ 12 ] = 'Selection ' . $field . ' is not selected.';
        $msg[ 13 ] = 'Radio ' . $field . ' is not marked.';
        $msg[ 14 ] = 'Field ' . $field . ' cannot contain numbers.';
        $msg[ 15 ] = 'Field ' . $field . ' does not match required length or contain numbers.';
        $msg[ 16 ] = 'Field  ' . $field . ' can only contain numbers.';
        $msg[ 17 ] = 'Field  ' . $field . ' does not match required length or contain texts.';
        $msg[ 18 ] = 'Field ' . $field . ' does not match required length or does not contain valid decimal place or contain texts.';
        $msg[ 19 ] = 'Field ' . $field . ' cannot contain spaces';
        $msg[ 20 ] = 'Field ' . $field . ' can only contain texts and spaces';
        $msg[ 21 ] = 'Field ' . $field . ' can only have texts';
        return $msg[ $num ];
    }



}




class CheckBoxValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for checkbox field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param str $attr['message'] custom error message
     * @param str $attr['field'] use to donate field name in error message instead of using POST key data
     * @example new CheckBoxValidator( 'subcribe' , $_POST['subscribe'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'checkbox';
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for checkbox
     * @access protected
     * @param mixed $name
     * @param mixed $value
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( isset( $this->data[ 'value' ] ) )
        {
            return true;
        }
        else
        {
            $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 11, $this->data[ 'field' ] );
            return false;
        }
    }



}




class CompareValidator extends ValidatorStrategy
{

    /**
     *
     * Validation to compare two field for equality
     * @access public
     * @param mixed $name name of the field
     * @param mixed $value value of the field
     * @param mixed $attr['field_comparison'] comparison field name
     * @param bool $attr['required']
     * @param mixed $attr['empty_message'] error message if field is empty
     * @param mixed $attr['unmatch_message'] - error message if this field is not equal with comparison field
     * @param mixed $attr['field'] custom field name in error message ( @default $name )
     * @example new CompareValidator( 'new_pass' , $_POST['password'], $_POST['repeat_password'], 'Repeat Password', array( 'empty_message' => 'Repeat password is empty', 'unmatch_message' => 'Repeat password dosent match password' ) )
     */
    public function __construct( $name, $value, $compareValue, $comparsionField, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'to_compare' ] = $value;
        $this->data[ 'compare_with' ] = $compareValue;
        $this->data[ 'field_comparasion' ] = ( array_key_exists( 'field_comparison', $attr ) ) ? $attr[ 'field_comparison' ] : $comparsionField;
        $this->data[ 'type' ] = 'compare_field';
        $this->data[ 'required' ] = ( array_key_exists( 'required', $attr ) ) ? ( boolean ) $attr[ 'required' ] : true;
        $this->data[ 'empty_message' ] = ( array_key_exists( 'empty_message', $attr ) ) ? $attr[ 'empty_message' ] : null;
        $this->data[ 'unmatch_message' ] = ( array_key_exists( 'unmatch_message', $attr ) ) ? $attr[ 'unmatch_message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for comparing two field
     * @access protected
     * @param mixed $name
     * @param mixed $to_compare
     * @param mixed $compare_with
     * @param mixed $comparasionFieldName
     * @param str $this->data[ 'required' ]
     * @param mixed $this->data[ 'empty_message' ]
     * @param mixed $this->data[ 'unmatch_message' ]
     * @return boolean
     * @example to set custom msg through setCustomMsg()::setCustomMsg(array('fieldname'=>array('empty'=>'*','notMatch'=>'**')))
     * @info index 'empty' donate msg when field are empty and index 'notMatch' donate msg when comparing field are not macth
     */
    public function isValid()
    {
        if ( $this->data[ 'to_compare' ] == '' )
        {
            if ( $this->data[ 'required' ] == true )
            {
                $this->messages = ( $this->data[ 'empty_message' ] ) ? $this->data[ 'empty_message' ] : $this->errorText( 1, $this->data[ 'field' ] );
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if ( $this->data[ 'to_compare' ] != $this->data[ 'compare_with' ] )
            {
                $this->messages = ( $this->data[ 'unmatch_message' ] ) ? $this->data[ 'unmatch_message' ] : $this->errorText( 2, $this->data[ 'field' ], $this->data[ 'field_comparison' ] );
                return false;
            }
            else
            {
                return true;
            }
        }
    }



}




class DateValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for date field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param str $attr['version'] version of date
     * @param bool $attr['required'] is required
     * @param mixed $attr['message'] custom error message
     * @param mixed $attr['field'] custom filed name ( @default $name )
     * @example new DateValidator( 'dob', $_POST['dob'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'date';
        $this->data[ 'version' ] = ( array_key_exists( 'version', $attr ) ) ? $attr[ 'version' ] : 'us';
        $this->data[ 'required' ] = ( array_key_exists( 'required', $attr ) ) ? ( boolean ) $attr[ 'required' ] : true;
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for date
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param mixed $this->data[ 'version' ]
     * @param str $this->data[ 'required' ]
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( $this->data[ 'value' ] == '' )
        {
            if ( $this->data[ 'required' ] == true )
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 1, $this->data[ 'field' ] );
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if ( $this->data[ 'version' ] == 'eu' )
            {
                $pattern = "/^(0[1-9]|[1-2][0-9]|3[0-1])[-](0[1-9]|1[0-2])[-](19|20)[0-9]{2}$/";
            }
            else
            {
                $pattern = "/^(19|20)[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$/";
            }
            if ( preg_match( $pattern, $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 4, $this->data[ 'field' ] );
                return false;
            }
        }
    }



}




class EmailValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for email
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param bool $attr['required']
     * @param mixed $attr['message']
     * @param mixed $attr['field'] field name in error message ( @default $name )
     * @example new EmailValidator( 'email', $_POST['email'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'email';
        $this->data[ 'required' ] = ( array_key_exists( 'required', $attr ) ) ? ( boolean ) $attr[ 'required' ] : true;
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for email
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param str $this->data[ 'required' ]
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( $this->data[ 'value' ] == '' )
        {
            if ( $this->data[ 'required' ] == true )
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 1, $this->data[ 'field' ] );
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if ( preg_match( "/^[^\W\d](?:\w+)(?:\.\w+|\-\w+)*@(?:\w+)(\.[a-z]{2,6})+$/i", $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 5, $this->data[ 'field' ] );
                return false;
            }
        }
    }



}




class NumberValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for number field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param bool $attr['required']
     * @param int $attr['decimal']
     * @param int $attr['min_length']
     * @param int $attr['max_length']
     * @param mixed $attr['message']
     * @param mixed $attr['field'] field name in error message ( @default $name )
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 2, 'max_length' => 0 ) ) check for number that length equal to 2
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 7, 'max_length' => 9 ) ) check for number that length in the range of 7-9
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 3, 'max_length' => 0 , 'decimal' => 2 ) ) check for number that length equal to 3 and have 2 decimal place (190.30)
     * @example new NumberValidator( 'age', $_POST['age'], array( 'min_length' => 2, 'max_length' => -1 ) ) check for number that length have at least 2 length and beyond (ignore the maxLength)
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'number';
        $this->data[ 'required' ] = ( array_key_exists( 'required', $attr ) ) ? ( boolean ) $attr[ 'required' ] : true;
        $this->data[ 'decimal' ] = ( array_key_exists( 'decimal', $attr ) ) ? $attr[ 'decimal' ] : 0;
        $this->data[ 'min_length' ] = ( array_key_exists( 'min_length', $attr ) ) ? $attr[ 'min_length' ] : 0;
        $this->data[ 'max_length' ] = ( array_key_exists( 'max_length', $attr ) ) ? $attr[ 'max_length' ] : 0;
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for number
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param str $this->data[ 'required' ]
     * @param int $this->data[ 'decimal' ]
     * @param int $this->data[ 'min_length' ]
     * @param int $this->data[ 'max_length' ]
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( empty( $this->data[ 'value' ] ) )
        {
            $this->checkRequired();
        }
        else
        {
            if ( $this->data[ 'min_length' ] > 0 && $this->data[ 'max_length' ] == 0 ) /* if minLength > 0 and maxLength == 0, check for exact length match */
            {
                $this->checkExactLength();
            }
            elseif ( $this->data[ 'min_length' ] >= 0 && $this->data[ 'max_length' ] > 0 ) /* if minLength == 0 or minLength > 0 ,check for range of length */
            {
                $this->checkVariableLength();
            }
            else
            {
                $this->checkInfiniteLength();
            }
        }
    }



    protected function checkRequired()
    {
        if ( $this->data[ 'required' ] == true )
        {
            $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 1, $this->data[ 'field' ] );
            return false;
        }
        else
        {
            return true;
        }
    }



    protected function checkExactLength()
    {
        if ( $this->data[ 'decimal' ] > 0 )
        {
            if ( preg_match( '/^[0-9]{' . $this->data[ 'min_length' ] . '}\.[0-9]{' . $this->data[ 'decimal' ] . '}$/i', $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 18, $this->data[ 'field' ] );
                return false;
            }
        }
        else
        {
            if ( preg_match( '/^[0-9]{' . $this->data[ 'min_length' ] . '}$/i', $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 17, $this->data[ 'field' ] );
                return false;
            }
        }
    }



    protected function checkVariableLength()
    {
        if ( $this->data[ 'decimal' ] > 0 )
        {
            if ( preg_match( '/^[0-9]{' . $this->data[ 'min_length' ] . ',' . $this->data[ 'max_length' ] . '}\.[0-9]{' . $this->data[ 'decimal' ] . '}$/i', $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 18, $this->data[ 'field' ] );
                return false;
            }
        }
        else
        {
            if ( preg_match( '/^[0-9]{' . $this->data[ 'min_length' ] . ',' . $this->data[ 'max_length' ] . '}$/i', $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 10, $this->data[ 'field' ] );
                return false;
            }
        }
    }



    protected function checkInfiniteLength()
    {
        if ( $this->data[ 'decimal' ] > 0 )
        {
            if ( preg_match( '/^[0-9]+\.[0-9]{' . $this->data[ 'decimal' ] . '}$/i', $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 18, $this->data[ 'field' ] );
                return false;
            }
        }
        else
        {
            if ( preg_match( '/^[0-9]+$/i', $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 16, $this->data[ 'field' ] );
                return false;
            }
        }
    }



}




class RadioValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for radio field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr['message']
     * @param mixed $attr['field'] donate field name in error message ( @default $name )
     * @example new RadioValidator( 'gender', $_POST['gender'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'radio';
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for radio
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( isset( $this->data[ 'value' ] ) )
        {
            return true;
        }
        else
        {
            $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 13, $this->data[ 'field' ] );
            return false;
        }
    }



}




class RegexValidator extends ValidatorStrategy
{

    /**
     *
     * Validation to perform custom validation using regex
     * @access public
     * @param mixed $name
     * @param mixed $regex
     * @param bool $attr['required']
     * @param mixed $attr['message']
     * @param mixed $attr['field'] -use to donate field name in error message instead of using POST key data
     * @example new RegexValidator( 'gender', $_POST['gender'], '/[a-z]+$/', array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $regex, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'regex' ] = $regex;
        $this->data[ 'type' ] = 'regex';
        $this->data[ 'required' ] = ( array_key_exists( 'required', $attr ) ) ? ( boolean ) $attr[ 'required' ] : true;
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation using custom regex
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param mixed $this->data[ 'regex' ]
     * @param mixed $this->data[ 'required' ]
     * @param mixed $this->data[ 'message' ]
     */
    public function isValid()
    {
        if ( $this->data[ 'value' ] == '' and $this->data[ 'required' ] == true )
        {
            $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 1, $this->data[ 'field' ] );
            return false;
        }

        if ( $this->data[ 'value' ] == '' and $this->data[ 'required' ] != true )
        {
            return true; //simply return true cause we dont care if this field is empty or not
        }

        if ( $this->data[ 'value' ] != '' )
        {
            if ( preg_match( $this->data[ 'regex' ], $this->data[ 'value' ] ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 6, $this->data[ 'field' ] );
                return false;
            }
        }
    }



}




class SelectValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for select field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr['message']
     * @param str $attr['field'] donate field name in error message ( @default $name )
     * @example new SelectValidator( 'country' , $_POST['country'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'select';
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for select
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( !empty( $this->data[ 'value' ] ) )
        {
            return true;
        }
        else
        {
            $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 12, $this->data[ 'field' ] );
            return false;
        }
    }



}




class TextValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for text field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param str $attr['required']
     * @param str $attr['allow_num']
     * @param str $attr['allow_space']
     * @param int $attr['min_length']
     * @param int $attr['max_length']
     * @param str $attr['message']
     * @param str $attr['field'] donate field name in error message ( @default $name )
     * @example new TextValidator( 'name', $_POST['name'], array( 'min_length' => 10, 'max_length' => 0 ) ) check for text that length equal to 10
     * @example new TextValidator( 'name', $_POST['name'], array( 'min_length' => 3, 'max_length' => 10 ) ) check for text that length in the range of 3-10
     * @example new TextValidator( 'name', $_POST['name'], array( 'min_length' => 3, 'max_length' => 10, 'allow_num' => true ) ) check for text that length equal to 10 and can contain number in it
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $this->data[ 'value' ] = $value;
        $this->data[ 'type' ] = 'text';
        $this->data[ 'required' ] = ( array_key_exists( 'required', $attr ) ) ? ( boolean ) $attr[ 'required' ] : true;
        $this->data[ 'allow_num' ] = ( array_key_exists( 'allow_num', $attr ) ) ? ( boolean ) $attr[ 'allow_num' ] : false;
        $this->data[ 'allow_space' ] = ( array_key_exists( 'allow_space', $attr ) ) ? ( boolean ) $attr[ 'allow_space' ] : false;
        $this->data[ 'min_length' ] = ( array_key_exists( 'min_length', $attr ) ) ? $attr[ 'min_length' ] : 0;
        $this->data[ 'max_length' ] = ( array_key_exists( 'max_length', $attr ) ) ? $attr[ 'max_length' ] : 0;
        $this->data[ 'message' ] = ( array_key_exists( 'message', $attr ) ) ? $attr[ 'message' ] : null;
        $this->data[ 'field' ] = ( array_key_exists( 'field', $attr ) ) ? $attr[ 'field' ] : $name;
    }



    /**
     *
     * Validation for text field
     * @access protected
     * @param mixed $name
     * @param mixed $this->data[ 'value' ]
     * @param str $this->data[ 'required' ]
     * @param str $this->data[ 'allow_num' ]
     * @param int $this->data[ 'min_length' ]
     * @param int $this->data[ 'max_length' ]
     * @param mixed $this->data[ 'message' ]
     * @return boolean
     */
    public function isValid()
    {
        if ( empty( $this->data[ 'value' ] ) )
        {
            $this->checkrequired();
        }
        else
        {
            if ( $this->data[ 'min_length' ] > 0 && $this->data[ 'max_length' ] == 0 ) /* if minLength > 0 and maxLength == 0, check for exact length match */
            {
                $this->checkExactLength();
            }
            elseif ( $this->data[ 'min_length' ] >= 0 && $this->data[ 'max_length' ] > 0 ) /* if minLength == 0 or minLength > 0 ,check for range of length */
            {
                $this->checkVariableLength();
            }
            else /* we dont bother string length..just check wether we permit for num and space character */
            {
                $this->checkInfiniteLength();
            }
        }
    }



    protected function checkrequired()
    {
        if ( $this->data[ 'required' ] )
        {
            $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 1, $this->data[ 'field' ] );
            return false;
        }
        else
        {
            return true;
            /* we dont bother with validation */
        }
    }



    protected function checkExactLength()
    {

        if ( $this->data[ 'allow_num' ] )
        {
            if ( $this->data[ 'allow_space' ] )
            {
                if ( preg_match( '/^[a-zA-Z0-9\s]{' . $this->data[ 'min_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 10, $this->data[ 'field' ] );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z0-9]{' . $this->data[ 'min_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 10, $this->data[ 'field' ] );
                    return false;
                }
            }
        }
        else
        {
            if ( $this->data[ 'allow_space' ] )
            {
                if ( preg_match( '/^[a-zA-Z\s]{' . $this->data[ 'min_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 15, $this->data[ 'field' ] );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z]{' . $this->data[ 'min_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 15, $this->data[ 'field' ] );
                    return false;
                }
            }
        }
    }



    protected function checkVariableLength()
    {
        if ( $this->data[ 'allow_num' ] )
        {
            if ( $this->data[ 'allow_space' ] )
            {
                if ( preg_match( '/^[a-zA-Z0-9\s]{' . $this->data[ 'min_length' ] . ',' . $this->data[ 'max_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 10, $this->data[ 'field' ] );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z0-9]{' . $this->data[ 'min_length' ] . ',' . $this->data[ 'max_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 10, $this->data[ 'field' ] );
                    return false;
                }
            }
        }
        else
        {
            if ( $this->data[ 'allow_space' ] )
            {
                if ( preg_match( '/^[a-zA-Z\s]{' . $this->data[ 'min_length' ] . ',' . $this->data[ 'max_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 15, $this->data[ 'field' ] );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z]{' . $this->data[ 'min_length' ] . ',' . $this->data[ 'max_length' ] . '}$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 15, $this->data[ 'field' ] );
                    return false;
                }
            }
        }
    }



    protected function checkInfiniteLength()
    {
        if ( $this->data[ 'allow_num' ] )
        {
            if ( $this->data[ 'allow_space' ] )
            {
                if ( preg_match( '/^[a-zA-Z0-9\s]+$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 6, $this->data[ 'field' ] );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z0-9]+$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 19, $this->data[ 'field' ] );
                    return false;
                }
            }
        }
        else
        {
            if ( $this->data[ 'allow_space' ] )
            {

                if ( preg_match( '/^[a-zA-Z\s]+$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 20, $this->data[ 'field' ] );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z]+$/i', $this->data[ 'value' ] ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $this->data[ 'message' ] ) ? $this->data[ 'message' ] : $this->errorText( 21, $this->data[ 'field' ] );
                    return false;
                }
            }
        }
    }



}




class Validator
{

    /**
     *
     * Check status of validation
     * @access protected
     * @var bool
     */
    protected $isEror = false;



    /**
     *
     * Hold and array of ValidatorStrategy
     * @access protected
     * @var array
     */
    protected $validators;



    /**
     *
     * Constructor Method
     * @access public
     * @param mixed $data
     */
    public function __construct()
    {

    }



    /**
     *
     * Add validator strategy
     * @param string $name
     * @param ValidatorStrategy $strategy
     */
    public function addValidator( $name, ValidatorStrategy $strategy )
    {
        $this->validators[ $name ] = $strategy;
    }



    /**
     *
     * Perform the validation
     */
    public function isValid()
    {
        $error = false;

        foreach ( $this->validators as $name => $validator )
        {
            if ( !$validator->isValid() )
            {
                $error = true;
            }
        }

        if ( $error )
        {
            $this->isEror = true;
            return false;
        }
        else
        {
            return true;
        }
    }



    /**
     *
     * Custom method to mark any form field as invalidate (failed validation)
     * @access public
     * @param mixed $name
     * @param mixed $customMsg
     */
    public function invalidateField( $field, $customMsg = null )
    {
        $this->validators[ $field ]->setMessage( $field, $customMsg );
        $this->isEror = true;
    }



    /**
     *
     * Custom method to mark overall validation process as valid or invalid
     * Typical use is login system, all input pass validation, but somehow no valid user is found
     * So use this method to mark overall process as invalid
     * @access public
     * @param boolean $type
     */
    public function invalidateValidation( $type = true )
    {
        $this->isEror = ( boolean ) $type;
    }



    /**
     *
     * Method to check wether validation is successfull or fail
     * @access public
     * @return bool
     */
    public function isError()
    {
        return $this->isEror;
    }



    /**
     *
     * Method to display eror message in html document
     * @access public
     * @param mixed $name
     * @example $obj->getError('username')
     * @return string
     */
    public function getError( $name )
    {
        return $this->validators[ $name ]->getMessage();
    }



    /**
     *
     * Method to populate error field
     * @access public
     * @return array of message
     */
    public function getAllError()
    {
        $message = array( );

        foreach ( $this->validators as $key => $validator )
        {
            if ( $validator->getMessage() != null )
            {
                $message[ $key ] = $validator->getMessage();
            }
        }

        return $message;
    }



    /**
     *
     * Method to create block of error message (usualy used at the top of the form)
     * @param string $break
     * @return string of message
     */
    public function showError( $break = '<br />' )
    {
        $return = null;
        $messages = $this->getAllError();

        if ( count( $messages ) > 0 )
        {
            foreach ( $messages as $value )
            {
                $return .= $value . $break . PHP_EOL;
            }
        }
        return $return;
    }



}




?>
