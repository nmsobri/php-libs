<?php

namespace utility;

class Validator
{

    /**
     * Check status of validation
     * @var bool
     */
    protected $isError = false;


    /**
     * Hold and array of ValidatorStrategy
     * @var array
     */
    protected $validators = array();


    /**
     * Constructor Method
     */
    public function __construct()
    {

    }


    /**
     * Add validator strategy
     *
     * @param string $name
     * @param validator\ValidatorStrategy $strategy
     * @return void
     */
    public function addValidator( $name, validator\ValidatorStrategy $strategy )
    {
        $this->validators[$name] = $strategy;
    }


    /**
     * Perform the validation
     *
     * @return bool
     */
    public function isValid()
    {
        $this->isError = false;

        if( count( $this->validators ) > 0 ){
            foreach( $this->validators as $validator ){
                if( !$validator->isValid() ) $this->isError = true;
            }
        }

        return ( $this->isError ) ? false : true;
    }


    /**
     * Custom method to mark any form field as invalidate (failed validation)
     *
     * @param string $name
     * @param string $message
     * @return void
     */
    public function invalidateField( $name, $message )
    {
        if( in_array( $name, array_keys( $this->validators ) ) ){
            $this->validators[$name]->setMessage( $message );
            $this->isError = true;
        }
    }


    /**
     * Custom method to mark overall validation process as invalid
     * Typical use is login system, all input passed validation, but somehow no valid user is found
     * So use this method to mark overall process as invalid
     *
     * @param boolean $message
     * @return void
     */
    public function invalidateValidation( $message )
    {
        $this->validators[] = new validator\ErrorValidator( $message );
        $this->isError = true;
    }


    /**
     * Method to check whether validation is successful or fail
     *
     * @return bool
     */
    public function isError()
    {
        return $this->isError;
    }


    /**
     * Method to get the error for particular/individual validator strategy
     *
     * @param string $name
     * @example $obj->getError('username')
     * @return string
     */
    public function getError( $name )
    {
        return in_array( $name, array_keys( $this->validators ) ) ? $this->validators[$name]->getMessage() : null;
    }


    /**
     * Method to populate error field
     *
     * @return array of message
     */
    public function getAllError()
    {
        $message = null;

        foreach( $this->validators as $key => $validator ){
            if( !is_null( $validator->getMessage() ) ){
                $message[$key] = $validator->getMessage();
            }
        }
        return $message;
    }


    /**
     * Method to create block of error message (usually used at the top of the form)
     *
     * @param string $template
     * @return string of message
     */
    public function showError( $template = '%s<br>' )
    {
        $messages = null;
        $errors = $this->getAllError();

        if( count( $errors ) > 0 ){
            foreach( $errors as $value ){
                $messages .= sprintf( $template, $value );
            }
        }
        return $messages;
    }


}


?>
