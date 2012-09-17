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
     * @param string $fieldname
     * @param string $fieldname2
     * @return string of message
     */
    protected function errorText( $num, $fieldname = null, $fieldname2 = null )
    {
        $msg[ 0 ] = 'Sila betulkan ralat dibawah:';
        $msg[ 1 ] = 'Medan <b>' . $fieldname . '</b> kosong.';
        $msg[ 2 ] = 'Medan <b>' . $fieldname . '</b> tidak padan dengan medan <b>' . $fieldname2 . '</b>';
        $msg[ 3 ] = 'Medan <b>' . $fieldname . '</b> mengandungi ralat.';
        $msg[ 4 ] = 'Tarikh didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[ 5 ] = 'Email didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[ 6 ] = 'Nilai didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[ 7 ] = 'Teks didalam medan <b>' . $fieldname . '</b> terlalu panjang.';
        $msg[ 8 ] = 'Url didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[ 9 ] = 'Terdapat kod html didalam medan <b>' . $fieldname . '</b>, ini tidak dibenarkan!.';
        $msg[ 10 ] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki.';
        $msg[ 11 ] = 'Checkbox <b>' . $fieldname . '</b> tidak ditanda.';
        $msg[ 12 ] = 'Selection <b>' . $fieldname . '</b> tidak dipilih.';
        $msg[ 13 ] = 'Radio <b>' . $fieldname . '</b> tidak ditanda.';
        $msg[ 14 ] = 'Medan <b>' . $fieldname . '</b> tidak boleh mengandungi nombor.';
        $msg[ 15 ] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki  atau mengandungi nombor.';
        $msg[ 16 ] = 'Medan <b>' . $fieldname . '</b> hanya boleh mengandungi nombor.';
        $msg[ 17 ] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki atau mengandungi teks.';
        $msg[ 18 ] = 'Nilaididalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki  atau titik perpuluhan yang sah atau mengandungi teks.';
        $msg[ 19 ] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak boleh mengandungi ruang kosong';
        $msg[ 20 ] = 'Teks didalam medan <b>' . $fieldname . '</b> hanya boleh mengandungi teks dan ruang kosong';
        $msg[ 21 ] = 'Teks didalam medan <b>' . $fieldname . '</b> hanya boleh mengandungi teks';
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
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( isset( $value ) )
        {
            return true;
        }
        else
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 11, $fieldName );
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
     * @param str $required
     * @param mixed $emptyMsg
     * @param mixed $notMatchMsg
     * @return boolean
     * @example to set custom msg through setCustomMsg()::setCustomMsg(array('fieldname'=>array('empty'=>'*','notMatch'=>'**')))
     * @info index 'empty' donate msg when field are empty and index 'notMatch' donate msg when comparing field are not macth
     */
    public function isValid()
    {
        $toCompare = $this->data[ 'to_compare' ];
        $compareWith = $this->data[ 'compare_with' ];
        $required = $this->data[ 'required' ];
        $comparasionName = $this->data[ 'field_comparasion' ];
        $emptyMsg = $this->data[ 'empty_message' ];
        $notMatchMsg = $this->data[ 'unmatch_message' ];
        $fieldName = $this->data[ 'field' ];

        if ( $toCompare == '' )
        {
            if ( $required == true )
            {
                $this->messages = ( $emptyMsg ) ? $emptyMsg : $this->errorText( 1, $fieldName );
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if ( $toCompare != $compareWith )
            {
                $this->messages = ( $notMatchMsg ) ? $notMatchMsg : $this->errorText( 2, $fieldName, $comparasionName );
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
     * @param mixed $value
     * @param mixed $version
     * @param str $required
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $version = $this->data[ 'version' ];
        $required = $this->data[ 'required' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( $value == '' )
        {
            if ( $required == true )
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 1, $fieldName );
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if ( $version == 'eu' )
            {
                $pattern = "/^(0[1-9]|[1-2][0-9]|3[0-1])[-](0[1-9]|1[0-2])[-](19|20)[0-9]{2}$/";
            }
            else
            {
                $pattern = "/^(19|20)[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$/";
            }
            if ( preg_match( $pattern, $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 4, $fieldName );
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
     * @param mixed $value
     * @param str $required
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $required = $this->data[ 'required' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( $value == '' )
        {
            if ( $required == true )
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 1, $fieldName );
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if ( preg_match( "/^[^\W\d](?:\w+)(?:\.\w+|\-\w+)*@(?:\w+)(\.[a-z]{2,6})+$/i", $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 5, $fieldName );
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
     * @param mixed $value
     * @param str $required
     * @param int $decimal
     * @param int $minLength
     * @param int $maxLength
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $required = $this->data[ 'required' ];
        $decimal = $this->data[ 'decimal' ];
        $minLength = $this->data[ 'min_length' ];
        $maxLength = $this->data[ 'max_length' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( empty( $value ) )
        {
            $this->checkRequired();
        }
        else
        {
            if ( $minLength > 0 && $maxLength == 0 ) /* if minLength > 0 and maxLength == 0, check for exact length match */
            {
                $this->checkExactLength();
            }
            elseif ( $minLength >= 0 && $maxLength > 0 ) /* if minLength == 0 or minLength > 0 ,check for range of length */
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
        if ( $required == true )
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 1, $fieldName );
            return false;
        }
        else
        {
            return true;
        }
    }




    protected function checkExactLength()
    {
        if ( $decimal > 0 )
        {
            if ( preg_match( '/^[0-9]{' . $minLength . '}\.[0-9]{' . $decimal . '}$/i', $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 18, $fieldName );
                return false;
            }
        }
        else
        {
            if ( preg_match( '/^[0-9]{' . $minLength . '}$/i', $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 17, $fieldName );
                return false;
            }
        }
    }




    protected function checkVariableLength()
    {
        if ( $decimal > 0 )
        {
            if ( preg_match( '/^[0-9]{' . $minLength . ',' . $maxLength . '}\.[0-9]{' . $decimal . '}$/i', $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 18, $fieldName );
                return false;
            }
        }
        else
        {
            if ( preg_match( '/^[0-9]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 10, $fieldName );
                return false;
            }
        }
    }




    protected function checkInfiniteLength()
    {
        if ( $decimal > 0 )
        {
            if ( preg_match( '/^[0-9]+\.[0-9]{' . $decimal . '}$/i', $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 18, $fieldName );
                return false;
            }
        }
        else
        {
            if ( preg_match( '/^[0-9]+$/i', $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 16, $fieldName );
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
     * @param mixed $value
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( isset( $value ) )
        {
            return true;
        }
        else
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 13, $fieldName );
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
     * @param mixed $value
     * @param mixed $regex
     * @param mixed $required
     * @param mixed $customMsg
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $regex = $this->data[ 'regex' ];
        $required = $this->data[ 'required' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( $value == '' and $required == true )
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 1, $fieldName );
            return false;
        }

        if ( $value == '' and $required != true )
        {
            return true; //simply return true cause we dont care if this field is empty or not
        }

        if ( $value != '' )
        {
            if ( preg_match( $regex, $value ) )
            {
                return true;
            }
            else
            {
                $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 6, $fieldName );
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
     * @param mixed $value
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( !empty( $value ) )
        {
            return true;
        }
        else
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 12, $fieldName );
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
     * @param mixed $value
     * @param str $required
     * @param str $permitNum
     * @param int $minLength
     * @param int $maxLength
     * @param mixed $customMsg
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->data[ 'value' ];
        $required = $this->data[ 'required' ];
        $permitSpace = $this->data[ 'allow_space' ];
        $permitNum = $this->data[ 'allow_num' ];
        $minLength = $this->data[ 'min_length' ];
        $maxLength = $this->data[ 'max_length' ];
        $customMsg = $this->data[ 'message' ];
        $fieldName = $this->data[ 'field' ];

        if ( empty( $value ) )
        {
            $this->checkrequired();
        }
        else
        {
            if ( $minLength > 0 && $maxLength == 0 ) /* if minLength > 0 and maxLength == 0, check for exact length match */
            {
                $this->checkExactLength();
            }
            elseif ( $minLength >= 0 && $maxLength > 0 ) /* if minLength == 0 or minLength > 0 ,check for range of length */
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
        if ( $required )
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 1, $fieldName );
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

        if ( $permitNum )
        {
            if ( $permitSpace )
            {
                if ( preg_match( '/^[a-zA-Z0-9\s]{' . $minLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 10, $fieldName );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z0-9]{' . $minLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 10, $fieldName );
                    return false;
                }
            }
        }
        else
        {
            if ( $permitSpace )
            {
                if ( preg_match( '/^[a-zA-Z\s]{' . $minLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 15, $fieldName );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z]{' . $minLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 15, $fieldName );
                    return false;
                }
            }
        }
    }




    protected function checkVariableLength()
    {
        if ( $permitNum )
        {
            if ( $permitSpace )
            {
                if ( preg_match( '/^[a-zA-Z0-9\s]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 10, $fieldName );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z0-9]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 10, $fieldName );
                    return false;
                }
            }
        }
        else
        {
            if ( $permitSpace )
            {
                if ( preg_match( '/^[a-zA-Z\s]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 15, $fieldName );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 15, $fieldName );
                    return false;
                }
            }
        }
    }




    protected function checkInfiniteLength()
    {
        if ( $permitNum )
        {
            if ( $permitSpace )
            {
                if ( preg_match( '/^[a-zA-Z0-9\s]+$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 6, $fieldName );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z0-9]+$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 19, $fieldName );
                    return false;
                }
            }
        }
        else
        {
            if ( $permitSpace )
            {

                if ( preg_match( '/^[a-zA-Z\s]+$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 20, $fieldName );
                    return false;
                }
            }
            else
            {
                if ( preg_match( '/^[a-zA-Z]+$/i', $value ) )
                {
                    return true;
                }
                else
                {
                    $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 21, $fieldName );
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