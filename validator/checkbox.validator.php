<?php

class CheckBoxValidator extends ValidatorStrategy
{

    /**
     * Validation for html checkbox element
     * @param string $name
     * @param string $value
     * @param string $attr ['field']
     * @param string $attr ['errors']['empty']
     * @param bool $attr ['required']
     *
     * new CheckBoxValidator( 'subscribe' , $_POST['subscribe'], array( 'message' => '*' ) )
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
        if( isset( $this->data['value'] ) ){
            return true;
        }
        elseif( $this->data['required'] ){
            $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_NOT_CHECK, array( $this->data['field'] ) );
            return false;
        }
        else{
            return true;
        }
    }


    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }
        else{
            return $cfg;
        }
    }


}