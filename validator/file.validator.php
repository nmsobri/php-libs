<?php

class FileValidator extends ValidatorStrategy
{

    /**
     * Validation for file
     *
     * @param string $name
     * @param mixed $value $_FILES['elem']
     * @param array $ext array('pdf','doc','ppt')
     * @param array $attr
     *
     * bool $attr['required']
     * string $attr['field']
     * string $attr['errors']['empty']
     * string $attr['errors']['extension']
     *
     * new FileValidator( 'user_image' , $_FILES['image'] )
     */
    public function __construct( $name, $value, array $ext = null, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidatorGenericAttr( $name, $value, $ext, $attr );
    }


    /**
     * @return bool
     */
    public function isValid()
    {
        $ext = pathinfo( $this->data['value']['name'], PATHINFO_EXTENSION );

        if( empty( $this->data['value']['name'] ) ){
            if( $this->data['required'] ){
                $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_FILE_EMPTY, array( $this->data['field'] ) );
                return false;
            }
            return true;
        }

        if( !in_array( $ext, $this->data['extension'] ) ){
            $this->messages = ( $this->data['errors']['extension'] ) ? $this->data['errors']['extension'] : $this->errorText( ValidatorStrategy::E_INVALID_EXTENSION, array( $this->data['field'], implode( ', ', $this->data['extension'] ) ) );
            return false;
        }

        return true;
    }


    /**
     * @param $name
     * @param $value
     * @param array $ext
     * @param $attr
     */
    protected function configValidatorGenericAttr( $name, $value, $ext, $attr )
    {
        parent::configValidatorGenericAttr( $name, $value, $attr );
        $this->data['extension'] = $ext;
    }


    /**
     * @param array $attr
     * @return array
     */
    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'extension' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}
