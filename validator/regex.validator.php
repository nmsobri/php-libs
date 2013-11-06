<?php

class RegexValidator extends ValidatorStrategy
{


    /**
     * @param string $name
     * @param mixed $value
     * @param string $regex
     * @param mixed $attr
     * @param bool $attr ['required']
     * @param mixed $attr ['errors']['empty']
     * @param mixed $attr ['field']
     *
     * @example new RegexValidator( 'gender', $_POST['gender'], '/[a-z]+$/', array( 'message' => '*' ) )
     */
    public function __construct( $name, $value, $regex, array $attr = null )
    {
        $attr = !is_null( $attr ) ? $attr : array();
        $this->configValidator( $name, $value, $regex, $attr );
    }


    /**
     * Perform validation
     * @return bool
     */
    public function isValid()
    {

        if( empty( $this->data['value'] ) ){
            if( $this->data['required'] ){
                $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_EMPTY, array( $this->data['field'] ) );
                return false;
            }
            else{
                return true; //simply return true cause we dont care if this field is empty or not
            }
        }
        else{
            if( preg_match( $this->data['regex'], $this->data['value'] ) ){
                return true;
            }
            else{
                $this->messages = ( $this->data['errors']['regex'] ) ? $this->data['errors']['regex'] : $this->errorText( ValidatorStrategy::E_INVALID_CHARACTER, array( $this->data['field'] ) );
                return false;
            }
        }

    }


    /**
     * @param $name
     * @param $value
     * @param array $regex
     * @param $attr
     */
    protected function configValidator( $name, $value, $regex, $attr )
    {
        parent::configValidator( $name, $value, $attr );
        $this->data['regex'] = $regex;
    }


    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'regex' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }
        else{
            return $cfg;
        }
    }


}
