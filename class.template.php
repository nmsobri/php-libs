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
     * @var array
     */
    private $variables = array();


    /**
     * The directory where the templates are stored
     * @var string
     */
    private $templateDir = null;


    /**
     * Turns caching on or off
     * CURRENTLY CACHING ONLY IMPLEMENTED IN display() METHOD.
     * CURRENTLY THERE IS NO UNIQUE ID FOR CACHE TEMPLATE,SO IF U USE SAME LAYOUT FOR ALL PAGE, PROBABLY ALL PAGE GONNA GET SAME CONTENT DUE TO CACHE OUTPUT
     * TODO..MAKE TEMPLATE CACHING UNIQUE
     * @var bool
     */
    private $caching = false;


    /**
     * The directory where the cache files will be saved.
     * @var string
     */
    private $cacheDir = 'cache';


    /**
     * Lifetime of a cache file in seconds.
     * @var int
     */
    private $cacheLifetime = 600;


    /**
     * Boolean whether to compress final output
     * @var bool
     */
    private $useGzip = false;


    /**
     * Constructor Function
     * @param string $templateDir
     * @param array $data to be used within template
     *
     */
    public function __construct( $templateDir, $data = array() )
    {
        $this->setTemplateDir( $templateDir );
        $this->populateVar( $data );

    }


    /**
     * Adds a data that can be used by the templates.
     * Adds a new array index to the variable property.
     * This new array index will be treated as a variable by the templates.
     * @param string $name variable name to use in the template
     * @param string $value content to assign
     * @return void
     */
    public function set( $name, $value )
    {
        $this->variables[$name] = $value;
    }


    /**
     * Set multiple data using array
     * @param array $data
     */
    public function sets( array $data )
    {
        $this->populateVar( $data );
    }

    /**
     * Get the value of variable with given name
     * @param string $name
     * @return string|null
     */
    public function get( $name )
    {
        return isset( $this->variables[$name] ) ? $this->variables[$name] : null;
    }


    /**
     * Return names of all the added variables
     * @return array
     */
    public function getVars()
    {
        $variables = array_keys( $this->variables );
        return ( !empty( $variables ) ) ? $variables : false;
    }


    /**
     * Outputs/echo the template output
     * Fetches template output, and echoes it to the browser.
     * @param string $template_file
     * @param array $data
     * @return string
     */
    public function display( $template_file, $data = array() )
    {
        $output = $this->fetch( $template_file, $data );

        if( $this->caching == true ) {
            $this->addCache( $output, $template_file );
        }

        if( $this->useGzip ) {
            #Check whether browser support compress output
            if( substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) ) {
                if( extension_loaded( 'zlib' ) ) {
                    ob_start( 'ob_gzhandler' );
                }
                else {
                    ob_start();
                }
            }
            else {
                ob_start();
            }
            echo $output;
            ob_end_flush();
        }
        else {
            echo $output;
        }
    }


    /**
     * Fetch template output and returns it value
     * @param string $template_file Filename (with path) to the template to fetch
     * @param array $data
     * @return string|bool
     */
    public function fetch( $template_file, $data = array() )
    {
        $this->populateVar( $data );
        $template_file = $this->templateDir . $template_file;

        if( $this->caching == true && $this->isCached( $template_file ) ) {
            $output = $this->getCache( $template_file );
        }
        else {
            $output = $this->getOutput( $template_file );
        }
        return isset( $output ) ? $output : false;
    }


    /**
     * Fetch the template output, and return it to caller
     * @param string $template_file filename (with path) to the template to be processed
     * @throws Exception
     * @return string |bool
     */
    private function getOutput( $template_file )
    {
        extract( $this->variables );
        if( file_exists( $template_file ) ) {
            ob_start();
            include( $template_file );
            $output = ob_get_contents();
            ob_end_clean();
        }
        else {
            throw new Exception( "The template file '$template_file' does not exist" );
        }
        return ( !empty( $output ) ) ? $output : false;
    }


    /**
     * Sets the template directory
     * @param string $dir path to the template dir
     * @throws Exception
     * @return void
     */
    public function setTemplateDir( $dir )
    {
        $template_dir = $this->fixPath( $dir );

        if( is_dir( $template_dir ) ) {
            $this->templateDir = $template_dir;
        }
        else {
            throw new Exception( "The template directory '$dir' does not exist" );
        }
    }


    /**
     * Sets the cache directory
     * @param string $dir path to the cache dir you want to use
     * @throws Exception
     * @return void
     */
    public function setCacheDir( $dir )
    {
        $cache_dir = $this->fixPath( $dir );

        if( is_dir( $cache_dir ) && is_writable( $cache_dir ) ) {
            $this->cacheDir = $cache_dir;
        }
        else {
            throw new Exception( "The cache directory '$dir' either does not exist, or is not writable" );
        }
    }


    /**
     * Sets how long the cache files should exists
     * @param int $seconds number of seconds the cache should survive
     * @return void
     */
    public function setCacheLifetime( $seconds = 0 )
    {
        $this->cacheLifetime = is_numeric( $seconds ) ? $seconds : 0;
    }


    /**
     * Turn caching on or off
     * @param bool $state Set TRUE turns caching on, FALSE turns caching off
     * @return void
     */
    public function setCaching( $state )
    {
        if( is_bool( $state ) ) {
            $this->caching = $state;
        }
    }


    /**
     * Turn on or off output compression
     * @param bool $state
     * @return void
     */
    public function setGzip( $state = true )
    {
        $this->useGzip = $state;
    }


    /**
     * Check whether cache directory is exist and writable
     * @return bool
     */
    private function checkCacheDir()
    {
        if( is_dir( $this->cacheDir ) && is_writable( $this->cacheDir ) ) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Checks if the template in $template is cached
     * @param string $file filename of the template
     * @see setCacheLifetime, setCacheDir, setCaching
     * @throws Exception
     * @return bool|Exception
     */
    public function isCached( $file )
    {
        if( $this->checkCacheDir() ) {
            $this->cacheDir = $this->fixPath( $this->cacheDir );

            $cacheId = md5( basename( $file ) );
            $filename = $this->cacheDir . $cacheId . basename( $file );

            if( is_file( $filename ) ) {
                clearstatcache();
                /* time of file creation    current time-time file must exist
                  Current time must minus cached timed because current time always move,
                  so need to minus the cached time to check whether file creation time is greater than current time-cached time, cause creation time is static */
                if( filemtime( $filename ) > ( time() - $this->cacheLifetime ) ) {
                    $isCached = true;
                }
                else {
                    $this->delCacheFile( $filename );
                    $isCached = false;
                }
            }
            return $isCached;
        }
        else {
            throw new Exception( 'Error with cache directory whether not exist or not writable' );
        }
    }


    /**
     * Crate cache file
     * @param string $content template output that will be saved in cache
     * @param string $file filename of the template to cache
     * @see getCache, clearCache
     * @throws Exception
     * @return void|Exception
     */
    private function addCache( $content, $file )
    {
        if( $this->checkCacheDir() ) {
            $this->cacheDir = $this->fixPath( $this->cacheDir );
            $cacheId = md5( basename( $file ) );
            $filename = $this->cacheDir . $cacheId . basename( $file );

            if( file_put_contents( $filename, $content ) == FALSE ) {
                throw new Exception( "Unable to write to cache" );
            }
        }
        else {
            throw new Exception( 'Error with cache directory whether not exist or not writable' );
        }
    }


    /**
     * Returns the content of a cached file
     * @param string $file filename of the template file to fetch
     * @example index.php?id=10
     * @example index.php?id=15
     * @see addCache, clearCache
     * @throws Exception
     * @return string|bool|Exception
     */
    private function getCache( $file )
    {
        if( $this->checkCacheDir() ) {
            $this->cacheDir = $this->fixPath( $this->cacheDir );
            $cacheId = md5( basename( $file ) );
            $filename = $this->cacheDir . $cacheId . basename( $file );
            $content = file_get_contents( $filename );
            return isset( $content ) ? $content : false;
        }
        else {
            throw new Exception( 'Error with cache directory wether not exist or not writable' );
        }
    }


    /**
     * Deletes the stored cache files
     * @param string $file
     * @see addCache, getCache
     * @throws Exception
     * @return void
     */
    private function delCacheFile( $file )
    {
        if( file_exists( $file ) ) {
            if( is_writable( $file ) ) {
                unlink( $file );
            }
            else {
                throw new Exception( "Unable to unlink {$file}" );
            }
        }
    }


    /**
     * Check if passed in array is an associative array
     * @param array $array
     * @return bool
     */
    private function isAssocArray( $array )
    {
        foreach( array_keys( $array ) as $val ) {
            if( is_numeric( $val ) ) {
                return false;
            }
        }
        return true;
    }


    /**
     * Populate passed in variable to template so it become local to template
     * @param array $data
     * @throws Exception
     * @return void
     */
    private function populateVar( $data )
    {
        if( ( count( $data ) > 0 ) ) {
            if( !$this->isAssocArray( $data ) ) {
                throw new Exception( 'Array passed to template must be an associative array' );
            }
            else {
                foreach( $data as $key => $val ) {
                    $this->variables[$key] = $val;
                }
            }
        }
    }


    /**
     * Fix end trailing slash
     * Add trailing slash if do not exist, and do nothing if already exist
     * @param string $path
     * @return string
     */
    private function fixPath( $path )
    {
        $fixPath = $path;

        if( substr( $path, -1 ) !== self::DS ) {
            $fixPath = $fixPath . self::DS;
        }

        return $fixPath;
    }


}

?>