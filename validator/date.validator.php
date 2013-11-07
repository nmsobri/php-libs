<?php

class DateValidator extends ValidatorStrategy
{

    /**
     * @param string $name
     * @param string $value
     * @param string $attr ['version']
     * @param bool $attr ['required']
     * @param string $attr ['field']
     * @param string $attr ['errors']['empty']
     * @param string $attr ['errors']['date']
     * @example new DateValidator( 'dob', $_POST['dob'], array( 'message' => '*' ) )
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
        $pattern = $this->data['version'] == 'eu' ? "#^(0[1-9]|[1-2][0-9]|3[0-1])[-](0[1-9]|1[0-2])[-](19|20)[0-9]{2}$#" : "#^(19|20)[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$#";
        $format = $this->data['version'] == 'eu' ? 'DD-MM-YY' : 'YY-MM-DD';

        if( empty( $this->data['value'] ) ){
            if( $this->data['required'] ){
                $this->messages = ( $this->data['errors']['empty'] ) ? $this->data['errors']['empty'] : $this->errorText( ValidatorStrategy::E_EMPTY, array( $this->data['field'] ) );
                return false;
            }
            return true;
        }

        if( !preg_match( $pattern, $this->data['value'] ) ){
            $this->messages = ( $this->data['errors']['date'] ) ? $this->data['errors']['date'] : $this->errorText( ValidatorStrategy::E_INVALID_DATE, array( $this->data['field'], $format ) );
            return false;
        }

        return true;
    }


    /**
     * @param $name
     * @param $value
     * @param array $attr
     */
    protected function configValidator( $name, $value, array $attr )
    {
        parent::configValidator( $name, $value, $attr );
        $this->data['version'] = ( array_key_exists( 'version', $attr ) ) ? $attr['version'] : 'us';
    }


    /**
     * @param array $attr
     * @return array
     */
    protected function configErrors( array $attr )
    {
        $cfg = array(
            'empty' => null, 'date' => null
        );

        if( isset( $attr['errors'] ) and is_array( $attr['errors'] ) ){
            return array_merge( $cfg, $attr['errors'] );
        }

        return $cfg;
    }


}
