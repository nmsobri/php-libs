<?php

/**
*  This class encapsulates the PHP mail() function.
*  It implements CC, Bcc, Priority headers
*
*  @author slier
*  @example
 *
 * [code]
*  include "libmail.php";
*  $m = new Mail;
*  $m->From( "leo@isp.com" );
*  $m->To( "destination@somewhere.fr" );
*  $m->Subject( "the subject of the mail" );
*
*  $message= "Hello world!\nthis is a test of the Mail class\nplease ignore\nThanks.";
*  $m->Body( $message);
*  $m->Cc( "someone@somewhere.fr");
*  $m->Bcc( "someoneelse@somewhere.fr");
*  $m->Priority(4) ;
*  $m->Attach( "/home/leo/toto.gif", "image/gif" ) ;
*  $m->Send();
*  echo "the mail below has been sent:", $m->Get(), "";
 * [/code]
*/
class Mail
{
    /**
     * list of To addresses
     * @var   array
     * @access protected
     */
    protected $sendto = array( );


    /**
     *
     * @var   array
     * @access protected
     */
    protected $acc = array( );


    /**
     * @var   array
     * @access protected
     */
    protected $abcc = array( );


    /**
     * paths of attached files
     * @var array
     * @access protected
     */
    protected $aattach = array( );


    /**
     * list of message headers
     * @var array
     * @access protected
     */
    protected $xheaders = array( );


