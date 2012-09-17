<?php

/**
 * Class To Generate Form Control
 */
class Form
{

    /**
     *
     * @var array
     * @access protected
     */
    protected $formData = array( );



    /**
     *
     * @var string
     * @access protected
     */
    protected $formMethod;



    /**
     *
     * @var int
     * @static
     * @access protected
     */
    protected static $instance = 0;




    /**
     * Constructor
     * @access public
     * @param bool $isUpload
     * @param bool $formMethod
     * @param bool $formAction
     */
    public function __construct( $formMethod = 'Post' )
    {
        $formMethod = ( is_null( $formMethod ) ) ? 'Post' : ucfirst( strtolower( $formMethod ) );
        $this->formData = ( $formMethod == 'Post' ) ? $_POST : $_GET;
        $this->formMethod = $formMethod;
    }




    /**
     * Method to cretae text control
     * @access public
     * @param string $name
     * @param mixed $defaultValue
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @return string
     */
    public function text( $name, $defaultValue = '', $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( ); /* Cast to an array if $atrributes exist otherwise create an empty array */

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;

        $value = isset( $this->formData[ $name ] ) ? $this->formData[ $name ] : $defaultValue;
        $text = '';
        $text .= '<input type="text" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" ' . $disabled . '>';
        $text .= PHP_EOL;
        return $text;
    }




    /**
     * Method to create textarea control
     * @access public
     * @param string $name
     * @param mixed $defaultValue
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @param mixed $attr['cols']
     * @param mixed $attr['rows']
     * @return string
     */
    public function textarea( $name, $defaultValue = '', $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';
        $cols = ( array_key_exists( 'cols', $attr ) ) ? $attr[ 'cols' ] : 20;
        $rows = ( array_key_exists( 'rows', $attr ) ) ? $attr[ 'rows' ] : 3;
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;

        $value = isset( $this->formData[ $name ] ) ? $this->formData[ $name ] : $defaultValue;
        $textarea = '';
        $textarea .= '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '" cols="' . $cols . '" rows="' . $rows . '" ' . $disabled . '>';
        $textarea .= $value;
        $textarea .= '</textarea>';
        $textarea .= PHP_EOL;
        return $textarea;
    }




    /**
     *
     * Method to create password control
     * @access public
     * @param string $name
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @return string
     */
    public function password( $name, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';

        $password = '';
        $password .= '<input type="password" name="' . $name . '" id="' . $id . '" class="' . $class . '">';
        $password .= PHP_EOL;
        return $password;
    }




