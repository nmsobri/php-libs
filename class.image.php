<?php

/**
 *
 * Class for handling inmage creation
 * @author Joddy
 */

class Image
{
    /**
     *
     * @var array
     * @access protected
     */
    protected $allowedExtensions = array( '.jpg', 'jpeg', '.gif', '.png' );

    /**
     *
     * @var int
     * @access protected
     */
    protected $jpegQuality = 100;

    /**
     *
     * @var string
     * @access protected
     */
    protected $imageUrl;

    /**
     *
     * @var resource
     * @access protected
     */
    protected $imageObj;

    /**
     *
     * @var string
     * @access protected
     */
    protected $imageType;

    /**
     *
     * @var resource
     * @access protected
     */
    protected $resizedImage;
    
    /**
     *
     * @var string
     * @access protected
     */
    protected $imageName;

    /**
     *
     * @var string
     * @access protected
     */
    protected $locationToSave;

    /**
     *
     * @var array
     * @access protected
     */
    protected $errorMsg;

    /**
     *
     * @var bool
     * @access protected
     */
    protected $uniqueName = false;



    /**
     * Constructor Method
     * @access public
     * @param mixed $imageFile
     * @param mixed $imageName
     * @param mixed $locationToSave
     * @param array $allowedExtension
     * @param bool $uniqueName
     */
    public function __construct( $imageFile, $imageName, $locationToSave, $allowedExtensions = null, $uniqueName = null )
    {
        $this->imageUrl = $imageFile;
        $this->imageName = $imageName;
        $this->locationToSave = $locationToSave;
        $this->allowedExtensions = ( is_null( $allowedExtensions ) ) ? $this->allowedExtensions : ( array ) $allowedExtensions;
        $this->uniqueName = ( is_null( $uniqueName ) ) ? false : ( boolean ) $uniqueName;
        $this->imageType = $this->getImageType( $imageFile, $imageName );
        $this->init();
    }



    /**
     *
     * Destructor Method
     * Free Memory
     * @access public
     */
    public function __destruct()
    {
        if( $this->isImage( $this->imageUrl, $this->imageName ) )
        {
            imagedestroy( $this->imageObj );
            if( !empty( $this->resizedImage ) )
            {
                imagedestroy( $this->resizedImage );
            }
        }
    }



    /**
     *
     * Method to perform initialization
     * @access private
     */
    private function init()
    {
        $this->isImgDir();
        $this->isImgDirWritable();
        $this->isImage( $this->imageUrl, $this->imageName );
    }



    /**
     * Method to create new blank image
     * @access private
     * @return mixed
     */
    private function create()
    {
        if( $this->isImage( $this->imageUrl, $this->imageName ) )
        {
            switch( $this->imageType )
            {
                case '.gif':
                    $this->imageObj = imagecreatefromgif( $this->imageUrl );
                    break;

                case '.jpg':
                    $this->imageObj = imagecreatefromjpeg( $this->imageUrl );
                    break;

                case '.jpeg':
                    $this->imageObj = imagecreatefromjpeg( $this->imageUrl );
                    break;

                case '.png':
                    $this->imageObj = imagecreatefrompng( $this->imageUrl );
                    break;
            }
            return true;
        }
        else
        {
            return false;
        }
    }



