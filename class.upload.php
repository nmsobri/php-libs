<?php

/**
 * Class For Handling File Upload
 * @author slier
 * @example
 *
 *     $upload = new FileUpload('./upload/',$_FILES['upload'],array('.png','.jpg'));
 *     if($upload->upload())
 *     {
 *       echo 'successfully upload';
 *       echo $upload->getUploadedFileInfo();
 *     }
 *     else
 *     {
 *       echo $upload->showError();
 *     }
 */
class Upload
{

    /**
     *
     * @access private
     * @var string
     */
    private $theFile = null;



    /**
     *
     * @access private
     * @var resource
     */
    private $theTempFile = null;



    /**
     *
     * @access private
     * @var string
     */
    private $uploadDir = null;



    /**
     *
     * @access private
     * @var mixed
     */
    private $httpError = null;



    /**
     *
     * @access private
     * @var array
     */
    private $allowedExtensions = array( );



    /**
     *
     * @access private
     * @var array
     */
    private $message = array( );



    /**
     *
     * @access private
     * @var string
     */
    private $extErrorString = null;



    /**
     *
     * @access private
     * @var string
     */
    private $copyFile = null;



    /**
     *
     * @access private
     * @var string
     */
    private $fullPathToFile = null;



    /**
     *
     * @access private
     * @var bool
     */
    private $renameFile = false;



    /**
     *
     * @access private
     * @var bool
     */
    private $replaceOldFile = false;



    /**
     *
     * @access
     * @var bool
     */
    private $createDirectory = true;



    /**
     *
     * @access private
     * @var bool
     */
    private $filenameCheck = true;



    /**
     *
     * @access private
     * @var int
     */
    private $filenameLength = 100;




    /**
     *
     * Constructor Function
     * @access public
     */
    public function __construct( $uploadDir, $file, $extensions )
    {
        $this->uploadDir = $this->constructUploadDir( $uploadDir );
        $this->theFile = $file[ 'name' ];
        $this->theTempFile = $file[ 'tmp_name' ];
        $this->httpError = $file[ 'error' ];
        $this->allowedExtensions = $extensions;
    }




    /**
     *
     * Main Method
     * Upload The File
     * @access public
     * @return bool
     */
    public function upload()
    {
        $newName = $this->setFileName();
        $this->copyFile = $newName . $this->getExtension( $this->theFile );

        if ( !$this->checkFileName( $newName ) )
        {
            return false;
        }

        if ( !$this->validateExtension() )
        {
            return false;
        }

        if ( !$this->isFileUploaded() )
        {
            return false;
        }

        if ( !$this->moveUpload( $this->theTempFile, $this->copyFile ) )
        {
            return false;
        }

        return true;
    }




    /**
     *
     * Show Appropriate Error Message
     * @access public
     * @return string
     */
    public function showError()
    {
        $msg_string = null;
        foreach ( $this->message as $value )
        {
            $msg_string .= $value . '<br>' . PHP_EOL;
        }
        return $msg_string;
    }




    /**
     *
     * Method to set how long can file name can be
     * @access public
     * @param int $length
     */
    public function setFileNameLength( $length = 100 )
    {
        $this->filenameLength = ( int ) $length;
    }




    /**
     *
     * Method to set either to check for valid file name
     * @param bool $type
     */
    public function setFileNameCheck( $type = true )
    {
        $this->filenameCheck = ( bool ) $type;
    }




    /**
     *
     * Method to set either to create upload directory if it dosent exist
     * @param bool $type
     */
    public function setCreateDirectory( $type = true )
    {
        $this->createDirectory = ( bool ) $type;
    }




    public function setRenameFile( $type = true )
    {
        $this->renameFile = ( boolean ) $type;
    }




    public function setReplaceOldFile( $type = true )
    {
        $this->replaceOldFile = ( boolean ) $type;
    }




    public function getFullPathToFile()
    {
        if ( is_null( $this->fullPathToFile ) )
        {
            trigger_error( 'Pleease call method upload() first', E_USER_ERROR );
        }
        else
        {
            return $this->fullPathToFile;
        }
    }




    /**
     * Get file bname
     * @return string 
     */
    public function getFileName()
    {

        if ( is_null( $this->fullPathToFile ) )
        {
            trigger_error( 'Pleease call method upload() first', E_USER_ERROR );
        }
        else
        {
            return basename( $this->fullPathToFile );
        }
    }




