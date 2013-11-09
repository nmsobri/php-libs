<?php

/////////////////////////////////////////////////
// phpmailer - PHP email class
//
// Version 0.9, 04/16/2001
//
// Class for sending email using either
// sendmail, PHP mail(), or SMTP.  Methods are
// based upon the standard AspEmail(tm) classes.
//
// Author: Brent R. Matzelle
//
// License: LGPL, see LICENSE
/////////////////////////////////////////////////

namespace utility;

class Mail
{

    /////////////////////////////////////////////////
    // CLASS VARIABLES
    /////////////////////////////////////////////////
    // General Variables
    var $Priority = 3;
    var $CharSet = "iso-8859-1";
    var $ContentType = "text/plain";
    var $Encoding = "8bit";
    var $From = "root@localhost";
    var $FromName = "root";
    var $to = array( );
    var $cc = array( );
    var $bcc = array( );
    var $ReplyTo = array( );
    var $Subject = "";
    var $Body = "";
    var $WordWrap = false;
    var $mailer = "mail";
    var $sendmail = "/usr/sbin/sendmail";
    var $attachment = array( );
    var $boundary = false;
    var $MailerDebug = true;
    // SMTP-specific variables
    var $Host = "localhost";
    var $Port = 25;
    var $Helo = "localhost.localdomain";
    var $Timeout = 10; // Socket timeout in sec.
    var $SMTPDebug = false;



    /////////////////////////////////////////////////
    // VARIABLE METHODS
    /////////////////////////////////////////////////
    // Sets message to HTML
    function IsHTML( $bool )
    {
        if ( $bool == true )
            $this->ContentType = "text/html";
        else
            $this->ContentType = "text/plain";
    }



    // Sets mailer to use SMTP
    function isSmtp()
    {
        $this->mailer = "smtp";
    }



    // Sets mailer to use PHP mail() function
    function isMail()
    {
        $this->mailer = "mail";
    }



    // Sets mailer to directly use $sendmail program
    function isSendmail()
    {
        $this->mailer = "sendmail";
    }



    // Sets $sendmail to qmail MTA
    function IsQmail()
    {
        $this->sendmail = "/var/qmail/bin/qmail-inject";
    }



    /**
     * Set priority of sending
     * @param int $priority
     */
    function priority( $priority )
    {
        $this->Priority = $priority;
    }



    /**
     * Set body of mail
     * @param string $body
     */
    function body( $body )
    {
        $this->Body = $body;
    }



    /**
     * Set subject of mail
     * @param string $subject
     */
    function subject( $subject )
    {
        $this->Subject = $subject;
    }



    /**
     * Add 'from' address
     * @param string $address
     */
    function from( $address, $name )
    {
        $this->From = $address;
        $this->FromName = $name;
    }



    /////////////////////////////////////////////////
    // RECIPIENT METHODS
    /////////////////////////////////////////////////
    // Add a "to" address
    function to( $address, $name = "" )
    {
        $cur = count( $this->to );
        $this->to[ $cur ][ 0 ] = trim( $address );
        $this->to[ $cur ][ 1 ] = $name;
    }



    // Add a "cc" address
    function addCc( $address, $name = "" )
    {
        $cur = count( $this->cc );
        $this->cc[ $cur ][ 0 ] = trim( $address );
        $this->cc[ $cur ][ 1 ] = $name;
    }



    // Add a "bcc" address
    function addBcc( $address, $name = "" )
    {
        $cur = count( $this->bcc );
        $this->bcc[ $cur ][ 0 ] = trim( $address );
        $this->bcc[ $cur ][ 1 ] = $name;
    }



    // Add a "Reply-to" address
    function addReplyTo( $address, $name = "" )
    {
        $cur = count( $this->ReplyTo );
        $this->ReplyTo[ $cur ][ 0 ] = trim( $address );
        $this->ReplyTo[ $cur ][ 1 ] = $name;
    }



    /////////////////////////////////////////////////
    // MAIL SENDING METHODS
    /////////////////////////////////////////////////
    // Create message and assign to mailer
    function send()
    {
        if ( count( $this->to ) < 1 )
            $this->errorHandler( "You must provide at least one recipient email address" );

        $header = $this->createHeader();
        $body = $this->createBody();

        // Choose the mailer
        if ( $this->mailer == "sendmail" )
            $this->sendmailSend( $header, $body );
        elseif ( $this->mailer == "mail" )
            $this->mailSend( $header, $body );
        elseif ( $this->mailer == "smtp" )
            $this->smtpSend( $header, $body );
        else
            $this->errorHandler( sprintf( "%s mailer is not supported", $this->mailer ) );
    }