    /**
     * message priorities referential
     * @var array
     * @access protected
     */
    protected $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );


    /**
     * character set of message
     * @var string
     * @access protected
     */
    protected $charset = "us-ascii";


    /**
     *
     * @var string
     * @access protected
     */
    protected $ctencoding = "7bit";


    /**
     *
     * @var int
     * @access protected
     */
    protected $receipt = 0;




    /**
     *
     * Constructor method
     * @access public
     */
    public function __construct()
    {
        $this->autoCheck( false );
        $this->boundary = "--" . md5( uniqid( "myboundary" ) );
    }




    /**
     *
     * Activate or desactivate the email addresses validator
     * Ex: autoCheck( true ) turn the validator on by default autoCheck feature is on
     * @param boolean   $bool set to true to turn on the auto validation
     * @access public
     */
    public function autoCheck( $bool )
    {
        if( $bool )
            $this->checkAddress = true;
        else
            $this->checkAddress = false;
    }




    /**
     *
     * Define the subject line of the email
     * @param string $subject any monoline string
     * @access public
     */
    public function subject( $subject )
    {
        $this->xheaders['Subject'] = strtr( $subject, "\r\n", "  " );
    }




    /**
     *
     * Set the sender of the mail
     * @param string $from should be an email address
     * @access public
     */
    public function from( $from )
    {

        if( !is_string( $from ) )
        {
            echo "Class Mail: error, From is not a string";
            exit;
        }
        $this->xheaders['From'] = $from;
    }




    /**
     *
     * Set the Reply-to header
     * @param string $address should be an email address
     * @access public
     */
    public function replyTo( $address )
    {

        if( !is_string( $addres ) )
            return false;
        $this->xheaders["Reply-To"] = $address;
    }




    /**
     *
     * Add a receipt to the mail
     * Ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined)  when the receiver opens the message.
     * @warning this functionality is *not* a standard, thus only some mail clients are compliants.
     * @access public
     */
    public function receipt()
    {
        $this->receipt = 1;
    }




    /**
     *
     * Set the mail recipient
     * @param string $to email address, accept both a single address or an array of addresses
     * @access public
     */
    public function to( $to )
    {
        // TODO : test validit� sur to
        if( is_array( $to ) )
            $this->sendto = $to;
        else
            $this->sendto[] = $to;

        if( $this->checkAddress == true )
            $this->checkAdresses( $this->sendto );
    }




    /**
     *
     * set the CC headers ( carbon copy )
     * @param mixed $cc : email address(es), accept both array and string
     * @access public
     */
    public function cc( $cc )
    {
        if( is_array( $cc ) )
            $this->acc = $cc;
        else
            $this->acc[] = $cc;

        if( $this->checkAddress == true )
            $this->checkAdresses( $this->acc );
    }




    /**
     *
     * Set the Bcc headers ( blank carbon copy ).
     * @param mixed $bcc : email address(es), accept both array and string
     * @access public
     */
    public function bcc( $bcc )
    {
        if( is_array( $bcc ) )
        {
            $this->abcc = $bcc;
        }
        else
        {
            $this->abcc[] = $bcc;
        }

        if( $this->checkAddress == true )
            $this->checkAdresses( $this->abcc );
    }




    /**
     *
     * Body( text [, charset] )
     * Set the body (message) of the mail
     * Define the charset if the message contains extended characters (accents)
     * Default to us-ascii
     * @example $mail->Body( "m�l en fran�ais avec des accents", "iso-8859-1" );
     * @param mixed $body
     * @param mixed $charset
     * @access public
     */
    public function body( $body, $charset = "" )
    {
        $this->body = $body;

        if( $charset != "" )
        {
            $this->charset = strtolower( $charset );
            if( $this->charset != "us-ascii" )
                $this->ctencoding = "8bit";
        }
    }




    /**
     *
     * Set the Organization header
     * @param mixed $org
     * @access public
     */
    public function organization( $org )
    {
        if( trim( $org != "" ) )
            $this->xheaders['Organization'] = $org;
    }




    /**
     *
     * Set the mail priority
     * @param mixed $priority : integer taken between 1 (highest) and 5 ( lowest )
     * @example $mail->Priority(1) ; => Highest
     * @access public
     */
    public function priority( $priority )
    {
        if( !intval( $priority ) )
            return false;

        if( !isset( $this->priorities[$priority - 1] ) )
            return false;
        $this->xheaders["X-Priority"] = $this->priorities[$priority - 1];
        return true;
    }




    /**
     *
     * Attach a file to the mail
     * @param string $filename : path of the file to attach
     * @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
     * @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
     * @access public
     */
    public function attach( $filename, $filetype = "", $disposition = "inline" )
    {
        // TODO : si filetype="", alors chercher dans un tablo de MT connus / extension du fichier
        if( $filetype == "" )
            $filetype = "application/x-unknown-content-type";
        $this->aattach[] = $filename;
        $this->actype[] = $filetype;
        $this->adispo[] = $disposition;
    }




    /**
     *
     * Send the email
     * @access public
     */
    public function send()
    {
        $this->buildMail();
        $this->strTo = implode( ", ", $this->sendto );
        $res = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );
    }




    /**
     *
     * Return the whole e-mail , headers ,message
     * Can be used for displaying the message in plain text or logging it
     * @access public
     * @return Mail
     */
    public function get()
    {
        $this->buildMail();
        $mail = "To: " . $this->strTo . "\n";
        $mail .= $this->headers . "\n";
        $mail .= $this->fullBody;
        return $mail;
    }




    /**
     *
     * Check validity of email addresses
     * @param   array $aad -
     * @return if unvalid, output an error message and exit, this may -should- be customized
     * @access private
     */
    private function checkAdresses( $aad )
    {
        for( $i = 0; $i < count( $aad ); $i++ )
        {
            if( !$this->validEmail( $aad[$i] ) )
            {
                echo "Class Mail, method Mail : invalid address $aad[$i]";
                exit;
            }
        }
    }




    /**
     *
     * Check an email address validity
     * @param string $address : email address to check
     * @return true if email adress is ok
     * @access private
     */
    private function validEmail( $address )
    {
        if( preg_match( "/^[0-9a-z]+(([\.\-_])[0-9a-z]+)*@[0-9a-z]+(([\.\-])[0-9a-z-]+)*\.[a-z]{2,4}$/i", $address ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }




    /**
     *
     * Build the email message
     * @access private
     */
    private function buildMail()
    {
        // build the headers
        $this->headers = "";
        //$this->xheaders['To'] = implode( ", ", $this->sendto );

        if( count( $this->acc ) > 0 )
            $this->xheaders['CC'] = implode( ", ", $this->acc );

        if( count( $this->abcc ) > 0 )
            $this->xheaders['BCC'] = implode( ", ", $this->abcc );

        if( $this->receipt )
        {
            if( isset( $this->xheaders["Reply-To"] ) )
                $this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
            else
                $this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
        }

        if( $this->charset != "" )
        {
            $this->xheaders["Mime-Version"] = "1.0";
            $this->xheaders["Content-Type"] = "text/plain; charset=$this->charset";
            $this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
        }

        $this->xheaders["X-Mailer"] = "Php Mailer";

        // include attached files
        if( count( $this->aattach ) > 0 )
        {
            $this->_build_attachement();
        }
        else
        {
            $this->fullBody = $this->body;
        }

        reset( $this->xheaders );
        while( list( $hdr, $value ) = each( $this->xheaders ) )
        {
            if( $hdr != "Subject" )
                $this->headers .= "$hdr: $value\n";
        }
    }




    /**
     *
     * Check and encode attach file(s) . internal use only
     * @access private
     */
    private function _build_attachement()
    {

        $this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";
        $this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
        $this->fullBody .= "Content-Type: text/plain; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body . "\n";
        $sep = chr( 13 ) . chr( 10 );
        $ata = array( );
        $k = 0;

        //for each attached file, do...
        for( $i = 0; $i < count( $this->aattach ); $i++ )
        {
            $filename = $this->aattach[$i];
            $basename = basename( $filename );
            $ctype = $this->actype[$i];   // content-type
            $disposition = $this->adispo[$i];

            if( !file_exists( $filename ) )
                echo "Class Mail, method attach : file $filename can't be found"; exit;

            $subhdr = "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
            $ata[$k++] = $subhdr;
            //non encoded line length
            $linesz = filesize( $filename ) + 1;
            $fp = fopen( $filename, 'r' );
            $ata[$k++] = chunk_split( base64_encode( fread( $fp, $linesz ) ) );
            fclose( $fp );
        }
        $this->fullBody .= implode( $sep, $ata );
    }
}
?>