    /**
     * Method to create select control
     * @access public
     * @param string $name
     * @param array $options
     * @param mixed $selected marked option selected
     * @param array $group create option group
     * @param mixed $defaultValue
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @param mixed $attr['multiple']
     * @param mixed $attr['size']
     * 
     * 
     * @param $options is pass as follows:
     * 
     * array('png'=>'Penang','kl'=>'K.Lumpur') will create select option like this 
     * <select>
     * <option value='png'>Penang</option>
     * <option value='kl'>K.Lumpur</option>
     * </select>
     * 
     *  array('north'=>array('kdh'=>'Kedah', 'png'=>'Penang', 'prk'=>'Perak' ) ) will create select option like this 
     * <select> 
     * <optgroup label='north'>
     *  <option value='kdh'>Kedah</option>
     *  <option value='png'>Penang</option>
     *  <option value='prk'>Perak</option>
     * </optgroup>
     * <select> 
     * 
     * 
     */
    public function select( $name, $options, $selected = null, $atr = null )
    {
        $atr = (!is_null( $atr ) ) ? ( array ) $atr : array( );

        $id = ( array_key_exists( 'id', $atr ) ) ? $atr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $atr ) ) ? $atr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';
        $multiple = ( array_key_exists( 'multiple', $atr ) ) ? $atr[ 'multiple' ] : null;
        $size = ( array_key_exists( 'size', $atr ) ) ? $atr[ 'size' ] : null;
        $selected = ( is_null( $selected ) ) ? '' : $selected;

        static $instance = 0;
        static $optionsList;
        $value = '';

        if ( $instance == 0 ) /* need to check if this method call in sequential (calling this method twice) to make sure it dosent cache previous <option> */
        {
            $optionsList = '';
        }

        $value = isset( $this->formData[ $name ] ) ? $this->formData[ $name ] : $selected;

        foreach ( $options as $key => $val )
        {
            if ( is_array( $val ) )
            {
                $instance++;
                $optionsList .='<optgroup label="' . $key . '">' . PHP_EOL;
                self::select( $name, $val );
                $instance--;
                $optionsList .='</optgroup>' . PHP_EOL;
            }
            else
            {
                $optionsList .='<option value="' . $key . '" ';
                if ( $key == $value )
                {
                    $optionsList .= 'selected="selected"';
                }
                $optionsList .='>' . $val . '</option>' . PHP_EOL;
            }
        }

        $select = '';
        $select .= '<select name="' . $name . '"';
        $select .= (!is_null( $multiple ) ) ? ' multiple="multiple"' : '';
        $select .= (!is_null( $size ) ) ? ' size="' . $size . '"' : '';
        $select .= ' id="' . $id . '" class="' . $class . '"';
        $select .= $disabled . '>';
        $select .= PHP_EOL;
        $select .= $optionsList;
        $select .= '</select>';
        $select .= PHP_EOL;

        return $select;
    }




    /**
     * Method to create radio control
     * @access public
     * @param string $name
     * @param mixed $value
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @return string
     */
    public function radio( $name, $value, $checked = false, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';
        $checked = ( is_null( $checked ) ) ? false : ( boolean ) $checked;

        $radio = '';
        $radio .= '<input type="radio" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '" ';

        if ( $checked and !isset( $this->formData[ $name ] ) )
        {
            $radio .= 'checked';
        }
        elseif ( ( isset( $this->formData[ $name ] ) and $this->formData[ $name ] == $value ) )
        {
            $radio .= 'checked';
        }

        $radio .= $disabled . '>' . PHP_EOL;
        return $radio;
    }




    /**
     * Method to create checkbox control
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @return string
     */
    public function checkbox( $name, $value, $checked = false, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';
        $checked = ( is_null( $checked ) ) ? false : ( boolean ) $checked;

        $checkbox = '';
        $checkbox .= '<input type="checkbox" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '" ';

        if ( ( isset( $this->formData[ $name ] ) and $this->formData[ $name ] == $value ) or $checked )
        {
            $checkbox .= 'checked';
        }

        $checkbox .= $disabled . '>' . PHP_EOL;
        return $checkbox;
    }




    /**
     * Method to create file control (for upload)
     * @access public
     * @param mixed $name
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @return string
     */
    public function file( $name, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';

        $file = '';
        $file .= '<input type="file" name="' . $name . '" id="' . $id . '" class="' . $class . '" ' . $disabled . '>';
        $file .= PHP_EOL;
        return $file;
    }




    /**
     * Method to create hidden control
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @return string
     */
    public function hidden( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';

        $hidden = '';
        $hidden .= '<input type="hidden" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '">';
        $hidden .= PHP_EOL;
        return $hidden;
    }




    /**
     *
     * Method to create button control
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @return string
     */
    public function button( $name, $value, $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';

        $button = '';
        $button .= '<input type="button" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '" ' . $disabled . '>';
        $button .= PHP_EOL;
        return $button;
    }




    /**
     * Method to create submit control
     * @access public
     * @param mixed $name
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled']
     * @return string
     */
    public function submit( $name, $value = 'Submit', $attr = null )
    {
        $attr = (!is_null( $attr ) ) ? ( array ) $attr : array( );

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr[ 'id' ] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr[ 'class' ] : $name . 'Class';
        $disabled = ( array_key_exists( 'disabled', $attr ) ) ? 'disabled="disabled"' : '';

        $submit = '';
        $submit .= '<input type="submit" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" ' . $disabled . '>';
        $submit .= PHP_EOL;
        return $submit;
    }




    /**
     * Method to create start form tag
     * @access public
     * @return string
     */
    public function formStart( $action, $isUpload )
    {
        $isUpload = ( boolean ) $isUpload;
        $formName = 'Form' . ++self::$instance;
        $formStart = '';
        $formStart = '<form name="' . $formName . '" method="' . $this->formMethod . '" action="' . $action . '"';
        $formStart .= ( $isUpload ) ? ' enctype="multipart/form-data">' : '>';
        $formStart .= PHP_EOL;
        return $formStart;
    }




    /**
     * Method to create end form tag
     * @access public
     * @return string
     */
    public function formEnd()
    {
        return '</form>' . PHP_EOL;
    }




}

?>