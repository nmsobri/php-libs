<?php

/**
 * Class To Generate Form Control
 * @author slier
 */
class Form
{

    /**
     *
     * @var array
     * @access protected
     */
    protected $formData;


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
     *
     * @var Session
     * @access protected
     */
    protected $session;




    /**
     *
     * Constructor
     * @access public
     * @param Session $session
     * @param Str $formMethod
     */
    public function __construct( Session $session, $formMethod = 'Post' )
    {

        $formMethod = ( is_null( $formMethod ) ) ? 'Post' : ucfirst( strtolower( $formMethod ) );
        $this->formData = ( $formMethod == 'Post' ) ? $_POST : $_GET;
        $this->formMethod = $formMethod;
        $this->session = $session;
    }




    /**
     *
     * Set Form Data Incase Form Get Redirect And Lost The POSTed Data
     * @param mixed $data
     */
    public function setFormData( &$data )
    {
        $this->formData = $data;
    }




    /**
     *
     * Method to cretae text control
     * @access public
     * @param string $name
     * @param mixed $defaultValue
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @return string
     */
    public function text( $name, $defaultValue = null, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';
        $defaultValue = ( !is_null( $defaultValue ) ) ? $defaultValue : '';

        $value = isset( $this->formData[$name] ) ? $this->formData[$name] : $defaultValue;
        $text = null;
        $text .= '<input type="text" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" ' . $readOnly . ' ' . $event . '>';
        return $text;
    }




    /**
     *
     * Method to create textarea control
     * @access public
     * @param string $name
     * @param mixed $defaultValue
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @param mixed $attributes['cols']
     * @param mixed $attributes['rows']
     * @return string
     */
    public function textarea( $name, $defaultValue = null, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $cols = ( array_key_exists( 'cols', $attr ) ) ? $attr['cols'] : 20;
        $rows = ( array_key_exists( 'rows', $attr ) ) ? $attr['rows'] : 3;
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';
        $defaultValue = ( !is_null( $defaultValue ) ) ? $defaultValue : '';

        $value = isset( $this->formData[$name] ) ? $this->formData[$name] : $defaultValue;
        $textarea = null;
        $textarea .= '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '" cols="' . $cols . '" rows="' . $rows . '" ' . $readOnly . ' ' . $event . '>' . $value . '</textarea>';
        return $textarea;
    }




    /**
     *
     * Method to create password control
     * @access public
     * @param string $name
     * @param mixed $attr
     * @return string
     */
    public function password( $name, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';

        $password = null;
        $password .= '<input type="password" name="' . $name . '" id="' . $id . '" class="' . $class . '" ' . $event . '>';
        return $password;
    }




    /**
     * @todo add recursive function to support <optgroup></optgroup>
     * Method to create select control
     * @access public
     * @param string $name
     * @param array $options
     * @param mixed $selected marked option selected
     * @param array $group create option group
     * @param mixed $defaultValue
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @param $options is passed like this:: array('png'=>'Penang','kl'=>'K.Lumpur') will create select option like this <option value='png'>Penang</option><option value='kl'>K.Lumpur</option>
     */
    function select( $name, $options, $selected = null, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $multiple = ( array_key_exists( 'multiple', $attr ) ) ? 'multiple="multiple"' : '';
        $size = ( array_key_exists( 'size', $attr ) ) ? $attr['size'] : 0;
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $selected = ( is_null( $selected ) ) ? '' : $selected;
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';


        $optionsList = '';
        $value = '';

        $value = isset( $this->formData[$name] ) ? $this->formData[$name] : $selected;

        foreach( $options as $key => $val )
        {
            $optionsList .= '<option value="' . $key . '" ';
            if( $key == $value )
            {
                $optionsList .= 'selected="selected"';
            }
            $optionsList .= '>' . $val . '</option>';
        }

        $select = '';
        $select .= '<select name="' . $name . '" ' . $multiple . ' size="' . $size . '" id="' . $id . '" class="' . $class . '" ' . $event . ' ' . $readOnly . '>';
        $select .= $optionsList;
        $select .= '</select>';

        return $select;
    }




    /**
     *
     * Method to create radio control
     * @access public
     * @param string $name
     * @param mixed $value
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @return string
     */
    public function radio( $name, $value, $checked = false, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';

        $radio = null;
        $radio .= '<input type="radio" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '" ';

        if( $checked and !isset( $this->formData[$name] ) )
        {
            $radio .= 'checked';
        }
        elseif( ( isset( $this->formData[$name] ) and $this->formData[$name] == $value ) )
        {
            $radio .= 'checked';
        }

        $radio .= ' ' . $readOnly . ' ' . $event . '>';
        return $radio;
    }