    // Send using the $sendmail program
    function sendmailSend( $header, $body )
    {
        $sendmail = sprintf( "%s -f %s -t", $this->sendmail, $this->From );

        if ( !$mail = popen( $sendmail, "w" ) )
            $this->errorHandler( sprintf( "Could not open %s", $this->sendmail ) );

        fputs( $mail, $header );
        fputs( $mail, $body );
        pclose( $mail );
    }



    // Send via the PHP mail() function
    function mailSend( $header, $body )
    {
        // Create mail recipient list
        $to = $this->to[ 0 ][ 0 ]; // no extra comma
        for ( $x = 1; $x < count( $this->to ); $x++ )
            $to .= sprintf( ",%s", $this->to[ $x ][ 0 ] );
        for ( $x = 0; $x < count( $this->cc ); $x++ )
            $to .= sprintf( ",%s", $this->cc[ $x ][ 0 ] );
        for ( $x = 0; $x < count( $this->bcc ); $x++ )
            $to .= sprintf( ",%s", $this->bcc[ $x ][ 0 ] );

        if ( !mail( $to, $this->Subject, $body, $header ) )
            $this->errorHandler( "Could not instantiate mail()" );
    }



    // Send message via SMTP using PhpSMTP
    // PhpSMTP written by Chris Ryan
    function smtpSend( $header, $body )
    {
        $smtp = new Smtp;
        $smtp->do_debug = $this->SMTPDebug;

        // Try to connect to all SMTP servers
        $hosts = explode( ";", $this->Host );
        $x = 0;
        $connection = false;
        while ( $x < count( $hosts ) )
        {
            if ( $smtp->connect( $hosts[ $x ], $this->Port, $this->Timeout ) )
            {
                $connection = true;
                break;
            }
            // printf("%s host could not connect<br>", $hosts[$x]); //debug only
            $x++;
        }
        if ( !$connection )
            $this->errorHandler( "SMTP Error: could not connect to SMTP host server(s)" );

        $smtp->hello( $this->Helo );
        $smtp->mail( sprintf( "<%s>", $this->From ) );

        for ( $x = 0; $x < count( $this->to ); $x++ )
            $smtp->recipient( sprintf( "<%s>", $this->to[ $x ][ 0 ] ) );
        for ( $x = 0; $x < count( $this->cc ); $x++ )
            $smtp->recipient( sprintf( "<%s>", $this->cc[ $x ][ 0 ] ) );
        for ( $x = 0; $x < count( $this->bcc ); $x++ )
            $smtp->recipient( sprintf( "<%s>", $this->bcc[ $x ][ 0 ] ) );

        $smtp->data( sprintf( "%s%s", $header, $body ) );
        $smtp->quit();
    }



    /////////////////////////////////////////////////
    // MESSAGE CREATION METHODS
    /////////////////////////////////////////////////
    // Creates recipient headers
    function addrAppend( $type, $addr )
    {
        $addr_str = "";
        $addr_str .= sprintf( "%s: %s <%s>", $type, $addr[ 0 ][ 1 ], $addr[ 0 ][ 0 ] );
        if ( count( $addr ) > 1 )
        {
            for ( $x = 1; $x < count( $addr ); $x++ )
            {
                $addr_str .= sprintf( ", %s <%s>", $addr[ $x ][ 1 ], $addr[ $x ][ 0 ] );
            }
            $addr_str .= "\n";
        }
        else
            $addr_str .= "\n";

        return($addr_str);
    }



    // Wraps message for use with mailers that don't
    // automatically perform wrapping
    // Written by philippe@cyberabuse.org
    function wordwrap( $message, $length )
    {
        $line = explode( "\n", $message );
        $message = "";
        for ( $i = 0; $i < count( $line ); $i++ )
        {
            $line_part = explode( " ", trim( $line[ $i ] ) );
            $buf = "";
            for ( $e = 0; $e < count( $line_part ); $e++ )
            {
                $buf_o = $buf;
                if ( $e == 0 )
                    $buf .= $line_part[ $e ];
                else
                    $buf .= " " . $line_part[ $e ];
                if ( strlen( $buf ) > $length and $buf_o != "" )
                {
                    $message .= $buf_o . "\n";
                    $buf = $line_part[ $e ];
                }
            }
            $message .= $buf . "\n";
        }
        return ($message);
    }