    /**
     *
     * Move The Uploaded File To New Location
     * @access private
     * @param resource $tmp_file
     * @param string $new_file
     * @return true
     */
    private function moveUpload( $tmp_file, $new_file )
    {
        umask( 0 );
        if ( !$this->isFileExist( $new_file ) )
        {
            $newfile = $this->uploadDir . $new_file;

            if ( $this->checkDir( $this->uploadDir ) )
            {
                if ( move_uploaded_file( $tmp_file, $newfile ) )
                {
                    $this->fullPathToFile = $newfile;
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                $this->message[ ] = $this->errorText( 14 );
                return false;
            }
        }
        else
        {
            $this->message[ ] = $this->errorText( 15 );
            return false;
        }
    }




    /**
     *
     * Set File Name
     * @access private
     * @return string
     */
    private function setFileName()
    {
        if ( empty( $this->theFile ) )
        {
            return null;
        }

        $name = substr( $this->theFile, 0, strpos( $this->theFile, '.' ) );

        if ( $this->renameFile )
        {
            $name .= strtotime( 'now' );
        }
        return $name;
    }




    /**
     *
     * Check Wether $name is Valid Filename
     * @access private
     * @param mixed $name
     * @return bool
     */
    private function checkFileName( $name )
    {
        if ( !is_null( $name ) )
        {
            if ( strlen( $name ) > $this->filenameLength )
            {
                $this->message[ ] = $this->errorText( 13 );
                return false;
            }
            else
            {
                if ( $this->filenameCheck )
                {
                    if ( preg_match( "/^[a-z0-9_]*$/i", $name ) )
                    {
                        return true;
                    }
                    else
                    {
                        $this->message[ ] = $this->errorText( 12 );
                        return false;
                    }
                }
                else
                {
                    return true;
                }
            }
        }
        else
        {
            $this->message[ ] = $this->errorText( 10 );
            return false;
        }
    }




    /**
     *
     * Check Wether Uploaded File Is In The Allowed Extension Type
     * @access private
     * @return bool
     */
    private function validateExtension()
    {
        $extension = $this->getExtension( $this->theFile );
        $ext_array = $this->allowedExtensions;

        if ( in_array( $extension, $ext_array ) )
        {
            return true;
        }
        else
        {
            $this->showAllowedExtensions();
            $this->message[ ] = $this->errorText( 11 );
            return false;
        }
    }




    /**
     * Get File Extension
     * @access private
     * @param mixed $file
     * @return string
     */
    private function getExtension( $file )
    {
        $ext = strtolower( strrchr( $file, '.' ) );
        return $ext;
    }




    /**
     * Check Directory
     * @access private
     * @param string $directory
     * @return bool
     */
    private function checkDir( $directory )
    {
        if ( !is_dir( $directory ) )
        {
            if ( $this->createDirectory )
            {
                umask( 0 );
                mkdir( $directory, 0777 );
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }




    /**
     * Check Wether File Already Exist
     * @access private
     * @param string $file_name
     * @return bool
     */
    private function isFileExist( $file_name )
    {
        if ( $this->replaceOldFile )
        {
            return false;
        }
        else
        {
            if ( file_exists( $this->uploadDir . $file_name ) )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }




    /**
     *
     * Get Uploaded File Info
     * @access public
     * @return string
     */
    public function getUploadedFileInfo()
    {
        $file = $this->fullPathToFile;
        $str = 'File name: ' . basename( $file ) . '<br />';
        $str .= 'File size: ' . filesize( $file ) . ' bytes<br />';
        if ( function_exists( 'mime_content_type' ) )
        {
            $str .= 'Mime type: ' . mime_content_type( $file ) . '<br />';
        }
        if ( $img_dim = getimagesize( $file ) )
        {
            $str .= 'Image dimensions: x = ' . $img_dim[ 0 ] . 'px, y = ' . $img_dim[ 1 ] . 'px<br />';
        }
        return $str;
    }




    /**
     * Safely add '/' to the end of $dir if its not exist
     * @access private
     * @param mixed $dir
     * @return string
     */
    private function constructUploadDir( $dir )
    {
        if ( substr( $dir, -1, 1 ) != '/' )
        {
            $dir .= '/';
        }
        return $dir;
    }




    /**
     * This method was first located inside the foto_upload extension
     * @param mixed $file
     * @deprecated
     */
    private function delTempFile( $file )
    {
        $delete = @unlink( $file );
        clearstatcache();
        if ( @file_exists( $file ) )
        {
            $filesys = eregi_replace( "/", "\\", $file );
            $delete = @system( "del $filesys" );
            clearstatcache();
            if ( @file_exists( $file ) )
            {
                $delete = @chmod( $file, 0775 );
                $delete = @unlink( $file );
                $delete = @system( "del $filesys" );
            }
        }
    }




    /**
     *
     * This method is only used for detailed error reporting
     * @access private
     */
    private function showAllowedExtensions()
    {
        $this->extErrorString = implode( ' ', $this->allowedExtensions );
    }




    /**
     * Some error (HTTP)reporting, change the messages or remove options if you like
     * @param mixed $err_num
     * @return mixed
     */
    private function errorText( $err_num )
    {
        $error[ 0 ] = 'File: <b>' . $this->theFile . '</b> successfully uploaded!';
        $error[ 1 ] = 'The uploaded file exceeds the max upload filesize directive in the server configuration.';
        $error[ 2 ] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.';
        $error[ 3 ] = 'The uploaded file was only partially uploaded.';
        $error[ 4 ] = 'An error occured while uploading.';
        $error[ 6 ] = 'Missing a temporary folder.';
        $error[ 7 ] = 'Failed to write file to disk.';
        $error[ 10 ] = 'Please select a file for upload.';
        $error[ 11 ] = 'Only files with the following extensions are allowed: <b>' . $this->extErrorString . '</b>';
        $error[ 12 ] = 'Sorry, the filename contains invalid characters. Use only alphanumerical chars and separate parts of the name (if needed) with an underscore.';
        $error[ 13 ] = 'The filename exceeds the maximum length of ' . $this->filenameLength . ' characters.';
        $error[ 14 ] = 'Sorry, the upload directory doesn\'t exist!.';
        $error[ 15 ] = 'Uploading <b>' . $this->theFile . '...Error!</b> Sorry, a file with this name already exitst.';
        $error[ 16 ] = 'The uploaded file is renamed to <b>' . $this->copyFile . '</b>.';
        return $error[ $err_num ];
    }




    private function isFileUploaded()
    {
        if ( is_uploaded_file( $this->theTempFile ) )
        {
            return true;
        }
        else
        {
            $this->message[ ] = $this->errorText( $this->httpError );
            return false;
        }
    }




}

?>