<?php

/**
 *
 * Template Class
 * @author slier
 */
class Template
{

    const DS = DIRECTORY_SEPARATOR;




    /**
     * The variable property contains the variables
     * that can be used inside of the templates.
     * @access private
     * @var array
     */
    private $variables = array( );



    /**
     * The directory where the templates are stored
     * @access private
     * @var string
     */
    private $templateDir = null;



    /**
     * Turns caching on or off
     * @access private
     * @var bool
     * CURRENTLY CHACHING ONLY IMPLEMENTED IN display() METHOD.
     * CURRENTLY THERE IS NO UNIQUE ID FOR CACHE TEMPLATE,SO IF U USE SAME LAYOUT FOR ALL PAGE, PROBABLY ALL PAGE GONNA GET SAME CONTENT DUE TO CACHE OUTPUT
     * TODO..MAKE TEMPLATE CACHING UNIQUE
     */
    private $caching = false;



    /**
     * The directory where the cache files will be saved.
     * @access private
     * @var string
     */
    private $cacheDir = 'cache';



    /**
     * Lifetime of a cache file in seconds.
     * @access private
     * @var int
     */
    private $cacheLifetime = 600;



    /**
     * Boolean wether to compress final output
     * @access private
     * @var mixed
     */
    private $useGzip = false;




    /**
     * Constructor Function
     * @access public
     * @param string $templateDir
     *
     */
    public function __construct( $templateDir )
    {

        $this->setTemplateDir( $templateDir );
    }




    /**
     *
     * Adds a variable that can be used by the templates.
     * Adds a new array index to the variable property.
     * This new array index will be treated as a variable by the templates.
     * @access public
     * @param string $name The variable name to use in the template
     * @param string $value The content you assign to $name
     * @access public
     * @see getVars, $variables
     *
     */
    public function set( $name, $value )
    {
        $this->variables[ $name ] = $value;
    }




    /**
     *
     * Get the value of variable with given name
     * @access public
     * @param mixed $name
     * @return string || null
     *
     */
    public function get( $name )
    {
        return isset( $this->variables[ $name ] ) ? $this->variables[ $name ] : null;
    }




    /**
     *
     * Returns names of all the added variables
     * @access public
     * @return array
     * @see addVar, $variables
     */
    public function getVars()
    {
        $variables = array_keys( $this->variables );
        return (!empty( $variables )) ? $variables : false;
    }




    /**
     *
     * Outputs the final template output
     * Fetches the final template output, and echoes it to the browser.
     * @access public
     * @param string $template_file Filename (with path) to the template you want to output
     * @see fetch
     *
     */
    public function display( $template_file, $data = array( ) )
    {
        $this->populateVar( $data );
        $output = $this->fetch( $template_file, $data );

        if ( $this->caching == true )
        {
            $this->addCache( $output, $template_file );
        }

        if ( $this->useGzip )
        {
            /* Check wether browser support compress output */
            if ( substr_count( $_SERVER[ 'HTTP_ACCEPT_ENCODING' ], 'gzip' ) )
            {
                if ( extension_loaded( 'zlib' ) )
                {
                    ob_start( 'ob_gzhandler' );
                }
                else
                {
                    ob_start();
                }
            }
            else
            {
                ob_start();
            }
            echo $output;
            ob_end_flush();
        }
        else
        {
            echo $output;
        }
    }




    /**
     * 
     * Fetch the final template output and returns it to caller
     * @access public
     * @param string $template_file Filename (with path) to the template you want to fetch
     * @return string || false
     * @see display
     *
     */
    public function fetch( $template_file, $data = array( ) )
    {
        $this->populateVar( $data );
        $template_file = $this->templateDir . $template_file;

        if ( $this->caching == true && $this->isCached( $template_file ) )
        {
            $output = $this->getCache( $template_file );
        }
        else
        {
            $output = $this->getOutput( $template_file );
        }
        return isset( $output ) ? $output : false;
    }




    /**
     *
     * Fetch the template output, and return it to caller
     * @access private
     * @param string $template_file Filename (with path) to the template to be processed
     * @return string || false
     * @see fetch, display
     */
    private function getOutput( $template_file )
    {
        extract( $this->variables );
        if ( file_exists( $template_file ) )
        {
            ob_start();
            include( $template_file );
            $output = ob_get_contents();
            ob_end_clean();
        }
        else
        {
            throw new Exception( "The template file '$template_file' does not exist" );
        }
        return (!empty( $output )) ? $output : false;
    }




    /**
     *
     * Sets the template directory
     * @access public
     * @param string $dir Path to the template dir you want to use
     */
    public function setTemplateDir( $dir )
    {
        $template_dir = $this->fixPath( $dir );

        if ( is_dir( $template_dir ) )
        {
            $this->templateDir = $template_dir;
        }
        else
        {
            throw new Exception( "The template directory '$dir' does not exist" );
        }
    }




    /**
     *
     * Sets the cache directory
     * @access public
     * @param string $dir Path to the cache dir you want to use
     * @see setCacheLifetime
     */
    public function setCacheDir( $dir )
    {
        $cache_dir = $this->fixPath( $dir );


        if ( is_dir( $cache_dir ) && is_writable( $cache_dir ) )
        {
            $this->cacheDir = $cache_dir;
        }
        else
        {
            throw new Exception( "The cache directory '$dir' either does not exist, or is not writable" );
        }
    }




