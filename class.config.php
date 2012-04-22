<?php


/**
 * Class to store configuration entities
 * Use Xml File As A Configuration File
 * Root element <configs></configs>
 * @author slier
 */
class Config
{
    /**
     *
     * @var string
     * @access private
     */


    private $fileName;
    /**
     *
     * @var array
     * @access private
     */


    private $data = array( );
    /**
     *
     * @var string
     * @access public
     */


    private $rootName;

    /**
     * Constructor method
     * @access public
     * @param string $fileName
     * @param string $rootName
     */


    public function __construct( $fileName, $rootName )
    {
        if( !file_exists( $fileName ) )
        {
            throw new Exception( "File {$fileName} not found" );
        }
        $this->fileName = $fileName;
        $this->rootName = $rootName;
        $this->read();
    }



    /**
     * Acces value of entities
     * @access public
     * @param string $name
     * @return mixed
     */
    public function get( $name )
    {
        return ( isset( $this->data[$name] ) ) ? $this->data[$name] : null;
    }



    /**
     * Set entities value
     * @access public
     * @param string $name
     * @param mixed $value
     */
    public function set( $name, $value )
    {
        if( isset( $this->data[$name] ) )
        {
            $this->data[$name] = $value;
        }
    }



    /**
     * Read the configuration file
     * @access public
     * @return array
     */
    public function read()
    {
        if( count( $this->data ) < 1 )
        {
            $xml = simplexml_load_file( $this->fileName );
            $rootName = $xml->getName();

            if( $rootName != $this->rootName )
            {
                throw new Exception( "File {$this->fileName} should have root <{$this->rootName}>" );
            }

            foreach( $xml->children() as $child )
            {
                $this->data[$child->getName()] = ( string ) $child;
            }
            return $this->data;
        }
        else
        {
            return $this->data;
        }
    }



    /**
     * Write to configuration file
     * @access public
     */
    public function write()
    {
        if( !is_writable( $this->fileName ) )
        {
            throw new Exception( "File {$this->fileName} is not writable" );
        }

        $xml = new SimpleXMLElement( '<configs></configs>' );
        foreach( $this->data as $key => $val )
        {
            $xml->addChild( $key, $val );
        }

        $dom = new DomDocument();
        $dom->loadXML( $xml->asXML() );
        $dom->formatOutput = true;
        $dom->save( $this->fileName );
    }



}

?>