    /**
     *
     * Method To Resize Image By Percent
     * @param int $Percent
     * @return bool
     */
    public function resizeByPercent( $percent )
    {
        $this->create();
        /* Get new dimensions */
        list( $width, $height ) = getimagesize( $this->imageUrl );
        $newWidth = intval( ( $Width * $percent ) / 100 );
        $newHeight = intval( ( $Height * $percent ) / 100 );

        $this->resizedImage = imagecreatetruecolor( $NewWidth, $NewHeight );
        imagecopyresampled( $this->resizedImage, $this->imageObj, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height );

        if( !$this->uniqueName )
        {
            if( $this->isImageExists( $this->locationToSave . $this->imageName ) )
            {
                $this->errorMsg = array( );
                $this->errorMsg[] = $this->errorText( 4 );
                return false;
            }
            else
            {
                if( $this->outputIMage( $this->imageType ) )
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            $this->imageName = $this->createUniqueImageName( $this->imageName );
            if( $this->outputIMage( $this->imageType ) )
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
     * Method To Resize Image Based on Given Respective Width And Heigth
     * @param int $W
     * @param int $H
     * @return bool
     */
    public function resizeByWidthHeight( $w, $h )
    {
        $this->create();
        /* Get new dimensions */
        list($width, $height) = getimagesize( $this->imageUrl );
        $newWidth = $w;
        $newHeight = $h;

        $this->resizedImage = imagecreatetruecolor( $newWidth, $newHeight );
        imagecopyresampled( $this->resizedImage, $this->imageObj, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height );

        if( !$this->uniqueName )
        {
            if( $this->isImageExists( $this->locationToSave . $this->imageName ) )
            {
                $this->errorMsg = array( );
                $this->errorMsg[] = $this->errorText( 4 );
                return false;
            }
            else
            {
                if( $this->outputIMage( $this->imageType ) )
                {

                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            $this->imageName = $this->createUniqueImageName( $this->imageName );
            if( $this->outputIMage( $this->imageType ) )
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
     * Method To Check Wether Image Is Exist
     * To check, before move the upload file, typically used to check wethere the same file already being upload
     * To prevent override
     * @access public
     * @param mixed $image
     * @return bool
     */
    public function isImageExists( $image )
    {
        if( file_exists( $image ) )
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
     * Provide acces to override $locationTosave set in constructor
     * Typically used to set different directory for thumbnail
     * @access public
     * @param string $location
     */

    public function setImgLocation( $location )
    {
        $this->locationToSave = $location;
    }



    /**
     *
     * Save/output image to location
     * @access private
     * @param string $imageType
     * @return bool
     */

    private function outputIMage( $imageType )
    {
        if( $this->isImgDir() && $this->isImgDirWritable() )
        {
            switch( $imageType )
            {
                case '.gif':
                    imagegif( $this->resizedImage, $this->locationToSave . $this->imageName );
                    break;

                case '.jpg':
                    imagejpeg( $this->resizedImage, $this->locationToSave . $this->imageName, $this->jpegQuality );
                    break;

                case '.jpeg':
                    imagejpeg( $this->resizedImage, $this->locationToSave . $this->imageName, $this->jpegQuality );
                    break;

                case '.png':
                    imagepng( $this->resizedImage, $this->locationToSave . $this->imageName );
                    break;
            }
            return true;
        }
        else
        {
            return false;
        }
    }



    /**
     *
     * Method to check wether submitted file is an image and its in allowed extension list
     * @access private
     * @param mixed $File
     * @return bool
     */
    private function isImage( $imageFile, $imageName )
    {
        if( $this->isImageExists( $imageFile ) )
        {
            $extension = strtolower( substr( $imageName, strrpos( $imageName, '.' ) ) );
            if( count( $this->allowedExtensions ) > 0 )
            {
                if( !in_array( $extension, $this->allowedExtensions ) )
                {
                    $this->errorMsg[] = $this->errorText( 3 );
                    return false;
                }
                else
                {
                    return true;
                }
            }
            else
            {
                $this->errorMsg[] = $this->errorText( 2 );
                return false;
            }
        }
        else
        {
            $this->errorMsg[] = $this->errorText( 1 );
            return false;
        }
    }



    /**
     *
     * Method To Check Image Extension
     * @access private
     * @param string $imageFile
     * @param string $imageName
     * @return bool
     */
    private function getImageType( $imageFile, $imageName )
    {
        if( $this->isImage( $imageFile, $imageName ) )
        {
            $extension = strtolower( substr( $imageName, strrpos( $imageName, '.' ) ) );

            switch( $extension )
            {
                case '.gif':
                    return '.gif';
                    break;

                case '.jpg':
                    return '.jpg';

                    break;

                case '.png':
                    return '.png';

                    break;

                default:
                    return false;
                    break;
            }
        }
        else
        {
            return false;
        }
    }



    /**
     *
     * Method To Create Unique Image Name
     * @access private
     * @param string $imageName
     * @return string
     */
    private function createUniqueImageName( $imageName )
    {
        $imageName = substr( $imageName, 0, strpos( $imageName, '.' ) );
        $imageName .= '_' . strtotime( 'now' ) . $this->imageType;
        return $imageName;
    }



    /**
     * 
     * Method to check wether directory location for image is exist
     * @access private
     * @return bool
     */
    private function isImgDir()
    {
        if( is_dir( $this->locationToSave ) )
        {
            return true;
        }
        else
        {
            $this->errorMsg[] = $this->errorText( 5 );
            return false;
        }
    }



    /**
     *
     * Method to check wethere directory location for image is writable
     * @access private
     * @return bool
     */
    private function isImgDirWritable()
    {
        if( is_writable( $this->locationToSave ) )
        {
            return true;
        }
        else
        {
            $this->errorMsg[] = $this->errorText( 6 );
            return false;
        }
    }



    /**
     *
     * Method to check if error occured during object initilization
     * @access public
     * @return bool
     */
    public function isError()
    {
        return ( count( $this->errorMsg ) > 0 ) ? true : false;
    }



    /**
     *
     * Method To Retun Error Message
     * @access public
     * @return string
     */
    public function getErrorMsg( $breakElem = '<br />' )
    {
        $msg = '';
        if( count( $this->errorMsg ) > 0 )
        {
            array_unshift( $this->errorMsg, $this->errorText( 0 ) );
            foreach( $this->errorMsg as $value )
            {
                $msg .= $value . $breakElem . "\n";
            }
        }
        return $msg;
    }



    /**
     *
     * Method To Get Error List
     * @access private
     * @param mixed $errorNum
     * @return string
     */
    private function errorText( $errorNum )
    {
        $error[0] = 'Please correct the following error(s):';
        $error[1] = 'Image File Not Exist';
        $error[2] = 'Allowed Extension List Are Empty';
        $error[3] = 'File Are Not In The Allowed Extension List';
        $error[4] = 'File Already Exist';
        $error[5] = 'Directory location for image dosent exist';
        $error[6] = 'Directory location for image is not writable';
        return $error[$errorNum];
    }



}

?>