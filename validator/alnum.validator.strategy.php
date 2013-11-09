<?php

namespace utility\validator;

abstract class AlnumValidatorStrategy extends ValidatorStrategy
{

    /**
     * @param $length
     */
    protected function configValidatorLengthAttr( $length )
    {
        if( is_null( $length ) ){
            $this->data['min_length'] = 0;
            $this->data['max_length'] = 0;
        }
        else{
            if( is_array( $length ) ){
                if( $this->is_assoc( $length ) ){
                    if( count( $length ) > 1 ){
                        $this->data['min_length'] = $length['min'];
                        $this->data['max_length'] = $length['max'];
                    }
                    else{
                        $this->data['min_length'] = isset( $length['min'] ) ? $length['min'] : 1;
                        $this->data['max_length'] = isset( $length['max'] ) ? $length['max'] : 0;
                    }
                }
                else{
                    if( count( $length ) > 1 ){
                        $this->data['min_length'] = $length[0];
                        $this->data['max_length'] = $length[1];
                    }
                    else{
                        $this->data['min_length'] = $length['min'];
                        $this->data['max_length'] = 0;
                    }
                }
            }
            else{
                $this->data['min_length'] = $length;
                $this->data['max_length'] = 0;
            }
        }

    }


    /**
     * @param $array
     * @return bool
     */
    protected function is_assoc( $array )
    {
        return (bool)count( array_filter( array_keys( $array ), 'is_string' ) );
    }
}