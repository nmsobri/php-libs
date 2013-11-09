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
     * @param Exception $e
     * @return void
     */
    public function exceptionHandler( Exception $e )
    {
        echo '<div style="background-color:#eee;border:1px dashed #999999;padding:10px;margin-top:60px;margin-left:auto;margin-right:auto;width:1000px">' . '<br>';
        echo '<h3>Exception Has Occured</h3>';
        echo "<p>Code: {$e->getCode()} </p>";
        echo "<p>Line: {$e->getLine()} </p>";
        echo "<p>File: {$e->getFile()} </p>";
        echo "<p>Message: {$e->getMessage()}</p>";
        echo '<p>Stack Trace:</p>';
        echo "<pre>";
        print_r( $e->getTraceAsString() );
        echo "</pre>";
        echo '</div>';
    }




}

?>
