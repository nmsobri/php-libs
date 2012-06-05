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
    protected $data = array();

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
        return ( !is_null( $this->messages ) ) ? $this->messages : null;
    }

    /**
     * 
     * Set error message
     * @access public
     */
    public function setMessage( $field, $message )
    {
        $this->messages = ( $message == null ) ? ( $this->data['field'] ) ? $this->errorText( 3, $this->data['field'] ) : $this->errorText( 3, $field ) : $message;
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
        $msg[0] = 'Sila betulkan ralat dibawah:';
        $msg[1] = 'Medan <b>' . $fieldname . '</b> kosong.';
        $msg[2] = 'Medan <b>' . $fieldname . '</b> tidak padan dengan medan <b>' . $fieldname2 . '</b>';
        $msg[3] = 'Medan <b>' . $fieldname . '</b> mengandungi ralat.';
        $msg[4] = 'Tarikh didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[5] = 'Email didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[6] = 'Nilai didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[7] = 'Teks didalam medan <b>' . $fieldname . '</b> terlalu panjang.';
        $msg[8] = 'Url didalam medan <b>' . $fieldname . '</b> tidak sah.';
        $msg[9] = 'Terdapat kod html didalam medan <b>' . $fieldname . '</b>, ini tidak dibenarkan!.';
        $msg[10] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki.';
        $msg[11] = 'Checkbox <b>' . $fieldname . '</b> tidak ditanda.';
        $msg[12] = 'Selection <b>' . $fieldname . '</b> tidak dipilih.';
        $msg[13] = 'Radio <b>' . $fieldname . '</b> tidak ditanda.';
        $msg[14] = 'Medan <b>' . $fieldname . '</b> tidak boleh mengandungi nombor.';
        $msg[15] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki  atau mengandungi nombor.';
        $msg[16] = 'Medan <b>' . $fieldname . '</b> hanya boleh mengandungi nombor.';
        $msg[17] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki atau mengandungi teks.';
        $msg[18] = 'Nilaididalam medan <b>' . $fieldname . '</b> tidak memenuhi panjang yang dikehendaki  atau titik perpuluhan yang sah atau mengandungi teks.';
        $msg[19] = 'Teks didalam medan <b>' . $fieldname . '</b> tidak boleh mengandungi ruang kosong';
        $msg[20] = 'Teks didalam medan <b>' . $fieldname . '</b> hanya boleh mengandungi teks';
        return $msg[$num];
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
     * @param str $attributes['type']
     * @param mixed $attributes['customMsg']
     * @param str $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addCheckBoxField('subcribe',$_POST['subscribe'],array('customMsg'=>'*'))
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'checkbox';
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;

        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( isset( $value ) )
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
     * @param mixed $name name of the filed
     * @param mixed $compareValue name of the filed you want to compare with
     * @param mixed $attributes['comparasionFieldName']
     * @param str $attributes['type']
     * @param str $attributes['required']
     * @param mixed $attributes['emptyMsg'] - Use this message if field is empty
     * @param mixed $attributes['emptyMsg'] - Use this message if this filed is not equal with comaprison field
     * @param str $attributes['fieldName']  - Use to donate field name in error message instead of using POST key data
     * @param str $attributes['fieldNameComparison'] - Use to donate comparison field name in error message instead of using comparison POST key data( using $compareWith)
     * @example $obj->add_compare_field('new_pass','confirm_pass',array('fieldName'=>'Confirm Password', 'comparasionFieldName'=>'New Password'))
     */
    public function __construct( $name, $value, $compareValue, $comparsionField, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'compare_field';
        $required = ( array_key_exists( 'required', $attr ) ) ? ( boolean )$attr['required'] : true;
        $emptyMsg = ( array_key_exists( 'empty_message', $attr ) ) ? $attr['empty_message'] : null;
        $notMatchMsg = ( array_key_exists( 'unmatch_message', $attr ) ) ? $attr['unmatch_message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;
        $fieldNameComparison = ( array_key_exists( 'field_comparison', $attr ) ) ? $attr['field_comparison'] : $comparsionField;


        $this->data['to_compare'] = $value;
        $this->data['compare_with'] = $compareValue;
        $this->data['field_comparasion'] = $fieldNameComparison;
        $this->data['type'] = $type;
        $this->data['required'] = $required;
        $this->data['empty_message'] = $emptyMsg;
        $this->data['unmatch_message'] = $notMatchMsg;
        $this->data['field'] = $fieldName;
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
        $toCompare = $this->data['to_compare'];
        $compareWith = $this->data['compare_with'];
        $required = $this->data['required'];
        $comparasionName = $this->data['field_comparasion'];
        $emptyMsg = $this->data['empty_message'];
        $notMatchMsg = $this->data['unmatch_message'];
        $fieldName = $this->data['field'];

        if( $toCompare == '' )
        {
            if( $required == true )
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
            if( $toCompare != $compareWith )
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
     * @param str $attributes['type']
     * @param str $attributes['version']
     * @param str $attributes['required']
     * @param mixed $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addDateField('dob',$_POST['dob'],array('customMsg'=>'*'))
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'date';
        $version = ( array_key_exists( 'version', $attr ) ) ? $attr['version'] : 'us';
        $required = ( array_key_exists( 'required', $attr ) ) ? ( boolean )$attr['required'] : true;
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;


        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['version'] = $version;
        $this->data['required'] = $required;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $version = $this->data['version'];
        $required = $this->data['required'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( $value == '' )
        {
            if( $required == true )
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
            if( $version == 'eu' )
            {
                $pattern = "/^(0[1-9]|[1-2][0-9]|3[0-1])[-](0[1-9]|1[0-2])[-](19|20)[0-9]{2}$/";
            }
            else
            {
                $pattern = "/^(19|20)[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$/";
            }
            if( preg_match( $pattern, $value ) )
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
     * @param str $attributes['type']
     * @param str $attributes['required']
     * @param mixed $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addEmailField('email',$_POST['email'],array('customMsg'=>'*'))
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'email';
        $required = ( array_key_exists( 'required', $attr ) ) ? ( boolean )$attr['required'] : true;
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;


        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['required'] = $required;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $required = $this->data['required'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( $value == '' )
        {
            if( $required == true )
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
            if( preg_match( "/^[^\W\d](?:\w+)(?:\.\w+|\-\w+)*@(?:\w+)(\.[a-z]{2,6})+$/i", $value ) )
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
     * @param str $attributes['type']
     * @param str $attributes['required']
     * @param int $attributes['decimal']
     * @param int $attributes['minLength']
     * @param int $attributes['maxLength']
     * @param mixed $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addNumberField('age',$_POST['age'],array('minLength'=>2,'maxLength'=>0)) -check for number that length equal to 2
     * @example $obj->addNumberField('age',$_POST['age'],array('minLength'=>7,'maxLength'=>9)) -check for number that length in the range of 7-9
     * @example $obj->addNumberField('age',$_POST['age'],array('minLength'=>3,'maxLength'=>0,'decimal'=>2)) -check for number that length equal to 3 and have 2 decimal place (190.30)
     * @example $obj->addNumberField('age',$_POST['age'],array('minLength'=>2,'maxLength'=>-1)) -check for number that length have at least 2 length and beyond (ignore the maxLength)
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'number';
        $required = ( array_key_exists( 'required', $attr ) ) ? ( boolean )$attr['required'] : true;
        $decimal = ( array_key_exists( 'decimal', $attr ) ) ? $attr['decimal'] : 0;
        $minLength = ( array_key_exists( 'min_length', $attr ) ) ? $attr['min_length'] : 0;
        $maxLength = ( array_key_exists( 'max_length', $attr ) ) ? $attr['max_length'] : 0;
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;

        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['required'] = $required;
        $this->data['decimal'] = $decimal;
        $this->data['min_length'] = $minLength;
        $this->data['max_length'] = $maxLength;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $required = $this->data['required'];
        $decimal = $this->data['decimal'];
        $minLength = $this->data['min_length'];
        $maxLength = $this->data['max_length'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( empty( $value ) )
        {
            if( $required == true )
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
            if( $minLength > 0 && $maxLength == 0 ) /* if minLength >0 and maxLength ==0, check for exact length match */
            {
                if( $decimal > 0 )
                {
                    if( preg_match( '/^[0-9]{' . $minLength . '}\.[0-9]{' . $decimal . '}$/i', $value ) )
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
                    if( preg_match( '/^[0-9]{' . $minLength . '}$/i', $value ) )
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
            if( $minLength > 0 && $maxLength == -1 ) /* if minLength >0 and maxLength ==-1, atleast have minLength and we dont bother the maxLength */
            {
                if( $decimal > 0 )
                {
                    if( preg_match( '/^[0-9]{' . $minLength . ',}\.[0-9]{' . $decimal . '}$/i', $value ) )
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
                    if( preg_match( '/^[0-9]{' . $minLength . ',}$/i', $value ) )
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
            elseif( ( $minLength == 0 || $minLength > 0 ) && $maxLength > 0 ) /* if minLength==0 or minLength >0 ,check for range of length,atleast have minLength but dont surpass maxLength */
            {
                if( $decimal > 0 )
                {
                    if( preg_match( '/^[0-9]{' . $minLength . ',' . $maxLength . '}\.[0-9]{' . $decimal . '}$/i', $value ) )
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
                    if( preg_match( '/^[0-9]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
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
                if( preg_match( '/^[0-9]+$/i', $value ) )
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

}

class RadioValidator extends ValidatorStrategy
{

    /**
     *
     * Validation for radio field
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param str $attributes['type']
     * @param mixed $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addRadioField('gender',$_POST['gender'],array('customMsg'=>'*'))
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'radio';
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;


        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( isset( $value ) )
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
     * @param str $attributes['type']
     * @param str $attributes['required']
     * @param str $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     */
    public function __construct( $name, $value, $regex, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'regex';
        $required = ( array_key_exists( 'required', $attr ) ) ? ( boolean )$attr['required'] : true;
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;


        $this->data['value'] = $value;
        $this->data['regex'] = $regex;
        $this->data['type'] = $type;
        $this->data['required'] = $required;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $regex = $this->data['regex'];
        $required = $this->data['required'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( $value == '' and $required == true )
        {
            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 1, $fieldName );
            return false;
        }

        if( $value == '' and $required != true )
        {
            return true; //simply return true cause we dont care if this field is empty or not
        }

        if( $value != '' )
        {
            if( preg_match( $regex, $value ) )
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
     * @param str $attributes['type']
     * @param mixed $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addSelectField('country',$_POST['country'],array('customMsg'=>'*'))
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'select';
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;


        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( !empty( $value ) )
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
     * @param str $attributes['type']
     * @param str $attributes['required']
     * @param str $attributes['permitNum']
     * @param int $attributes['minLength']
     * @param int $attributes['maxLength']
     * @param str $attributes['customMsg']
     * @param str $attributes['fieldName'] -use to donate field name in error message instead of using POST key data
     * @example $obj->addTextField('name',$_POST['name'],array('minLength'=>10,'maxLength'=>0))  -check for text that length equal to 10
     * @example $obj->addTextField('name',$_POST['name'],array('minLength'=>3,'maxLength'=>10))  -check for text that length in the range of 3-10
     * @example $obj->addTextField('name',$_POST['name'],array('minLength'=>3,'maxLength'=>10,'permitNum'=>'y'))  -check for text that length equal to 10 and can contain number in it
     */
    public function __construct( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $type = ( array_key_exists( 'type', $attr ) ) ? $attr['type'] : 'text';
        $required = ( array_key_exists( 'required', $attr ) ) ? ( boolean )$attr['required'] : true;
        $permitNum = ( array_key_exists( 'allow_num', $attr ) ) ? ( boolean )$attr['allow_num'] : false;
        $permitSpace = ( array_key_exists( 'allow_space', $attr ) ) ? ( boolean )$attr['allow_space'] : false;
        $minLength = ( array_key_exists( 'min_length', $attr ) ) ? $attr['min_length'] : 0;
        $maxLength = ( array_key_exists( 'max_length', $attr ) ) ? $attr['max_length'] : 0;
        $customMsg = ( array_key_exists( 'message', $attr ) ) ? $attr['message'] : null;
        $fieldName = ( array_key_exists( 'field', $attr ) ) ? $attr['field'] : $name;


        $this->data['value'] = $value;
        $this->data['type'] = $type;
        $this->data['required'] = $required;
        $this->data['allow_num'] = $permitNum;
        $this->data['allow_space'] = $permitSpace;
        $this->data['min_length'] = $minLength;
        $this->data['max_length'] = $maxLength;
        $this->data['message'] = $customMsg;
        $this->data['field'] = $fieldName;
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
        $value = $this->data['value'];
        $required = $this->data['required'];
        $permitSpace = $this->data['allow_space'];
        $permitNum = $this->data['allow_num'];
        $minLength = $this->data['min_length'];
        $maxLength = $this->data['max_length'];
        $customMsg = $this->data['message'];
        $fieldName = $this->data['field'];

        if( empty( $value ) )
        {
            if( $required == true )
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
        else
        {
            if( $minLength > 0 && $maxLength == 0 ) /* if minLength >0 and maxLength ==0, check for exact length match */
            {
                if( $permitNum == true )
                {
                    if( $permitSpace == true )
                    {
                        if( preg_match( '/^[a-zA-Z0-9\s]{' . $minLength . '}$/i', $value ) )
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
                        if( preg_match( '/^[a-zA-Z0-9]{' . $minLength . '}$/i', $value ) )
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
                    if( $permitSpace == true )
                    {
                        if( preg_match( '/^[a-zA-Z\s]{' . $minLength . '}$/i', $value ) )
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
                        if( preg_match( '/^[a-zA-Z]{' . $minLength . '}$/i', $value ) )
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
            elseif( ( $minLength == 0 || $minLength > 0 ) && $maxLength > 0 ) /* if minLength==0 or minLength >0 ,check for range of length */
            {
                if( $permitNum == true )
                {
                    if( $permitSpace == true )
                    {
                        if( preg_match( '/^[a-zA-Z0-9\s]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
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
                        if( preg_match( '/^[a-zA-Z0-9]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
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
                    if( $permitSpace == true )
                    {
                        if( preg_match( '/^[a-zA-Z\s]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
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
                        if( preg_match( '/^[a-zA-Z]{' . $minLength . ',' . $maxLength . '}$/i', $value ) )
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
            else /* we dont bother string length..just check wether we permit for num character */
            {
                if( $permitNum == true )
                {
                    if( $permitSpace == true )
                    {
                        if( preg_match( '/^[a-zA-Z0-9\s]+$/i', $value ) )
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
                        if( preg_match( '/^[a-zA-Z0-9]+$/i', $value ) )
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
                    if( $permitSpace == true )
                    {

                        if( preg_match( '/^[a-zA-Z\s]+$/i', $value ) )
                        {
                            return true;
                        }
                        else
                        {
                            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 14, $fieldName );
                            return false;
                        }
                    }
                    else
                    {
                        if( preg_match( '/^[a-zA-Z]+$/i', $value ) )
                        {
                            return true;
                        }
                        else
                        {
                            $this->messages = ( $customMsg ) ? $customMsg : $this->errorText( 20, $fieldName );
                            return false;
                        }
                    }
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
        $this->validators[$name] = $strategy;
    }

    /**
     *
     * Perform the validation
     */
    public function isValid()
    {
        $error = false;

        foreach( $this->validators as $name => $validator )
        {
            if( !$validator->isValid() )
            {
                $error = true;
            }
        }

        if( $error )
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
        $this->validators[$field]->setMessage( $field, $customMsg );
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
        $this->isEror = ( boolean )$type;
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
        return $this->validators[$name]->getMessage();
    }

    /**
     *
     * Method to populate error field
     * @access public
     * @return array of message
     */
    public function getAllError()
    {
        $message = array();

        foreach( $this->validators as $key => $validator )
        {
            if( $validator->getMessage() != null )
            {
                $message[$key] = $validator->getMessage();
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

        if( count( $messages ) > 0 )
        {
            foreach( $messages as $value )
            {
                $return .= $value . $break . PHP_EOL;
            }
        }
        return $return;
    }

}

?>
