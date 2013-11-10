<?php

/**
 * Class To Generate Html Form Element
 */

namespace utility;

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
     * Create html text element
     *
     * @param string $name
     * @param string $defaultValue
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * string $attr['placeholder']
     * bool $attr['disabled'] remove from $_POST
     * bool $attr['readonly']
     *
     * @return string
     */
    public function text( $name, $defaultValue = null, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array(); #Cast to an array if $attribute exist otherwise create an empty array
        $cfg = $this->configElement( $name, $attr );
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;
        $value = $this->getTextValue( $name, $defaultValue );
        return $this->removeExtraSpaces( sprintf( '<input type="text" name="%s" id="%s" class="%s" value="%s" placeholder="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $value, $cfg['placeholder'], $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create html textarea element
     *
     * @param string $name
     * @param string $defaultValue
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * string $attr['placeholder']
     * bool $attr['disabled']
     * bool $attr['readonly']
     * int $attr['cols']
     * int $attr['rows']
     *
     * @return string
     */
    public function textarea( $name, $defaultValue = null, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        $defaultValue = ( is_null( $defaultValue ) ) ? '' : $defaultValue;
        $value = $this->getTextValue( $name, $defaultValue );
        return $this->removeExtraSpaces( sprintf( '<textarea name="%s" id="%s" class="%s" placeholder="%s" cols="%d" rows="%d" %s %s>%s</textarea>', $name, $cfg['id'], $cfg['class'], $cfg['placeholder'], $cfg['cols'], $cfg['rows'], $cfg['readonly'], $cfg['disabled'], $value ) );
    }


    /**
     * Create html password element
     *
     * @param string $name
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * string $attr['placeholder']
     * bool $attr['disabled']
     * bool $attr['readonly']
     *
     * @return string
     */
    public function password( $name, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        return $this->removeExtraSpaces( sprintf( '<input type="password" name="%s" id="%s" class="%s" placeholder="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $cfg['placeholder'], $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create html select element
     *
     * @param string $name
     * @param array $options
     * @param string|array $selected marked option selected
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * bool $attr['disabled']
     * bool $attr['readonly']
     * bool $attr['multiple']
     * int $attr['size']
     *
     * @return string
     *
     *
     * $options is pass as follows:
     *
     * select( 'state', array('png'=>'Penang','kl'=>'K.Lumpur') )
     *
     * <select>
     *  <option value='png'>Penang</option>
     *  <option value='kl'>K.Lumpur</option>
     * </select>
     *
     *
     * select('state', array('north'=>array('kdh'=>'Kedah', 'png'=>'Penang', 'prk'=>'Perak' ) ) )
     *
     * <select>
     *  <optgroup label='north'>
     *   <option value='kdh'>Kedah</option>
     *   <option value='png'>Penang</option>
     *   <option value='prk'>Perak</option>
     *  </optgroup>
     * </select>
     *
     *
     * [] allow php to collect multiple value from this select element, with its multiple attribute set to true
     * select('speciality[]', array( 'economy'=>'Economy', 'technology'=> 'Technology', 'health'=>'Health' ), null, array( 'multiple'=>true ) )
     *
     *
     * Pass array to $selected to mark multiple selection as selected
     * select('speciality[]', array( 'economy'=>'Economy', 'technology'=> 'Technology', 'health'=>'Health' ), array( 'economy', 'health' ), array( 'multiple'=>true ) )
     *
     * <select>
     *  <option value="economy" selected="selected">Economy</option>
     *  <option value="technology">Technology</option>
     *  <option value="health" selected="selected">Health</option>
     * </select>
     *
     */
    public function select( $name, $options, $selected = null, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        $name = $this->setSelectElementName( $name );
        $selected = ( is_null( $selected ) ) ? '' : $selected;

        static $instance = 0;
        static $optionsList = null;
        $value = null;

        #Need to check if this method call in sequential (calling this method twice) to make sure it dosent cache previous <option>
        if( $instance == 0 ){
            $optionsList = null;
        }

        $value = $this->getSelectValue( $name, $selected );
        $optionsList = $this->buildSelectOption( $name, $options, $instance, $optionsList, $value );
        return $this->removeExtraSpaces( sprintf( '<select name="%s" id="%s" class="%s" %s %s %s %s>%s</select>', $name, $cfg['id'], $cfg['class'], $cfg['size'], $cfg['multiple'], $cfg['readonly'], $cfg['disabled'], $optionsList ) );
    }


    /**
     * Create html radio element
     *
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * bool $attr['disabled']
     * bool $attr['readonly']
     *
     * @return string
     */
    public function radio( $name, $value, $checked = false, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $radio_checked = $this->getRadioValue( $name, $value, $checked );
        return $this->removeExtraSpaces( sprintf( '<input type="radio" name="%s" id="%s" class="%s" value="%s" %s %s %s>', $name, $cfg['id'], $cfg['class'], $value, $radio_checked, $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create html checkbox element
     *
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * bool $attr['disabled']
     * bool $attr['readonly']
     *
     * @return string
     */
    public function checkbox( $name, $value, $checked = false, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        $checked = ( is_null( $checked ) ) ? false : ( boolean )$checked;
        $checkbox_checked = $this->getCheckboxValue( $name, $value, $checked );
        return $this->removeExtraSpaces( sprintf( '<input type="checkbox" name="%s" id="%s" class="%s" value="%s" %s %s %s>', $name, $cfg['id'], $cfg['class'], $value, $checkbox_checked, $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create html file element(for upload)
     *
     * @param string $name
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * bool $attr['disabled'] remove from $_POST data
     * bool $attr['readonly']
     *
     * @return string
     */
    public function file( $name, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        return $this->removeExtraSpaces( sprintf( '<input type="file" name="%s" id="%s" class="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create html hidden element
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     *
     * @return string
     */
    public function hidden( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        $value = $this->getTextValue( $name, $value );
        return $this->removeExtraSpaces( sprintf( '<input type="hidden" name="%s" value="%s" id="%s" class="%s">', $name, $value, $cfg['id'], $cfg['class'] ) );
    }


    /**
     *
     * Create html button element
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * bool $attr['disabled']
     * bool $attr['readonly']
     *
     * @return string
     */
    public function button( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        return $this->removeExtraSpaces( sprintf( '<input type="button" name="%s" value="%s" id="%s" class="%s" %s %s>', $name, $value, $cfg['id'], $cfg['class'], $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create html submit element
     *
     * @param string $name
     * @param string $value
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * bool $attr['disabled']
     * bool $attr['readonly']
     *
     * @return string
     */
    public function submit( $name, $value = 'Submit', array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $cfg = $this->configElement( $name, $attr );
        return $this->removeExtraSpaces( sprintf( '<input type="submit" name="%s" id="%s" class="%s" value="%s" %s %s>', $name, $cfg['id'], $cfg['class'], $value, $cfg['readonly'], $cfg['disabled'] ) );
    }


    /**
     * Create opening html form element
     * If GET data exist in the $action, it will be merge with GET from requesting script
     * GET data from requesting script will always overwrite GET in $action if they both have same key
     *
     * $action = 'index.php?id=1' and requesting script is index.php?id=3&lang=en, resulting to index.php?id=3&lang=en
     *
     * @param string $action
     * @param array $attr
     *
     * string $attr['id']
     * string $attr['class']
     * string $attr['target']
     * bool $attr[upload]
     *
     * @return string
     */
    public function formStart( $action, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $action = $this->formAction( $action, @$_SERVER['REQUEST_URI'] );
        $formName = 'form' . ++self::$instance;
        $cfg = $this->configElement( $formName, $attr );
        return $this->removeExtraSpaces( sprintf( '<form name="%s" id="%s" class="%s" method="%s" action="%s" target="%s" %s>', $formName, $cfg['id'], $cfg['class'], $this->form_method, $action, $cfg['target'], $cfg['upload'] ) );
    }


    /**
     * Create closing html form element
     * @return string
     */
    public function formEnd()
    {
        return '</form>';
    }


    /**
     * Get form data
     *
     * @return array
     */
    protected function &getFormData()
    {
        if( $this->form_method == 'post' ){
            return $_POST;
        }
        else{
            return $_GET;
        }
    }


    /**
     * Create form action
     *
     * @param string $action
     * @param string $request_script
     * @return string
     */
    protected function formAction( $action, $request_script )
    {
        $url = $this->getUrl( $action, $request_script );
        $action_query = $this->extractQueryString( $action );
        $request_query = $this->extractQueryString( $request_script );
        $collections = array_merge( $action_query, $request_query );

        foreach( $collections as $key => $val ){
            $url .= $key . '=' . $val . '&';
        }

        $url = rtrim( $url, '&' );
        return $url;
    }


    /**
     * Extract $_GET data from url
     *
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
     * Passing null to any attribute, yield to default attribute in $cfg
     *
     * @param string $name
     * @param array $attr
     * @return array
     */
    protected function configElement( $name, array $attr )
    {
        $attr = $this->castConfigElementAttribute( $attr );
        $attr = $this->fixStandaloneConfigElementAttribute( $attr );
        $attr = $this->removeEmptyEconfigElementAttribute( $attr );

        $cfg = array( 'id' => $this->getElementId( $name ), 'class' => $this->getElementClass( $name ),
            'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3,
            'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null
        );

        return array_merge( $cfg, $attr );

    }


    /**
     * Get value for all text related html element
     *
     * @param string $name
     * @param string $defaultValue
     * @return string
     */
    protected function getTextValue( $name, $defaultValue )
    {
        $value = null;
        $formData = & $this->getFormData();
        $elem_name = $this->getElementName( $name );

        if( isset( $formData[$elem_name] ) ){
            if( $this->isElementAnArray( $name ) ){
                $value = $formData[$elem_name][0];
                array_shift( $formData[$elem_name] );
            }
            else{
                $value = $formData[$elem_name];
            }
        }
        else{
            $value = $defaultValue;
        }
        return $value;

    }


    /**
     * Get value for html select element
     *
     * @param string $name
     * @param string $selected
     * @return string|array
     */
    protected function getSelectValue( $name, $selected )
    {
        $value = null;
        $formData = & $this->getFormData();
        $elem_name = $this->getElementName( $name );

        if( isset( $formData[$elem_name] ) ){
            if( $this->isElementAnArray( $name ) ){
                foreach( $formData[$elem_name] as $elem ){
                    foreach( $elem as $key => $val ){
                        $value[] = $val;
                    }
                    array_shift( $formData[$elem_name] );
                    break;
                }
            }
            else{
                $value = $formData[$elem_name];
            }
        }
        else{
            $value = $selected;
        }

        return $value;
    }


    /**
     * Get value for html radio element
     *
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @return string
     */
    protected function getRadioValue( $name, $value, $checked )
    {
        $radio_checked = null;
        $formData = & $this->getFormData();
        $elem_name = $this->getElementName( $name );

        if( isset( $formData[$elem_name] ) ){
            if( $this->isElementAnArray( $name ) && $formData[$elem_name][0] == $value ){
                $radio_checked = 'checked';
                array_shift( $formData[$elem_name] );
            }
            elseif( $formData[$elem_name] == $value ){

                $radio_checked = 'checked';
            }
        }
        elseif( $checked ){
            $radio_checked = 'checked';
        }

        return $radio_checked;
    }


    /**
     * Get value for html checkbox element
     *
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @return string
     */
    protected function getCheckboxValue( $name, $value, $checked )
    {
        $checkbox_checked = null;
        $formData = & $this->getFormData();
        $elem_name = $this->getElementName( $name );

        if( isset( $formData[$elem_name] ) ){
            if( $this->isElementAnArray( $name ) && $formData[$elem_name][0] == $value ){
                $checkbox_checked = 'checked';
                array_shift( $formData[$elem_name] );
            }
            elseif( $formData[$elem_name] == $value ){
                $checkbox_checked = 'checked';
            }
        }
        elseif( $checked ){
            $checkbox_checked = 'checked';

        }
        return $checkbox_checked;
    }


    /**
     * Build option list for html select element
     *
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
                if( is_array( $value ) && in_array( $key, $value ) ){
                    $optionsList .= ' selected';
                }
                elseif( $key == $value ){
                    $optionsList .= ' selected';
                }
                $optionsList .= sprintf( '>%s</option>', $val );
            }
        }
        return $optionsList;
    }


    /**
     * Cast html element attribute property
     *
     * @param array $attr
     * @return array
     */
    protected function castConfigElementAttribute( array $attr )
    {
        @$attr['id'] = ( string)$attr['id'];
        @$attr['class'] = (string)$attr['class'];
        @$attr['disabled'] = (boolean)$attr['disabled'];
        @$attr['readonly'] = (boolean)$attr['readonly'];
        @$attr['placeholder'] = (string)$attr['placeholder'];
        @$attr['cols'] = is_bool( $attr['cols'] ) ? 0 : (int)$attr['cols'];
        @$attr['rows'] = is_bool( $attr['rows'] ) ? 0 : (int)$attr['rows'];
        @$attr['multiple'] = (boolean)$attr['multiple'];
        @$attr['size'] = is_bool( $attr['size'] ) ? 0 : (int)$attr['size'];;
        @$attr['target'] = (string)$attr['target'];
        @$attr['upload'] = (boolean)$attr['upload'];
        return $attr;
    }


    /**
     * Fix html element standalone property
     *
     * @param array $attr
     * @return array
     */
    protected function fixStandaloneConfigElementAttribute( array $attr )
    {
        $attr['disabled'] = ( $attr['disabled'] ) ? 'disabled' : null;
        $attr['readonly'] = ( $attr['readonly'] ) ? 'readonly' : null;
        $attr['multiple'] = ( $attr['multiple'] ) ? 'multiple' : null;
        $attr['upload'] = ( $attr['upload'] ) ? 'enctype="multipart/form-data"' : null;
        $attr['size'] = ( is_int( $attr['size'] ) && $attr['size'] != 0 ) ? sprintf( 'size="%d"', $attr['size'] ) : null;
        return $attr;
    }


    /**
     * Remove any empty|broken html element property
     *
     * @param array $attr
     * @return array
     */
    protected function removeEmptyEconfigElementAttribute( array $attr )
    {
        if( !$attr['id'] ) unset( $attr['id'] );
        if( !$attr['class'] ) unset( $attr['class'] );
        if( !$attr['disabled'] ) unset( $attr['disabled'] );
        if( !$attr['readonly'] ) unset( $attr['readonly'] );
        if( !$attr['placeholder'] ) unset( $attr['placeholder'] );
        if( !$attr['cols'] ) unset( $attr['cols'] );
        if( !$attr['rows'] ) unset( $attr['rows'] );
        if( !$attr['multiple'] ) unset( $attr['multiple'] );
        if( !$attr['size'] ) unset( $attr['size'] );
        if( !$attr['target'] ) unset( $attr['target'] );
        if( !$attr['upload'] ) unset( $attr['upload'] );

        return $attr;
    }


    /**
     * Remove extra spaces around any generated html element
     *
     * @param $text
     * @return string
     */
    protected function removeExtraSpaces( $text )
    {
        return preg_replace_callback( "#(\s+(?=>))|(\s{2,}(?!>))#", function ( $match ) {
            if( @$match[1] ){
                return '';
            }
            if( @$match[2] ){
                return ' ';
            }
        }, $text );

    }


    /**
     * Prepare url for combining with GET
     *
     * @param string $action
     * @param string $request_script
     * @return mixed|string
     */
    protected function getUrl( $action, $request_script )
    {
        $action = parse_url( $action );
        $request_script = parse_url( $request_script );

        if( isset( $action['query'] ) || isset( $request_script['query'] ) ){

            return $action['path'] . '?';
        }

        return $action['path'];
    }


    /**
     * Check if element is an array
     *
     * @param string $name
     * @return bool
     */
    protected function isElementAnArray( $name )
    {
        return preg_match( '#\[.*?]$#', $name );
    }


    /**
     * Get element name
     *
     * @param string $name
     * @return string
     */
    protected function getElementName( $name )
    {
        return substr( $name, 0, ( strpos( $name, '[' ) == false ) ? strlen( $name ) : strpos( $name, '[' ) );
    }


    /**
     * Get element css class
     *
     * @param string $name
     * @return string
     */
    protected function getElementClass( $name )
    {
        return preg_replace( '#\[]#', '', $name . '_class' );
    }


    /**
     * Get element css id
     *
     * @param string $name
     * @return string
     */
    protected function getElementId( $name )
    {
        static $instance = 0;
        if( $this->isElementAnArray( $name ) ){
            $name .= ++$instance;
        }
        return preg_replace( '#\[]#', '', $name . '_id' );
    }


    /**
     * Set html select element name
     *
     * @param string $name
     * @return string
     */
    protected function setSelectElementName( $name )
    {
        static $instance = 0;
        static $cache_name = null;

        if( is_null( $cache_name ) ){
            $cache_name = $name;
        }

        if( $cache_name != $name ){
            $instance = 0;
            $cache_name = $name;
        }

        if( $this->isElementAnArray( $name ) ){
            $name = $this->getElementName( $name ) . sprintf( '[%s][]', $instance++ );
        }
        return $name;
    }

}


?>