    /**
     *
     * Method to create checkbox control
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @return string
     */
    public function checkbox( $name, $value, $checked = false, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';

        $checkbox = null;
        $checkbox .= '<input type="checkbox" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '" ';

        if( ( isset( $this->formData[$name] ) and $this->formData[$name] == $value ) or $checked )
        {
            $checkbox .= 'checked';
        }
        $checkbox .= ' ' . $readOnly . ' ' . $event . '>';
        return $checkbox;
    }




    /**
     *
     * Method to create file control (for upload)
     * @access public
     * @param mixed $name
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @return string
     */
    public function file( $name, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';

        $file = null;
        $file .= '<input type="file" name="' . $name . '" id="' . $id . '" class="' . $class . '" ' . $readOnly . ' ' . $event . '>';
        return $file;
    }




    /**
     *
     * Method to create hidden control
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @return string
     */
    public function hidden( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';

        $hidden = null;
        $hidden .= '<input type="hidden" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '">';
        return $hidden;
    }




    /**
     *
     * Method to create button control
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @return string
     */
    public function button( $name, $value, $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : '';
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';

        $button = null;
        $button .= '<input type="button" name="' . $name . '" value="' . $value . '" id="' . $id . '" class="' . $class . '" ' . $readOnly . ' ' . $event . '>';
        return $button;
    }




    /**
     *
     * Method to create submit control
     * @access public
     * @param mixed $name
     * @param mixed $attributes['id']
     * @param mixed $attributes['class']
     * @param mixed $attributes['event']
     * @param mixed $attributes['caption']
     * @return string
     */
    public function submit( $name, $value = 'Submit', $attr = null )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();

        $id = ( array_key_exists( 'id', $attr ) ) ? $attr['id'] : $name . 'Id';
        $class = ( array_key_exists( 'class', $attr ) ) ? $attr['class'] : $name . 'Class';
        $event = ( array_key_exists( 'event', $attr ) ) ? $attr['event'] : null;
        $readOnly = ( array_key_exists( 'readOnly', $attr ) ) ? 'readonly="readonly"' : '';

        $submit = null;
        $submit .= '<input type="submit" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" ' . $readOnly . ' ' . $event . '>';
        return $submit;
    }




    /**
     *
     * Method to to check wether form has been submitted and also used PRG pattern
     * @access public
     * @return bool
     */
    public function isPost()
    {
        if( !$_GET['post'] ) //quickly delete session data iff this GET data don exist (make session only available on this page and only when GET data is exist)
        {
            unset( $_SESSION[$_SERVER['PHP_SELF'] . 'POST'] );
            unset( $_SESSION[$_SERVER['PHP_SELF'] . 'FILES'] );
        }

        if( count( $_POST ) > 0 || count( $_FILES ) > 0 )
        {
            $this->session->flash( $_SERVER['PHP_SELF'] . 'POST', $_POST ); //$_SERVER['PHP_SELF'] . 'POST' -to make this session data avaliable only for this page
            $this->session->flash( $_SERVER['PHP_SELF'] . 'FILES', $_FILES ); //adding $FILES support
            $path = ( $_SERVER['QUERY_STRING'] != '' ) ? ( isset( $_GET['post'] ) ) ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'] . '&post=t' : $_SERVER['REQUEST_URI'] . '?post=t';
            header( 'Location: ' . $path );
            exit();
        }
        elseif( $this->session->check( $_SERVER['PHP_SELF'] . 'POST' ) || $this->session->check( $_SERVER['PHP_SELF'] . 'FILES' ) )
        {
            $this->formData = $this->session->get( $_SERVER['PHP_SELF'] . 'POST' );
            $_POST = $this->session->get( $_SERVER['PHP_SELF'] . 'POST' ); //just a convenience so validation object can acces to post data
            $_FILES = $this->session->get( $_SERVER['PHP_SELF'] . 'FILES' ); //just a convenience so we can acces upload file through $FILES
            $this->session->keepFlash( $_SERVER['PHP_SELF'] . 'POST' );
            $this->session->keepFlash( $_SERVER['PHP_SELF'] . 'FILES' );
            return true;
        }
        else
        {
            return false;
        }
    }




    /**
     * 
     * Method To Check For Existence Of Get Data
     * @access public
     * @return bool
     *
     */
    public function isGet()
    {
        return isset( $_GET );
    }




    /**
     *
     * Method to create start form tag
     * @access public
     * @return string
     */
    public function formStart( $isUpload, $action = null )
    {
        $form = null;
        $name = 'Form' . ( ++self::$instance );
        $action = ( is_null( $action ) ) ? $_SERVER['REQUEST_URI'] : $action;
        $form = '<form name="' . $name . '" method="' . $this->formMethod . '" action="' . $action . '"';
        $form .= ( $isUpload ) ? ' enctype="multipart/form-data">' : '>';
        return $form . PHP_EOL;
    }




    /**
     *
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