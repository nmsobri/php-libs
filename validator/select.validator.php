<?php

class SelectValidator extends ValidatorStrategy
{
    /**
     * Validation for select field
     * @param string $name
     * @param mixed $value
     * @param $attr ['errors']['empty']
     * @param $attr ['required']
     * @param $attr ['field']
     *
     * @example new SelectValidator( 'country' , $_POST['country'], array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidator( $name, $value, $attr );
    }


    /**
     * Perform validation
     * @return bool
     */
    public function isValid()
    {
        if( empty( $this->data['value'] ) ){
            if( $this->data['required'] ){
                $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_NOT_SELECT, array( $this->data['field'] ) );
                return false;
            }
        }
        return true;
    }


    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}