    // Assembles and returns the message header
    function createHeader()
    {
        $header = array( );
        $header[ ] = sprintf( "From: %s <%s>\n", $this->FromName, trim( $this->From ) );
        $header[ ] = $from;
        $header[ ] = $this->addrAppend( "To", $this->to );
        if ( count( $this->cc ) > 0 )
            $header[ ] = $this->addrAppend( "cc", $this->cc );
        if ( count( $this->bcc ) > 0 )
            $header[ ] = $this->addrAppend( "bcc", $this->bcc );
        if ( count( $this->ReplyTo ) > 0 )
            $header[ ] = $this->addrAppend( "Reply-to", $this->ReplyTo );
        $header[ ] = sprintf( "Subject: %s\n", trim( $this->Subject ) );
        $header[ ] = sprintf( "Return-Path: %s\n", trim( $this->From ) );
        $header[ ] = sprintf( "X-Priority: %d\n", $this->Priority );
        $header[ ] = sprintf( "X-Mailer: phpmailer [version .9]\n" );
        $header[ ] = sprintf( "Content-Transfer-Encoding: %s\n", $this->Encoding );
        // $header[] = sprintf("Content-Length: %d\n", (strlen($this->Body) * 7));
        if ( count( $this->attachment ) > 0 )
        {
            $header[ ] = sprintf( "Content-Type: Multipart/Mixed; charset = \"%s\";\n", $this->CharSet );
            $header[ ] = sprintf( " boundary=\"Boundary-=%s\"\n", $this->boundary );
        }
        else
        {
            $header[ ] = sprintf( "Content-Type: %s; charset = \"%s\";\n", $this->ContentType, $this->CharSet );
        }
        $header[ ] = "MIME-Version: 1.0\n\n";

        return(join( "", $header ));
    }



    // Assembles and returns the message body
    function createBody()
    {
        // wordwrap the message body if set
        if ( $this->WordWrap )
            $this->Body = $this->wordwrap( $this->Body, $this->WordWrap );

        if ( count( $this->attachment ) > 0 )
            $body = $this->attachAll();
        else
            $body = $this->Body;

        return($body);
    }



    /////////////////////////////////////////////////
    // ATTACHMENT METHODS
    /////////////////////////////////////////////////
    // Check if attachment is valid and add to list
    function addAttachment( $path )
    {
        if ( !is_file( $path ) )
            $this->errorHandler( sprintf( "Could not find %s file on filesystem", $path ) );

        // Separate file name from full path
        $separator = "/";
        $len = strlen( $path );

        // Set $separator to win32 style
        if ( !ereg( $separator, $path ) )
            $separator = "\\";

        // Get the filename from the path
        $pos = strrpos( $path, $separator ) + 1;
        $filename = substr( $path, $pos, $len );

        // Set message boundary
        $this->boundary = "_b" . md5( uniqid( time() ) );

        // Append to $attachment array
        $cur = count( $this->attachment );
        $this->attachment[ $cur ][ 0 ] = $path;
        $this->attachment[ $cur ][ 1 ] = $filename;
    }



    // Attach text and binary attachments to body
    function attachAll()
    {
        // Return text of body
        $mime = array( );
        $mime[ ] = sprintf( "--Boundary-=%s\n", $this->boundary );
        $mime[ ] = sprintf( "Content-Type: %s\n", $this->ContentType );
        $mime[ ] = "Content-Transfer-Encoding: 8bit\n";
        $mime[ ] = sprintf( "%s\n\n", $this->Body );

        // Add all attachments
        for ( $x = 0; $x < count( $this->attachment ); $x++ )
        {
            $path = $this->attachment[ $x ][ 0 ];
            $filename = $this->attachment[ $x ][ 1 ];
            $mime[ ] = sprintf( "--Boundary-=%s\n", $this->boundary );
            $mime[ ] = "Content-Type: application/octet-stream;\n";
            $mime[ ] = sprintf( "name=\"%s\"\n", $filename );
            $mime[ ] = "Content-Transfer-Encoding: base64\n";
            $mime[ ] = sprintf( "Content-Disposition: attachment; filename=\"%s\"\n\n", $filename );
            $mime[ ] = sprintf( "%s\n\n", $this->encodeFile( $path ) );
        }
        $mime[ ] = sprintf( "\n--Boundary-=%s--\n", $this->boundary );

        return(join( "", $mime ));
    }



