<?php

/**
 * Class For Handling File Upload
 * @author slier
 */

namespace utility;

class Upload
{

    /**
     * @var string file name
     */
    private $theFile = null;


    /**
     * @var Upload resource of the uploaded file
     */
    private $theTempFile = null;


    /**
     * @var string upload directory
     */
    private $uploadDir = null;


    /**
     * @var string HttpError
     */
    private $httpError = null;


    /**
     * @var array extension list
     */
    private $allowedExtensions = array();


    /**
     * @var array error message
     */
    private $message = array();


    /**
     * @var string extension error
     */
    private $extErrorString = null;


    /**
     * @var string name of copy file
     */
    private $copyFile = null;


    /**
     * @var string
     */
    private $fullPathToFile = null;


    /**
     * @var bool
     */
    private $renameFile = false;


    /**
     * @var bool
     */
    private $replaceOldFile = false;


    /**
     * @var bool
     */
    private $createDirectory = true;


    /**
     * @var bool
     */
    private $filenameCheck = true;


    /**
     * @var int
     */
    private $filenameLength = 100;


    /**
     * @param string $upload_dir
     * @param resource $_FILES ['element']
     * @param array $extensions ['txt', 'doc', 'pdf']
     *
     * $upload = new FileUpload( 'upload/', $_FILES['upload'], ['doc','txt'] );
     * if( $upload->upload() )
     * {
     *   print 'successfully upload';
     *   print $upload->getUploadedFileInfo();
     * }
     * else
     * {
     *   print $upload->showError();
     * }
     */
    public function __construct( $upload_dir, $file, array $extensions )
    {
        $this->uploadDir = $this->constructUploadDir( $upload_dir );
        $this->theFile = $file['name'];
        $this->theTempFile = $file['tmp_name'];
        $this->httpError = $file['error'];
        $this->allowedExtensions = $extensions;
    }


    /**
     * Upload The File
     *
     * @return bool
     */
    public function upload()
    {
        $file_name = $this->setFileName();
        $this->copyFile = $file_name . '.' . $this->getExtension( $this->theFile );

        if( !$this->checkFileName( $file_name ) ){
            return false;
        }

        if( !$this->validateExtension() ){
            return false;
        }

        if( !$this->isFileUploaded() ){
            return false;
        }

        if( !$this->moveUpload( $this->theTempFile, $this->copyFile ) ){
            return false;
        }

        return true;
    }


    /**
     * Show Error Message
     *
     * @return string
     */
    public function showError( $template = '%s<br>' )
    {
        $messages = null;
        foreach( $this->message as $value ){
            $messages .= sprintf( $template, $value );
        }
        return $messages;
    }


    /**
     * Set how long can file name can be
     *
     * @param int $length
     * @return void
     */
    public function setFileNameLength( $length = 100 )
    {
        $this->filenameLength = ( int )$length;
    }


    /**
     * Set whether to check for valid file name
     *
     * @param bool $type
     * @return void
     */
    public function setFileNameCheck( $type = true )
    {
        $this->filenameCheck = ( bool )$type;
    }


    /**
     * Set whether to create upload directory if it dosent exist
     *
     * @param bool $type
     * @return void
     */
    public function setCreateDirectory( $type = true )
    {
        $this->createDirectory = ( bool )$type;
    }


    /**
     * Set whether to rename file
     *
     * @param bool $type
     * @return void
     */
    public function setRenameFile( $type = true )
    {
        $this->renameFile = ( boolean )$type;
    }


    /**
     * Set whether to replace existing file
     *
     * @param bool $type
     * @return void
     */
    public function setReplaceOldFile( $type = true )
    {
        $this->replaceOldFile = ( boolean )$type;
    }


    /**
     * Get full path to uploaded file
     *
     * @return null|string
     */
    public function getFullPathToFile()
    {
        return $this->fullPathToFile;
    }


    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName()
    {
        return basename( $this->fullPathToFile );
    }


    /**
     * Move The Uploaded File To New Location
     *
     * @param resource $tmp_file
     * @param string $new_file
     * @return true
     */
    private function moveUpload( $tmp_file, $new_file )
    {
        umask( 0 );
        if( !$this->isFileExist( $new_file ) ){
            $newfile = $this->uploadDir . $new_file;

            if( $this->checkDir( $this->uploadDir ) ){
                if( move_uploaded_file( $tmp_file, $newfile ) ){
                    $this->fullPathToFile = $newfile;
                    return true;
                }
                $this->message[] = $this->errorText( 4 );
                return false;
            }
            $this->message[] = $this->errorText( 14 );
            return false;
        }
        $this->message[] = $this->errorText( 15 );
        return false;
    }


    /**
     * Set File Name
     *
     * @return string
     */
    private function setFileName()
    {
        $name = pathinfo( $this->theFile, PATHINFO_FILENAME );

        if( $this->renameFile ){
            $name .= strtotime( 'now' );
        }

        return $name;
    }


