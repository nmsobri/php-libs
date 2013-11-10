<?php

/**
 * Exception Class
 * Note:When Using This Class..U dont Need to Use Try Catch Block
 * @author slier
 */

namespace utility;

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
        $styles = 'width:40%;height:25%;background-color:#eee;
                    border:1px dashed #999;position:absolute;
                    top:-100;bottom:0;left:0;right:0;margin:auto;
                    padding:20px;z-index:99';

        $template = '<div style="%s">%s%s%s%s</div>';

        printf( $template, $styles, '<h2>Exception Has Occured</h2>',
            sprintf( '<p>Line:%s</p>', $e->getLine() ),
            sprintf( '<p>File:%s</p>', $e->getFile() ),
            sprintf( '<p>Message:%s</p>', $e->getMessage() )
        );
    }


}

?>