    // Encode attachment in base64 format
    function encodeFile( $path )
    {
        if ( !$fd = fopen( $path, "r" ) )
            $this->errorHandler( "File Error: Could not open file %s", $path );
        $file = fread( $fd, filesize( $path ) );

        // chunk_split is found in PHP >= 3.0.6
        $encoded = chunk_split( base64_encode( $file ) );
        fclose( $fd );

        return($encoded);
    }



    /////////////////////////////////////////////////
    // MISCELLANEOUS METHODS
    /////////////////////////////////////////////////
    // Print out error and exit
    function errorHandler( $msg )
    {
        if ( $this->MailerDebug == true )
        {
            print("<h2>Mailer Error</h2>" );
            print("Description:<br>" );
            printf( "<font color=\"FF0000\">%s</font>", $msg );
            exit;
        }
    }



}




/*
 * File: smtp.php
 *
 * Description: Define an SMTP class that can be used to connect
 *              and communicate with any SMTP server. It implements
 *              all the SMTP functions defined in RFC821 except TURN.
 *
 * Creator: Chris Ryan <chris@greatbridge.com>
 * Created: 03/26/2001
 *
 * TODO:
 *     - Move all the duplicate code to a utility function
 *           Most of the functions have the first lines of
 *           code do the same processing. If this can be moved
 *           into a utility function then it would reduce the
 *           overall size of the code significantly.
 */

/*
 * STMP is rfc 821 compliant and implements all the rfc 821 SMTP
 * commands except TURN which will always return a not implemented
 * error. SMTP also provides some utility methods for sending mail
 * to an SMTP server.
 */

class Smtp
{

    var $SMTP_PORT = 25; # the default SMTP PORT
    var $CRLF = "\r\n";  # CRLF pair
    var $smtp_conn;      # the socket to the server
    var $error;          # error if any on the last call
    var $helo_rply;      # the reply the server sent to us for HELO
    var $do_debug;       # the level of debug to perform



    /*
     * SMTP()
     *
     * Initialize the class so that the data is in a known state.
     */

    function Smtp()
    {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;

        $this->do_debug = 0;
    }



    /*     * **********************************************************
     *                    CONNECTION FUNCTIONS                  *
     * ********************************************************* */

    /*
     * Connect($host, $port=0, $tval=30)
     *
     * Connect to the server specified on the port specified.
     * If the port is not specified use the default SMTP_PORT.
     * If tval is specified then a connection will try and be
     * established with the server for that number of seconds.
     * If tval is not specified the default is 30 seconds to
     * try on the connection.
     *
     * SMTP CODE SUCCESS: 220
     * SMTP CODE FAILURE: 421
     */

    function connect( $host, $port = 0, $tval = 30 )
    {
        # set the error val to null so there is no confusion
        $this->error = null;

        # make sure we are __not__ connected
        if ( $this->connected() )
        {
            # ok we are connected! what should we do?
            # for now we will just give an error saying we
            # are already connected
            $this->error =
                    array( "error" => "Already connected to a server" );
            return false;
        }

        if ( empty( $port ) )
        {
            $port = $this->SMTP_PORT;
        }

        #connect to the smtp server
        $this->smtp_conn = fsockopen( $host, # the host of the server
                $port, # the port to use
                $errno, # error number if any
                $errstr, # error message if any
                $tval );   # give up after ? secs
        # verify we connected properly
        if ( empty( $this->smtp_conn ) )
        {
            $this->error = array( "error"  => "Failed to connect to server",
                "errno"  => $errno,
                "errstr" => $errstr );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": $errstr ($errno)" . $this->CRLF;
            }
            return false;
        }

        # sometimes the SMTP server takes a little longer to respond
        # so we will give it a longer timeout for the first read
        socket_set_timeout( $this->smtp_conn, 1, 0 );

        # get any announcement stuff
        $announce = $this->getLines();

        # set the timeout  of any socket functions at 1/10 of a second
        socket_set_timeout( $this->smtp_conn, 0, 100000 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $announce;
        }

