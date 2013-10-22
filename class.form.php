<?php

/**
 * Class To Generate Form Control
 */
class Form
{


    /**
     * @var string form method
     */
    protected $form_method;


    /**
     *
     * @var int instance of form
     */
    protected static $instance = 0;


    /**
     * @param string $form_method
     */
    public function __construct( $form_method = 'post' )
    {
        $this->form_method = ( is_null( $form_method ) ) ? 'post' : strtolower( $form_method );
    }


    /**
     * Method to create text control
     * @param string $name
     * @param mixed $defaultValue
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['placeholder']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function text( $name, $defaultValue = '', $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array(); /* Cast to an array if $attribute exist otherwise create an empty array */
        $formData = & $this->getFormData();
        $cfg = $this->configElement( $name, $attr );
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;

        #$_POST[data][]
        if( substr( $name, -2 ) == '[]' ) {
            $tmp_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( isset( $formData[$tmp_name] ) ) {
                $value = $formData[$tmp_name][0];
                array_shift( $formData[$tmp_name] );
            }
            else {
                $value = $defaultValue;
            }
        }
        else {
            $value = isset( $formData[$name] ) ? $formData[$name] : $defaultValue;
        }

        $text = sprintf( '<input type="text" name="%s" id="%s" class="%s" value="%s"  placeholder="%s" %s  %s>', $name, $cfg['id'], $cfg['class'], $value, $cfg['placeholder'], $cfg['readonly'], $cfg['disabled'] );
        return $text;
    }


    /**
     * Method to create textarea control
     * @param string $name
     * @param mixed $defaultValue
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['placeholder']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @param mixed $attr['cols']
     * @param mixed $attr['rows']
     * @return string
     */
    public function textarea( $name, $defaultValue = '', $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $formData = & $this->getFormData();
        $cfg = $this->configElement( $name, $attr );
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;

        #$_POST[data][]
        if( substr( $name, -2 ) == '[]' ) {
            $tmp_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( isset( $formData[$tmp_name] ) ) {
                $value = $formData[$tmp_name][0];
                array_shift( $formData[$tmp_name] );
            }
            else {
                $value = $defaultValue;
            }
        }
        else {
            $value = isset( $formData[$name] ) ? $formData[$name] : $defaultValue;
        }

        $textarea = sprintf( '<textarea name="%s" id="%s" class="%s" placeholder="%s" cols="%d" rows="%d" %s %s >%s</textarea>', $name, $cfg['id'], $cfg['class'], $cfg['placeholder'], $cfg['cols'], $cfg['rows'], $cfg['readonly'], $cfg['disabled'], $value );
        return $textarea;
    }


    /**
     *
     * Method to create password control
     * @param string $name
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['placeholder']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function password( $name, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );

        $password = sprintf( '<input type="password" name="%s" id="%s" class="%s" placeholder="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $cfg['placeholder'], $cfg['readonly'], $cfg['disabled'] );
        return $password;
    }


    /**
     * Method to create select control
     * @access public
     * @param string $name
     * @param array $options
     * @param string $selected marked option selected
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @param mixed $attr['multiple']
     * @param mixed $attr['size']
     * @return string
     *
     * $options is pass as follows:
     *
     * @example
     *
     * select( 'state', array('png'=>'Penang','kl'=>'K.Lumpur') )
     *
     * <select>
     * <option value='png'>Penang</option>
     * <option value='kl'>K.Lumpur</option>
     * </select>
     *
     * @example
     *
     * select('state', array('north'=>array('kdh'=>'Kedah', 'png'=>'Penang', 'prk'=>'Perak' ) ) )
     *
     * <select>
     * <optgroup label='north'>
     *  <option value='kdh'>Kedah</option>
     *  <option value='png'>Penang</option>
     *  <option value='prk'>Perak</option>
     * </optgroup>
     * <select>
     *
     * @example
     * select('speciality[]', array( 'economy'=>'Economy', 'technology'=> 'Technology', 'health'=>'Health' ), null, array( 'multiple'=>true ) )
     * [] allow php to collect multiple value from this select
     * @example
     * select('speciality[]', array( 'economy'=>'Economy', 'technology'=> 'Technology', 'health'=>'Health' ), array( 'economy', 'health' ), array( 'multiple'=>true ) )
     * pass array to $selected to mark multiple selection as selected
     *
     * <select>
     *  <option value="economy" selected="selected">Economy</option>
     *  <option value="technology">Technology</option>
     *  <option value="health" selected="selected">Health</option>
     * </select>
     *
     */
    public function select( $name, $options, $selected = null, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $formData = & $this->getFormData();
        $cfg = $this->configElement( $name, $attr );
        $selected = ( is_null( $selected ) ) ? '' : $selected;

        static $instance = 0;
        static $optionsList;
        $value = null;

        if( $instance == 0 ) /* need to check if this method call in sequential (calling this method twice) to make sure it dosent cache previous <option> */ {
            $optionsList = '';
        }

        #$_POST[data][]
        if( substr( $name, -2 ) == '[]' ) {
            $tmp_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( isset( $formData[$tmp_name] ) ) {
                foreach( $formData[$tmp_name] as $form_val ) {
                    $value[] = $form_val;
                    array_shift( $formData[$tmp_name] );
                }
            }
            else {
                $value = $selected;
            }
        }
        else {
            $value = isset( $formData[$name] ) ? $formData[$name] : $selected;
        }

        foreach( $options as $key => $val ) {
            if( is_array( $val ) ) {
                $instance++;
                $optionsList .= '<optgroup label="' . $key . '">' . PHP_EOL;
                self::select( $name, $val );
                $instance--;
                $optionsList .= '</optgroup>' . PHP_EOL;
            }
            else {
                $optionsList .= '<option value="' . $key . '" ';

                if( is_array( $value ) ) {
                    if( in_array( $key, $value ) ) {
                        $optionsList .= 'selected="selected"';
                    }
                }
                else {
                    if( $key == $value ) {
                        $optionsList .= 'selected="selected"';
                    }
                }
                $optionsList .= '>' . $val . '</option>' . PHP_EOL;
            }
        }

        $select = sprintf( '<select name="%s" id="%s" class="%s" %d %s %s %s>%s</select>', $name, $cfg['id'], $cfg['class'], $cfg['size'], $cfg['multiple'], $cfg['readonly'], $cfg['disabled'], $optionsList );
        return $select;
    }


    /**
     * Method to create radio control
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function radio( $name, $value, $checked = false, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $formData = & $this->getFormData();
        $cfg = $this->configElement( $name, $attr );
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $radio_checked = '';

        #$_POST[data][]
        if( substr( $name, -2 ) == '[]' ) {
            $tmp_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( !isset( $formData[$tmp_name] ) and $checked ) {
                $radio_checked = 'checked';
            }
            elseif( ( $formData[$tmp_name]  and $formData[$tmp_name][0] == $value ) ) {
                $radio_checked = 'checked';
            }
        }
        else {
            if( !$formData[$name] and $checked ) {
                $radio_checked = 'checked';
            }
            elseif( ( $formData[$name]  and $formData[$name] == $value ) ) {
                $radio_checked = 'checked';
            }
        }

        $radio = sprintf( '<input type="radio" name="%s" id="%s" class="%s" value="%s" %s %s %s>', $name, $cfg['id'], $cfg['class'], $value, $radio_checked, $cfg['readonly'], $cfg['disabled'] );
        return $radio;
    }


    /**
     * Method to create checkbox control
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function checkbox( $name, $value, $checked = false, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $formData = & $this->getFormData();
        $cfg = $this->configElement( $name, $attr );
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $checkbox_checked = '';

        #form with same name $_POST[data][]
        if( substr( $name, -2 ) == '[]' ) {
            $tmp_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( ( isset( $formData[$tmp_name] ) and $formData[$tmp_name][0] == $value ) or ( !$formData && $checked ) ) {

                if( isset( $formData[$tmp_name] ) ) {
                    array_shift( $formData[$tmp_name] );
                }
                $checkbox_checked = 'checked';
            }
        }
        else {
            if( ( isset( $formData[$name] )  and $formData[$name] == $value ) or ( !$formData && $checked ) ) {
                $checkbox_checked = 'checked';
            }
        }

        $checkbox = sprintf( '<input type="checkbox" name="%s" id="%s" class="%s" value="%s" %s %s %s>', $name, $cfg['id'], $cfg['class'], $value, $checkbox_checked, $cfg['readonly'], $cfg['disabled'] );
        return $checkbox;
    }


    /**
     * Method to create file control (for upload)
     * @param string $name
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function file( $name, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );

        $file = sprintf( '<input type="file" name="%s" id="%s" class="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $cfg['readonly'], $cfg['disabled'] );
        return $file;
    }


    /**
     * Method to create hidden control
     * @param mixed $name
     * @param mixed $defaultValue
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @return string
     */
    public function hidden( $name, $defaultValue, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $formData = & $this->getFormData();
        $cfg = $this->configElement( $name, $attr );

        #$_POST[data][]
        if( substr( $name, -2 ) == '[]' ) {
            $tmp_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( isset( $formData[$tmp_name] ) ) {
                $value = $formData[$tmp_name][0];
                array_shift( $formData[$tmp_name] );
            }
            else {
                $value = $defaultValue;
            }
        }
        else {
            $value = isset( $formData[$name] ) ? $formData[$name] : $defaultValue;
        }

        $hidden = sprintf( '<input type="hidden" name="%s" value="%s" id="%s" class="%s">', $name, $value, $cfg['id'], $cfg['class'] );
        return $hidden;
    }


    /**
     *
     * Method to create button control
     * @param mixed $name
     * @param mixed $value
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function button( $name, $value, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );

        $button = sprintf( '<input type="button" name="%s" value="%s" id="%s" class="%s" %s %s>', $name, $value, $cfg['id'], $cfg['class'], $cfg['readonly'], $cfg['disabled'] );
        return $button;
    }


    /**
     * Method to create submit control
     * @param string $name
     * @param mixed $value
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['disabled'] remove from $_POST data
     * @param mixed $attr['readonly']
     * @return string
     */
    public function submit( $name, $value = 'Submit', $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );

        $submit = sprintf( '<input type="submit" name="%s" id="%s" class="%s" value="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $value, $cfg['readonly'], $cfg['disabled'] );
        return $submit;
    }


    /**
     * Method to create start form tag
     * @param string $action
     * If $_GET data exist in the $action, it will be merge with $_GET from requesting script
     * $_GET from requesting script will always overwrite $_GET in $action if they both have same key
     * $action = 'index.php?id=1' and requesting script is index.php?id=3&lang=en, resulting to index.php?id=3&lang=en
     * @param mixed $attr['id']
     * @param mixed $attr['class']
     * @param mixed $attr['target']
     * @param bool $attr[upload]
     * @return string
     */
    public function formStart( $action, $attr = array() )
    {
        $action = $this->formAction( $action );
        $formName = 'Form' . ++self::$instance;
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $formName, $attr );

        $formStart = sprintf( '<form name="%s" id="%s" class="%s" method="%s" action="%s" target="%s" %s>', $formName, $cfg['id'], $cfg['class'], $this->form_method, $action, $cfg['target'], $cfg['upload'] );
        return $formStart;
    }


    /**
     * Method to create end form tag
     * @return string
     */
    public function formEnd()
    {
        return '</form>';
    }


    /**
     * Populated form data
     * @return mixed
     */
    protected function &getFormData()
    {
        if( $this->form_method == 'post' ) {
            return $_POST;
        }
        else {
            return $_GET;
        }

    }


    /**
     * Method to create form action
     * @param $action
     * @return string
     */
    protected function formAction( $action )
    {
        $url = parse_url( $action );
        $url = $url['path'] . '?';
        $action_query = $this->extractQueryString( $action );
        $request_query = $this->extractQueryString( $_SERVER['REQUEST_URI'] );
        $collections = array_merge( $action_query, $request_query );

        foreach( $collections as $key => $val ) {
            $url .= $key . '=' . $val . '&';
        }

        $url = rtrim( $url, '&' );
        return $url;
    }


    /**
     * Method to extract $_GET data from url
     * @param $url
     * @return array
     */
    protected function extractQueryString( $url )
    {
        $url = parse_url( $url );
        $collections = [ ];

        if( isset( $url['query'] ) ) {
            $parts = explode( '&', $url['query'] );

            foreach( $parts as $part ) {
                list( $key, $val ) = explode( '=', $part );
                $collections[$key] = $val;
            }
        }
        return $collections;
    }


    protected function configElement( $name, array $attr )
    {
        $cfg = array(
            'id' => $name . '_id',
            'class' => $name . '_class',
            'disabled' => '',
            'readonly' => '',
            'placeholder' => '',
            'cols' => 20,
            'rows' => 3,
            'multiple' => null,
            'size' => null,
            'target' => '',
            'upload' => false
        );

        return array_merge( $cfg, $attr );

    }


}


?>