    /**
     * Check Wether $name is Valid Filename
     *
     * @param mixed $name
     * @return bool
     */
    private function checkFileName( $name )
    {
        if( !is_null( $name ) ){

            if( strlen( $name ) > $this->filenameLength ){
                $this->message[] = $this->errorText( 13 );
                return false;
            }

            if( $this->filenameCheck ){
                if( !preg_match( '#^[a-z0-9_]*$#i', $name ) ){
                    $this->message[] = $this->errorText( 12 );
                    return false;
                }
                return true;
            }
            return true;
        }

        $this->message[] = $this->errorText( 10 );
        return false;
    }


    /**
     * Check Whether Uploaded File Is In The Allowed Extension Type
     *
     * @return bool
     */
    private function validateExtension()
    {
        $extension = $this->getExtension( $this->theFile );
        $ext_array = $this->allowedExtensions;

        if( !in_array( $extension, $ext_array ) ){
            $this->showAllowedExtensions();
            $this->message[] = $this->errorText( 11 );
            return false;
        }
        return true;
    }


    /**
     * Get File Extension
     *
     * @param mixed $file
     * @return string
     */
    private function getExtension( $file )
    {
        return pathinfo( $file, PATHINFO_EXTENSION );
    }


    /**
     * Check Directory
     *
     * @param string $directory
     * @return bool
     */
    private function checkDir( $directory )
    {
        if( !is_dir( $directory ) ){
            if( $this->createDirectory ){
                umask( 0 );
                mkdir( $directory, 0777 );
                return true;
            }
            return false;
        }
        return true;
    }


    /**
     * Check Wether File Already Exist
     *
     * @param string $file_name
     * @return bool
     */
    private function isFileExist( $file_name )
    {
        if( $this->replaceOldFile ){
            return false;
        }

        if( file_exists( $this->uploadDir . $file_name ) ){
            return true;
        }

        return false;
    }


    /**
     * Get Uploaded File Info
     *
     * @return string
     */
    public function getUploadedFileInfo()
    {
        $file = $this->fullPathToFile;
        $str = 'File name: ' . basename( $file ) . '<br>';
        $str .= 'File size: ' . filesize( $file ) . ' bytes<br>';

        if( function_exists( 'mime_content_type' ) ){
            $str .= 'Mime type: ' . mime_content_type( $file ) . '<br>';
        }

        if( $img_dim = getimagesize( $file ) ){
            $str .= 'Image dimensions: x = ' . $img_dim[0] . 'px, y = ' . $img_dim[1] . 'px<br>';
        }

        return $str;
    }


    /**
     * Safely add '/' to the end of $dir if its not exist
     *
     * @param mixed $dir
     * @return string
     */
    private function constructUploadDir( $dir )
    {
        if( substr( $dir, -1, 1 ) != '/' ){
            $dir .= '/';
        }
        return $dir;
    }


    /**
     * This method was first located inside the foto_upload extension
     *
     * @param mixed $file
     * @deprecated
     */
    private function delTempFile( $file )
    {
        @unlink( $file );
        clearstatcache();
        if( @file_exists( $file ) ){
            $filesys = preg_replace( "#/#i", "#\\#", $file );
            @system( "del $filesys" );
            clearstatcache();
            if( @file_exists( $file ) ){
                @chmod( $file, 0775 );
                @unlink( $file );
                @system( "del $filesys" );
            }
        }
    }


    /**
     * This method is only used for detailed error reporting
     *
     * @return void
     */
    private function showAllowedExtensions()
    {
        $this->extErrorString = implode( ', ', $this->allowedExtensions );
    }


    /**
     * Check whether file is upload
     *
     * @return bool
     */
    private function isFileUploaded()
    {
        if( is_uploaded_file( $this->theTempFile ) ){
            return true;
        }

        $this->message[] = $this->errorText( $this->httpError );
        return false;
    }


    /**
     * Get the error message
     *
     * @param mixed $err_num
     * @return string
     */
    private function errorText( $err_num )
    {
        $error[0] = 'File: <b>' . $this->theFile . '</b> successfully uploaded!';
        $error[1] = 'The uploaded file exceeds the max upload filesize directive in the server configuration.';
        $error[2] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.';
        $error[3] = 'The uploaded file was only partially uploaded.';
        $error[4] = 'An error occured while uploading.';
        $error[6] = 'Missing a temporary folder.';
        $error[7] = 'Failed to write file to disk.';
        $error[10] = 'Please select a file for upload.';
        $error[11] = 'Only files with the following extensions are allowed: <b>' . $this->extErrorString . '</b>';
        $error[12] = 'Sorry, the filename contains invalid characters. Use only alphanumerical chars and separate parts of the name (if needed) with an underscore.';
        $error[13] = 'The filename exceeds the maximum length of ' . $this->filenameLength . ' characters.';
        $error[14] = 'Sorry, the upload directory doesn\'t exist!.';
        $error[15] = 'Uploading <b>' . $this->theFile . '...Error!</b> Sorry, a file with this name already exitst.';
        $error[16] = 'The uploaded file is renamed to <b>' . $this->copyFile . '</b>.';
        return $error[$err_num];
    }


}

?>