        return true;
    }



    /*
     * Connected()
     *
     * Returns true if connected to a server otherwise false
     */

    function connected()
    {
        if ( !empty( $this->smtp_conn ) )
        {
            $sock_status = socket_get_status( $this->smtp_conn );
            if ( $sock_status[ "eof" ] )
            {
                # hmm this is an odd situation... the socket is
                # valid but we aren't connected anymore
                if ( $this->do_debug >= 1 )
                {
                    echo "SMTP -> NOTICE:" . $this->CRLF .
                    "EOF caught while checking if connected";
                }
                $this->close();
                return false;
            }
            return true; # everything looks good
        }
        return false;
    }



    /*
     * Close()
     *
     * Closes the socket and cleans up the state of the class.
     * It is not considered good to use this function without
     * first trying to use QUIT.
     */

    function close()
    {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if ( !empty( $this->smtp_conn ) )
        {
            # close the connection and cleanup
            fclose( $this->smtp_conn );
            $this->smtp_conn = 0;
        }
    }



    /*     * ************************************************************
     *                        SMTP COMMANDS                       *
     * *********************************************************** */

    /*
     * Data($msg_data)
     *
     * Issues a data command and sends the msg_data to the server
     * finializing the mail transaction. $msg_data is the message
     * that is to be send with the headers. Each header needs to be
     * on a single line followed by a <CRLF> with the message headers
     * and the message body being seperated by and additional <CRLF>.
     *
     * Implements rfc 821: DATA <CRLF>
     *
     * SMTP CODE INTERMEDIATE: 354
     *     [data]
     *     <CRLF>.<CRLF>
     *     SMTP CODE SUCCESS: 250
     *     SMTP CODE FAILURE: 552,554,451,452
     * SMTP CODE FAILURE: 451,554
     * SMTP CODE ERROR  : 500,501,503,421
     */

    function data( $msg_data )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Data() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "DATA" . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 354 )
        {
            $this->error =
                    array( "error"     => "DATA command not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        # the server is ready to accept data!
        # according to rfc 821 we should not send more than 1000
        # including the CRLF
        # characters on a single line so we will break the data up
        # into lines by \r and/or \n then if needed we will break
        # each of those into smaller lines to fit within the limit.
        # in addition we will be looking for lines that start with
        # a period '.' and append and additional period '.' to that
        # line. NOTE: this does not count towards are limit.
        # normalize the line breaks so we know the explode works
        $msg_data = str_replace( "\r\n", "\n", $msg_data );
        $msg_data = str_replace( "\r", "\n", $msg_data );
        $lines = explode( "\n", $msg_data );

        # we need to find a good way to determine is headers are
        # in the msg_data or if it is a straight msg body
        # currently I'm assuming rfc 822 definitions of msg headers
        # and if the first field of the first line (':' sperated)
        # does not contain a space then it _should_ be a header
        # and we can process all lines before a blank "" line as
        # headers.
        $field = substr( $lines[ 0 ], 0, strpos( $lines[ 0 ], ":" ) );
        $in_headers = false;
        if ( !empty( $field ) && !strstr( $field, " " ) )
        {
            $in_headers = true;
        }

        $max_line_length = 998; # used below; set here for ease in change

        while ( list(, $line) = @each( $lines ) )
        {
            $lines_out = null;
            if ( $line == "" && $in_headers )
            {
                $in_headers = false;
            }
            # ok we need to break this line up into several
            # smaller lines
            while ( strlen( $line ) > $max_line_length )
            {
                $pos = strrpos( substr( $line, 0, $max_line_length ), " " );
                $lines_out[ ] = substr( $line, 0, $pos );
                $line = substr( $line, $pos + 1 );
                # if we are processing headers we need to
                # add a LWSP-char to the front of the new line
                # rfc 822 on long msg headers
                if ( $in_headers )
                {
                    $line = "\t" . $line;
                }
            }
            $lines_out[ ] = $line;

            # now send the lines to the server
            while ( list(, $line_out) = @each( $lines_out ) )
            {
                if ( $line_out[ 0 ] == "." )
                {
                    $line_out = "." . $line_out;
                }
                fputs( $this->smtp_conn, $line_out . $this->CRLF );
            }
        }

        # ok all the message data has been sent so lets get this
        # over with aleady
        fputs( $this->smtp_conn, $this->CRLF . "." . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "DATA not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * Expand($name)
     *
     * Expand takes the name and asks the server to list all the
     * people who are members of the _list_. Expand will return
     * back and array of the result or false if an error occurs.
     * Each value in the array returned has the format of:
     *     [ <full-name> <sp> ] <path>
     * The definition of <path> is defined in rfc 821
     *
     * Implements rfc 821: EXPN <SP> <string> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 550
     * SMTP CODE ERROR  : 500,501,502,504,421
     */

    function expand( $name )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Expand() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "EXPN " . $name . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "EXPN not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        # parse the reply and place in our array to return to user
        $entries = explode( $this->CRLF, $rply );
        while ( list(, $l) = @each( $entries ) )
        {
            $list[ ] = substr( $l, 4 );
        }

        return $rval;
    }



    /*
     * Hello($host="")
     *
     * Sends the HELO command to the smtp server.
     * This makes sure that we and the server are in
     * the same known state.
     *
     * Implements from rfc 821: HELO <SP> <domain> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 501, 504, 421
     */

    function hello( $host = "" )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Hello() without being connected" );
            return false;
        }

        # if a hostname for the HELO wasn't specified determine
        # a suitable one to send
        if ( empty( $host ) )
        {
            # we need to determine some sort of appopiate default
            # to send to the server
            $host = "localhost";
        }

        fputs( $this->smtp_conn, "HELO " . $host . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "HELO not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        $this->helo_rply = $rply;

        return true;
    }



    /*
     * Help($keyword="")
     *
     * Gets help information on the keyword specified. If the keyword
     * is not specified then returns generic help, ussually contianing
     * A list of keywords that help is available on. This function
     * returns the results back to the user. It is up to the user to
     * handle the returned data. If an error occurs then false is
     * returned with $this->error set appropiately.
     *
     * Implements rfc 821: HELP [ <SP> <string> ] <CRLF>
     *
     * SMTP CODE SUCCESS: 211,214
     * SMTP CODE ERROR  : 500,501,502,504,421
     *
      function Help($keyword="") {
      $this->error = null; # to avoid confusion

      if(!$this->connected()) {
      $this->error = array(
      "error" => "Called Help() without being connected");
      return false;
      }

      $extra = "";
      if(!empty($keyword)) {
      $extra = " " . $keyword;
      }

      fputs($this->smtp_conn,"HELP" . $extra . $this->CRLF);

      $rply = $this->get_lines();
      $code = substr($rply,0,3);

      if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
      }

      if($code != 211 && $code != 214) {
      $this->error =
      array("error" => "HELP not accepted from server",
      "smtp_code" => $code,
      "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
      echo "SMTP -> ERROR: " . $this->error["error"] .
      ": " . $rply . $this->CRLF;
      }
      return false;
      }

      return $rply;
      }

      /*
     * Mail($from)
     *
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command.
     *
     * Implements rfc 821: MAIL <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,421
     */

    function mail( $from )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Mail() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "MAIL FROM:" . $from . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "MAIL not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * Noop()
     *
     * Sends the command NOOP to the SMTP server.
     *
     * Implements from rfc 821: NOOP <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 421
     */

    function noop()
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Noop() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "NOOP" . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "NOOP not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * Quit($close_on_error=true)
     *
     * Sends the quit command to the server and then closes the socket
     * if there is no error or the $close_on_error argument is true.
     *
     * Implements from rfc 821: QUIT <CRLF>
     *
     * SMTP CODE SUCCESS: 221
     * SMTP CODE ERROR  : 500
     */

    function quit( $close_on_error = true )
    {
        $this->error = null; # so there is no confusion

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Quit() without being connected" );
            return false;
        }

        # send the quit command to the server
        fputs( $this->smtp_conn, "quit" . $this->CRLF );

        # get any good-bye messages
        $byemsg = $this->getLines();

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $byemsg;
        }

        $rval = true;
        $e = null;

        $code = substr( $byemsg, 0, 3 );
        if ( $code != 221 )
        {
            # use e as a tmp var cause Close will overwrite $this->error
            $e = array( "error"     => "SMTP server rejected quit command",
                "smtp_code" => $code,
                "smtp_rply" => substr( $byemsg, 4 ) );
            $rval = false;
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $e[ "error" ] . ": " .
                $byemsg . $this->CRLF;
            }
        }

        if ( empty( $e ) || $close_on_error )
        {
            $this->close();
        }

        return $rval;
    }



    /*
     * Recipient($to)
     *
     * Sends the command RCPT to the SMTP server with the TO: argument of $to.
     * Returns true if the recipient was accepted false if it was rejected.
     *
     * Implements from rfc 821: RCPT <SP> TO:<forward-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,552,553,450,451,452
     * SMTP CODE ERROR  : 500,501,503,421
     */

    function recipient( $to )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Recipient() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "RCPT TO:" . $to . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 && $code != 251 )
        {
            $this->error =
                    array( "error"     => "RCPT not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * Reset()
     *
     * Sends the RSET command to abort and transaction that is
     * currently in progress. Returns true if successful false
     * otherwise.
     *
     * Implements rfc 821: RSET <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500,501,504,421
     */

    function reset()
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Reset() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "RSET" . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "RSET failed",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }



    /*
     * Send($from)
     *
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in.
     *
     * Implements rfc 821: SEND <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     */

    function send( $from )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Send() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "SEND FROM:" . $from . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "SEND not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * SendAndMail($from)
     *
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in and send them an email.
     *
     * Implements rfc 821: SAML <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     */

    function sendAndMail( $from )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called SendAndMail() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "SAML FROM:" . $from . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "SAML not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * SendOrMail($from)
     *
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in or mail it to them if they are not.
     *
     * Implements rfc 821: SOML <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     */

    function sendOrMail( $from )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called SendOrMail() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "SOML FROM:" . $from . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 )
        {
            $this->error =
                    array( "error"     => "SOML not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }



    /*
     * Turn()
     *
     * This is an optional command for SMTP that this class does not
     * support. This method is here to make the RFC821 Definition
     * complete for this class and __may__ be implimented in the future
     *
     * Implements from rfc 821: TURN <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 502
     * SMTP CODE ERROR  : 500, 503
     */

    function turn()
    {
        $this->error = array( "error" => "This method, TURN, of the SMTP " .
            "is not implemented" );
        if ( $this->do_debug >= 1 )
        {
            echo "SMTP -> NOTICE: " . $this->error[ "error" ] . $this->CRLF;
        }
        return false;
    }



    /*
     * Verify($name)
     *
     * Verifies that the name is recognized by the server.
     * Returns false if the name could not be verified otherwise
     * the response from the server is returned.
     *
     * Implements rfc 821: VRFY <SP> <string> <CRLF>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,553
     * SMTP CODE ERROR  : 500,501,502,421
     */

    function verify( $name )
    {
        $this->error = null; # so no confusion is caused

        if ( !$this->connected() )
        {
            $this->error = array(
                "error" => "Called Verify() without being connected" );
            return false;
        }

        fputs( $this->smtp_conn, "VRFY " . $name . $this->CRLF );

        $rply = $this->getLines();
        $code = substr( $rply, 0, 3 );

        if ( $this->do_debug >= 2 )
        {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if ( $code != 250 && $code != 251 )
        {
            $this->error =
                    array( "error"     => "VRFY failed on name '$name'",
                        "smtp_code" => $code,
                        "smtp_msg"  => substr( $rply, 4 ) );
            if ( $this->do_debug >= 1 )
            {
                echo "SMTP -> ERROR: " . $this->error[ "error" ] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return $rply;
    }



    /*     * ****************************************************************
     *                       INTERNAL FUNCTIONS                       *
     * **************************************************************** */

    /*
     * get_lines()
     *
     * __internal_use_only__: read in as many lines as possible
     * either before eof or socket timeout occurs on the operation.
     * With SMTP we can tell if we have more lines to read if the
     * 4th character is '-' symbol. If it is a space then we don't
     * need to read anything else.
     */

    function getLines()
    {
        $data = "";
        while ( $str = fgets( $this->smtp_conn, 515 ) )
        {
            if ( $this->do_debug >= 4 )
            {
                echo "SMTP -> get_lines(): \$data was \"$data\"" .
                $this->CRLF;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                $this->CRLF;
            }
            $data .= $str;
            if ( $this->do_debug >= 4 )
            {
                echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->CRLF;
            }
            # if the 4th character is a space then we are done reading
            # so just break the loop
            if ( substr( $str, 3, 1 ) == " " )
            {
                break;
            }
        }
        return $data;
    }



}




?>
