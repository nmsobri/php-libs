<?php

namespace utility;

/**
 * Class ExceptionHandler
 * @package utility
 * Note:When Using This Class..U dont Need to Use Try Catch Block
 */
class ExceptionHandler
{

    /**
     * Constructor method
     * @access public
     */
    public function __construct()
    {
        set_exception_handler( array( $this, 'exceptionHandler' ) );
    }


    /**
     * Exception handler
     *
     * @param \Exception $e
     * @return void
     */
    public function exceptionHandler( \Exception $e )
    {
        $styles = 'width:40%;min-height:25%;background-color:#eee;
                    border:1px dashed #999;position:absolute;
                    top:20%;left:0;right:0;margin:auto;padding:20px';

        $template = '<div style="%s">%s%s%s%s</div>';

        printf( $template, $styles, '<h2>Exception Has Occured</h2>',
            sprintf( '<p>File: %s</p>', $e->getFile() ),
            sprintf( '<p>Line: %s</p>', $e->getLine() ),
            sprintf( '<p>Message: %s</p>', $e->getMessage() )
        );
    }


}

?>