    /**
     *
     * Sets how long the cache files should survive
     * @access public
     * @param INT $seconds Number of seconds the cache should survive
     * @see setCacheDir, isCached, setCaching
     */
    public function setCacheLifetime( $seconds = 0 )
    {
        $this->cacheLifetime = is_numeric( $seconds ) ? $seconds : 0;
    }




    /**
     *
     * Turn caching on or off
     * @access public
     * @param bool $state Set TRUE turns caching on, FALSE turns caching off
     * @see setCacheLifetime, isCached, setCacheDir
     */
    public function setCaching( $state )
    {
        if ( is_bool( $state ) )
        {
            $this->caching = $state;
        }
    }




    /**
     *
     * Turn on or off output compression
     * @access public
     * @param mixed $state
     */
    public function setGzip( $state = true )
    {
        $this->useGzip = $state;
    }




    /**
     *
     * Check wether cache directory is exist and writable
     * @access private
     * @return bool
     */
    private function checkCacheDir()
    {
        if ( is_dir( $this->cacheDir ) && is_writable( $this->cacheDir ) )
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
     * Checks if the template in $template is cached
     * @access public
     * @param string $file Filename of the template
     * @return bool || Exception
     * @see setCacheLifetime, setCacheDir, setCaching
     *
     */
    public function isCached( $file )
    {
        if ( $this->checkCacheDir() )
        {
            $this->cacheDir = $this->fixPath( $this->cacheDir );

            $cacheId = md5( basename( $file ) );

            $filename = $this->cacheDir . $cacheId . basename( $file );

            if ( is_file( $filename ) )
            {
                clearstatcache();
                /* time of file creation    current time-time file must exist
                  Current time must minus cached timed because current time always move,
                  so need to minus the cached time to check wether file creation time is greater than current time-cahched time, cause creation time si static */
                if ( filemtime( $filename ) > ( time() - $this->cacheLifetime ) )
                {
                    $isCached = true;
                }
                else
                {
                    $this->delCacheFile( $filename );
                    $isCached = false;
                }
            }
            return $isCached;
        }
        else
        {
            throw new Exception( 'Error with cache directory wether not exist or not writable' );
        }
    }




    /**
     *
     * Makes a cache file
     * @access private
     * @param string $content The template output that will be saved in cache
     * @param string $_SERVER['PHP_SELF'] The filename of the template that is being cached
     * @param string $id The cache identification number/string of the template you want to fetch
     * @return mixed
     * @see getCache, clearCache
     */
    private function addCache( $content, $file )
    {
        if ( $this->checkCacheDir() )
        {
            $this->cacheDir = $this->fixPath( $this->cacheDir );
            $cacheId = md5( basename( $file ) );
            $filename = $this->cacheDir . $cacheId . basename( $file );

            if ( file_put_contents( $filename, $content ) == FALSE )
            {
                throw new Exception( "Unable to write to cache" );
            }
        }
        else
        {
            throw new Exception( 'Error with cache directory wether not exist or not writable' );
        }
    }




    /**
     *
     * Returns the content of a cached file
     * @access private
     * @param string $file The filename of the template you want to fetch
     * @example index.php?id=10
     * @example index.php?id=15
     * @return string || bool || Exception
     * @see addCache, clearCache
     */
    private function getCache( $file )
    {
        if ( $this->checkCacheDir() )
        {
            $this->cacheDir = $this->fixPath( $this->cacheDir );
            $cacheId = md5( basename( $file ) );
            $filename = $this->cacheDir . $cacheId . basename( $file );
            $content = file_get_contents( $filename );
            return isset( $content ) ? $content : false;
        }
        else
        {
            throw new Exception( 'Error with cache directory wether not exist or not writable' );
        }
    }




    /**
     *
     * Deletes the stored cache files
     * @access private
     * @see addCache, getCache
     */
    private function delCacheFile( $file )
    {
        if ( file_exists( $file ) )
        {
            if ( is_writable( $file ) )
            {
                unlink( $file );
            }
            else
            {
                throw new Exception( "Unable to unlink {$file}" );
            }
        }
    }




    /**
     *
     * Check if passed in array is an associative array
     * @access private
     * @return bool
     */
    private function isAssocArray( $array )
    {
        foreach ( array_keys( $array ) as $key => $val )
        {
            if ( is_numeric( $val ) )
            {

                return false;
            }
        }
        return true;
    }




    /**
     *
     * Populate passed in variable to template so it become local to template
     * @access private
     */
    private function populateVar( $data )
    {
        if ( ( count( $data ) > 0 ) )
        {
            if ( !$this->isAssocArray( $data ) )
            {
                throw new Exception( 'Array passed to template must be an associative array' );
            }
            else
            {
                foreach ( $data as $key => $val )
                {
                    $this->variables[ $key ] = $val;
                }
            }
        }
    }




    /**
     *
     * Fix end trailing slash
     * Add trailing slah if dont exist, and do nothing if already exist
     * @access private
     * @param string $path
     * @return string
     */
    private function fixPath( $path )
    {
        $fixPath = $path;

        if ( substr( $path, -1 ) !== self::DS )
        {
            $fixPath = $fixPath . self::DS;
        }

        return $fixPath;
    }




}

?>