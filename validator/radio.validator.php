<?php

class RadioValidator extends ValidatorStrategy
{

    /**
     * Validation for radio field
     * @param string $name
     * @param mixed $value
     * @param string $attr ['errors']['empty']
     * @param bool $attr ['required']
     * @param string $attr ['field']
     *
     * @example new RadioValidator( 'gender', $_POST['gender'], array( 'message' => '*' ) )
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

        if( $this->data['required'] ){
            $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_NOT_MARK, array( $this->data['field'] ) );
            return false;
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