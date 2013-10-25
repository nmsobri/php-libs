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
     * Create text control
     * @param string $name
     * @param string $defaultValue
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param string $attr ['placeholder']
     * @param bool $attr ['disabled'] remove from $_POST data
     * @param bool $attr ['readonly']
     * @return string
     */
    public function text( $name, $defaultValue = '', $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array(); #Cast to an array if $attribute exist otherwise create an empty array
        $cfg = $this->configElement( $name, $attr );
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;
        $value = $this->getTextValue( $name, $defaultValue );
        return sprintf( '<input type="text" name="%s" id="%s" class="%s" value="%s"  placeholder="%s" %s  %s>', $name, $cfg['id'], $cfg['class'], $value, $cfg['placeholder'], $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create textarea control
     * @param string $name
     * @param string $defaultValue
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param string $attr ['placeholder']
     * @param bool $attr ['disabled']
     * @param bool $attr ['readonly']
     * @param int $attr ['cols']
     * @param int $attr ['rows']
     * @return string
     */
    public function textarea( $name, $defaultValue = '', $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;
        $value = $this->getTextValue( $name, $defaultValue );
        return sprintf( '<textarea name="%s" id="%s" class="%s" placeholder="%s" cols="%d" rows="%d" %s %s>%s</textarea>', $name, $cfg['id'], $cfg['class'], $cfg['placeholder'], $cfg['cols'], $cfg['rows'], $cfg['readonly'], $cfg['disabled'], $value );
    }


    /**
     *
     * Create password control
     * @param string $name
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param string $attr ['placeholder']
     * @param bool $attr ['disabled'] remove from $_POST data
     * @param bool $attr ['readonly']
     * @return string
     */
    public function password( $name, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        return sprintf( '<input type="password" name="%s" id="%s" class="%s" placeholder="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $cfg['placeholder'], $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create select control
     * @access public
     * @param string $name
     * @param array $options
     * @param string $selected marked option selected
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param bool $attr ['disabled']
     * @param bool $attr ['readonly']
     * @param bool $attr ['multiple']
     * @param int $attr ['size']
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
        $cfg = $this->configElement( $name, $attr );
        $selected = ( is_null( $selected ) ) ? '' : $selected;

        static $instance = 0;
        static $optionsList = null;
        $value = null;

        if( $instance == 0 ) /* need to check if this method call in sequential (calling this method twice) to make sure it dosent cache previous <option> */{
            $optionsList = '';
        }

        $value = $this->getSelectValue( $name, $selected );
        $optionsList = $this->buildSelectOption( $name, $options, $instance, $optionsList, $value );
        return sprintf( '<select name="%s" id="%s" class="%s" %d %s %s %s>%s</select>', $name, $cfg['id'], $cfg['class'], $cfg['size'], $cfg['multiple'], $cfg['readonly'], $cfg['disabled'], $optionsList );
    }


    /**
     * Create radio control
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param bool $attr ['disabled']
     * @param bool $attr ['readonly']
     * @return string
     */
    public function radio( $name, $value, $checked = false, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $radio_checked = $this->getRadioValue( $name, $value, $checked );
        return sprintf( '<input type="radio" name="%s" id="%s" class="%s" value="%s" %s %s %s>', $name, $cfg['id'], $cfg['class'], $value, $radio_checked, $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create checkbox control
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param bool $attr ['disabled'] remove from $_POST data
     * @param bool $attr ['readonly']
     * @return string
     */
    public function checkbox( $name, $value, $checked = false, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $checkbox_checked = $this->getCheckboxValue( $name, $value, $checked );
        return sprintf( '<input type="checkbox" name="%s" id="%s" class="%s" value="%s" %s %s %s>', $name, $cfg['id'], $cfg['class'], $value, $checkbox_checked, $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create file control (for upload)
     * @param string $name
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param bool $attr ['disabled'] remove from $_POST data
     * @param bool $attr ['readonly']
     * @return string
     */
    public function file( $name, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        return sprintf( '<input type="file" name="%s" id="%s" class="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create hidden control
     * @param string $name
     * @param string $defaultValue
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @return string
     */
    public function hidden( $name, $defaultValue, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        $value = $this->getTextValue( $name, $defaultValue );
        return sprintf( '<input type="hidden" name="%s" value="%s" id="%s" class="%s">', $name, $value, $cfg['id'], $cfg['class'] );
    }


    /**
     *
     * Create button control
     * @param string $name
     * @param string $value
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param bool $attr ['disabled']
     * @param bool $attr ['readonly']
     * @return string
     */
    public function button( $name, $value, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        return sprintf( '<input type="button" name="%s" value="%s" id="%s" class="%s" %s %s>', $name, $value, $cfg['id'], $cfg['class'], $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create submit control
     * @param string $name
     * @param string $value
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param bool $attr ['disabled']
     * @param bool $attr ['readonly']
     * @return string
     */
    public function submit( $name, $value = 'Submit', $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $cfg = $this->configElement( $name, $attr );
        return sprintf( '<input type="submit" name="%s" id="%s" class="%s" value="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $value, $cfg['readonly'], $cfg['disabled'] );
    }


    /**
     * Create start form tag
     * If GET data exist in the $action, it will be merge with GET from requesting script
     * GET data from requesting script will always overwrite GET in $action if they both have same key
     * Example $action = 'index.php?id=1' and requesting script is index.php?id=3&lang=en, resulting to index.php?id=3&lang=en
     * @param string $action
     * @param string $attr ['id']
     * @param string $attr ['class']
     * @param string $attr ['target']
     * @param bool $attr [upload]
     * @return string
     */
    public function formStart( $action, $attr = array() )
    {
        $attr = ( !is_null( $attr ) ) ? ( array )$attr : array();
        $action = $this->formAction( $action );
        $formName = 'Form' . ++self::$instance;
        $cfg = $this->configElement( $formName, $attr );
        return sprintf( '<form name="%s" id="%s" class="%s" method="%s" action="%s" target="%s" %s>', $formName, $cfg['id'], $cfg['class'], $this->form_method, $action, $cfg['target'], $cfg['upload'] );
    }


    /**
     * Create end form tag
     * @return string
     */
    public function formEnd()
    {
        return '</form>';
    }


    /**
     * Get form data
     * @return mixed
     */
    protected function &getFormData()
    {
        return ( $this->form_method == 'post' ) ? $_POST : $_GET;
    }


    /**
     * Create form action
     * @param string $action
     * @return string
     */
    protected function formAction( $action )
    {
        $url = parse_url( $action );
        $url = $url['path'] . '?';
        $action_query = $this->extractQueryString( $action );
        $request_query = $this->extractQueryString( $_SERVER['REQUEST_URI'] );
        $collections = array_merge( $action_query, $request_query );

        foreach( $collections as $key => $val ){
            $url .= $key . '=' . $val . '&';
        }

        $url = rtrim( $url, '&' );
        return $url;
    }


    /**
     * Extract $_GET data from url
     * @param string $url
     * @return array
     */
    protected function extractQueryString( $url )
    {
        $url = parse_url( $url );
        $collections = [ ];

        if( isset( $url['query'] ) ){
            $parts = explode( '&', $url['query'] );

            foreach( $parts as $part ){
                list( $key, $val ) = explode( '=', $part );
                $collections[$key] = $val;
            }
        }
        return $collections;
    }


    /**
     * Populate html element attribute
     * @param string $name
     * @param array $attr
     * @return array
     */
    protected function configElement( $name, array $attr )
    {
        #Overwrite the attribute because this attribute cant be use by itself
        $attr['disabled'] = ( $attr['disabled'] == true ) ? 'disabled' : null;
        $attr['readonly'] = ( $attr['readonly'] == true ) ? 'readonly' : null;
        $attr['multiple'] = ( $attr['multiple'] == true ) ? 'multiple' : null;
        $attr['upload'] = ( $attr['upload'] == true ) ? 'enctype="multipart/form-data"' : null;
        $attr['size'] = ( is_int( $attr['size'] ) ) ? sprintf( 'size="%d"', $attr['size'] ) : null;

        $cfg = array( 'id' => $name . '_id', 'class' => $name . '_class', 'disabled' => null,
            'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3,
            'multiple' => null, 'size' => null, 'target' => null, 'upload' => null
        );

        return array_merge( $cfg, $attr );

    }


    /**
     * Get value for all text related html element
     * @param string $name
     * @param string $defaultValue
     * @return string
     */
    protected function getTextValue( $name, $defaultValue )
    {
        #$_POST[data][]
        $formData = & $this->getFormData();
        if( substr( $name, -2 ) == '[]' ){
            $elem_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( isset( $formData[$elem_name] ) ){
                $value = $formData[$elem_name][0];
                array_shift( $formData[$elem_name] );
                return $value;
            }
            else{
                $value = $defaultValue;
                return $value;
            }
        }
        else{
            $value = isset( $formData[$name] ) ? $formData[$name] : $defaultValue;
            return $value;
        }
    }


    /**
     * Get value for html select
     * @param string $name
     * @param string $selected
     * @return mixed
     */
    protected function getSelectValue( $name, $selected )
    {
        #$_POST[data][]
        $formData = & $this->getFormData();
        if( substr( $name, -2 ) == '[]' ){
            $elem_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( isset( $formData[$elem_name] ) ){
                foreach( $formData[$elem_name] as $form_val ){
                    $value[] = $form_val;
                    array_shift( $formData[$elem_name] );
                }
                return $value;
            }
            else{
                $value = $selected;
                return $value;
            }
        }
        else{
            $value = isset( $formData[$name] ) ? $formData[$name] : $selected;
            return $value;
        }
    }


    /**
     * Get value for html radio
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @return string
     */
    protected function getRadioValue( $name, $value, $checked )
    {
        #$_POST[data][]
        $formData = & $this->getFormData();
        if( substr( $name, -2 ) == '[]' ){
            $elem_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( !isset( $formData[$elem_name] ) and $checked ){
                $radio_checked = 'checked';
                return $radio_checked;
            }
            elseif( ( $formData[$elem_name]  and $formData[$elem_name][0] == $value ) ){
                $radio_checked = 'checked';
                return $radio_checked;
            }
            return $radio_checked;
        }
        else{
            if( !$formData[$name] and $checked ){
                $radio_checked = 'checked';
                return $radio_checked;
            }
            elseif( ( $formData[$name]  and $formData[$name] == $value ) ){
                $radio_checked = 'checked';
                return $radio_checked;
            }
            return $radio_checked;
        }
    }


    /**
     * Get value for html checkbox
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @return string
     */
    protected function getCheckboxValue( $name, $value, $checked )
    {
        #$_POST[data][]
        $formData = & $this->getFormData();
        if( substr( $name, -2 ) == '[]' ){
            $elem_name = substr( $name, 0, strpos( $name, '[]' ) );
            if( ( isset( $formData[$elem_name] ) and $formData[$elem_name][0] == $value ) or ( !$formData && $checked ) ){

                if( isset( $formData[$elem_name] ) ){
                    array_shift( $formData[$elem_name] );
                }
                $checkbox_checked = 'checked';
                return $checkbox_checked;
            }
            return $checkbox_checked;
        }
        else{
            if( ( isset( $formData[$name] )  and $formData[$name] == $value ) or ( !$formData && $checked ) ){
                $checkbox_checked = 'checked';
                return $checkbox_checked;
            }
            return $checkbox_checked;
        }
    }


    /**
     * Build select option list
     * @param string $name
     * @param array $options
     * @param int $instance
     * @param string $optionsList
     * @param mixed $value
     * @return array
     */
    protected function buildSelectOption( $name, $options, &$instance, &$optionsList, $value )
    {
        foreach( $options as $key => $val ){
            if( is_array( $val ) ){
                $instance++;
                $optionsList .= sprintf( '<optgroup label="%s">', $key );
                $this->select( $name, $val );
                $instance--;
                $optionsList .= '</optgroup>';
            }
            else{
                $optionsList .= sprintf( '<option value="%s"', $key );

                if( is_array( $value ) ){
                    if( in_array( $key, $value ) ){
                        $optionsList .= ' selected';
                    }
                }
                else{
                    if( $key == $value ){
                        $optionsList .= ' selected';
                    }
                }
                $optionsList .= sprintf( '>%s</option>', $val );
            }
        }
        return $optionsList;
    }


}


?>