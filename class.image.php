<?php

/**
 *
 * @example
 * $obj = new Image('input.jpg', array('jpg','png'));
 * $obj->resize(150, 100, 0);
 * $obj->save('output.jpg', 100);
 */
class Image
{

    protected $image;
    protected $width;
    protected $height;
    protected $imageResized;
    protected $file = null;
    protected $extensions = array( 'jpg', 'png', 'gif' );



    /**
     *
     * @access public
     * @param string $file path to file, if from upload form it would be $_FILES['html_index']['tmp_name']
     * @param array $extensions
     * @throws Exception
     * @return void
     */
    public function __construct( $file, array $extensions = null )
    {
        $this->file = $file;
        $this->extensions = !is_null( $extensions ) ? $extensions : $this->extensions;

        try
        {
            if ( !$this->isFile() )
            {
                throw new Exception( 'File does not exist' );
            }

            if ( !$this->isAllowedExtensions() )
            {
                throw new Exception( 'File extension is not allowed' );
            }

            /* Open up the file */
            $this->image = $this->openImage();

            /* Get width and height */
            $this->width = imagesx( $this->image );
            $this->height = imagesy( $this->image );
        }
        catch( Exception $e )
        {
            echo $e->getMessage();
        }
    }



    /**
     *
     * Resize the image
     * @access public
     * @param int $newWidth
     * @param int $newHeight
     * @param string $option exact|portrait|landscape|auto|crop
     * @return void
     */
    public function resize( $newWidth, $newHeight, $option = "auto" )
    {
        // *** Get optimal width and height - based on $option
        $optionArray = $this->getDimensions( $newWidth, $newHeight, $option );

        $optimalWidth = $optionArray[ 'optimalWidth' ];
        $optimalHeight = $optionArray[ 'optimalHeight' ];


        // *** Resample - create image canvas of x, y size
        $this->imageResized = imagecreatetruecolor( $optimalWidth, $optimalHeight );
        imagecopyresampled( $this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height );


        // *** if option is 'crop', then crop too
        if ( $option == 'crop' )
        {
            $this->crop( $optimalWidth, $optimalHeight, $newWidth, $newHeight );
        }
    }



    /**
     *
     * Save the image
     * @access public
     * @param type $savePath
     * @param type $imageQuality
     * @return image
     */
    public function save( $savePath, $imageQuality = 100 )
    {
        $extension = pathinfo( $savePath, PATHINFO_EXTENSION );
        switch ( $extension )
        {
            case 'jpg':
            case 'jpeg':
                if ( imagetypes() & IMG_JPG )
                {
                    imagejpeg( $this->imageResized, $savePath, $imageQuality );
                }
                break;

            case 'gif':
                if ( imagetypes() & IMG_GIF )
                {
                    imagegif( $this->imageResized, $savePath );
                }
                break;

            case 'png':
                /* Scale quality from 0-100 to 0-9 */
                $scaleQuality = round( ($imageQuality / 100) * 9 );

                /* Invert quality setting as 0 is best, not 9 */
                $invertScaleQuality = 9 - $scaleQuality;

                if ( imagetypes() & IMG_PNG )
                {
                    imagepng( $this->imageResized, $savePath, $invertScaleQuality );
                }
                break;

            default:
                break;
        }

        imagedestroy( $this->imageResized );
    }



    /**
     * Output image to browser
     * @access public
     * @param int $imageQuality
     * @return resource
     */
    public function output( $imageQuality = 100 )
    {
        $mime = getimagesize( $this->file );
        $mime = $mime[ 'mime' ];


        $extension = $this->getExtension();

        switch ( $extension )
        {
            case 'jpg':
            case 'jpeg':
                if ( imagetypes() & IMG_JPG )
                {
                    header( 'Content-Type:' . $mime );
                    imagejpeg( $this->imageResized, null, $imageQuality );
                }
                break;

            case 'gif':
                if ( imagetypes() & IMG_GIF )
                {
                    header( 'Content-Type:' . $mime );
                    imagegif( $this->imageResized );
                }
                break;

            case 'png':
                /* Scale quality from 0-100 to 0-9 */
                $scaleQuality = round( ($imageQuality / 100) * 9 );

                /* Invert quality setting as 0 is best, not 9 */
                $invertScaleQuality = 9 - $scaleQuality;

                if ( imagetypes() & IMG_PNG )
                {
                    header( 'Content-Type:' . $mime );
                    imagepng( $this->imageResized, null, $invertScaleQuality );
                }
                break;

            default:
                break;
        }
    }



    /**
     *
     * Get file name
     * @access public
     * @param type $file
     * @return string
     */
    public function getFileName()
    {
        return pathinfo( $this->file, PATHINFO_BASENAME );
    }



    /**
     *
     * Get file extension
     * @access public
     * @param string $file
     * @return string
     */
    public function getExtension()
    {
        $ext = getimagesize( $this->file );
        $ext = $ext[ 2 ];

        switch ( $ext )
        {
            case IMAGETYPE_GIF:
                return 'gif';
                break;

            case IMAGETYPE_JPEG:
                return 'jpg';
                break;

            case IMAGETYPE_PNG:
                return 'png';
                break;

            case IMAGETYPE_BMP :
                return 'bmp';
                break;

            case IMAGETYPE_ICO:
                return 'ico';
                break;

            case IMAGETYPE_PSD :
                return 'psd';
                break;

            default:
                return false;
        }
    }



    /**
     *
     * Create blank image
     * @access protected
     * @param type $this->file
     * @return image|boolean
     */
    protected function openImage()
    {
        /* Get extension */
        $extension = $this->getExtension();

        switch ( $extension )
        {
            case 'jpg':
                $img = @imagecreatefromjpeg( $this->file );
                break;

            case 'gif':
                $img = @imagecreatefromgif( $this->file );
                break;

            case 'png':
                $img = @imagecreatefrompng( $this->file );
                break;

            default:
                $img = false;
                break;
        }
        return $img;
    }



    /**
     *
     * Get the optimal size for width and height of an image based on option
     * @access protected
     * @param int $newWidth
     * @param int $newHeight
     * @param string $option
     * @return array
     */
    protected function getDimensions( $newWidth, $newHeight, $option )
    {

        switch ( $option )
        {
            case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
                break;
            case 'portrait':
                $optimalWidth = $this->getSizeByFixedHeight( $newHeight );
                $optimalHeight = $newHeight;
                break;
            case 'landscape':
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth( $newWidth );
                break;
            case 'auto':
                $optionArray = $this->getSizeByAuto( $newWidth, $newHeight );
                $optimalWidth = $optionArray[ 'optimalWidth' ];
                $optimalHeight = $optionArray[ 'optimalHeight' ];
                break;
            case 'crop':
                $optionArray = $this->getOptimalCrop( $newWidth, $newHeight );
                $optimalWidth = $optionArray[ 'optimalWidth' ];
                $optimalHeight = $optionArray[ 'optimalHeight' ];
                break;
        }
        return array( 'optimalWidth'  => $optimalWidth, 'optimalHeight' => $optimalHeight );
    }



    /**
     *
     * Get optimal width when height is fixed
     * @access protected
     * @param type $newHeight
     * @return int
     */
    protected function getSizeByFixedHeight( $newHeight )
    {
        $ratio = $this->width / $this->height;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }



    /**
     *
     * Get optimal height when width is fixed
     * @access protected
     * @param type $newWidth
     * @return int
     */
    protected function getSizeByFixedWidth( $newWidth )
    {
        $ratio = $this->height / $this->width;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }



    /**
     *
     * Get optimal width and height when resize is auto
     * @access protected
     * @param type $newWidth
     * @param type $newHeight
     * @return int
     */
    protected function getSizeByAuto( $newWidth, $newHeight )
    {
        if ( $this->height < $this->width )
        // *** Image to be resized is wider (landscape)
        {
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth( $newWidth );
        }
        elseif ( $this->height > $this->width )
        // *** Image to be resized is taller (portrait)
        {
            $optimalWidth = $this->getSizeByFixedHeight( $newHeight );
            $optimalHeight = $newHeight;
        }
        else
        // *** Image to be resizerd is a square
        {
            if ( $newHeight < $newWidth )
            {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth( $newWidth );
            }
            else if ( $newHeight > $newWidth )
            {
                $optimalWidth = $this->getSizeByFixedHeight( $newHeight );
                $optimalHeight = $newHeight;
            }
            else
            {
                // *** Sqaure being resized to a square
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
            }
        }

        return array( 'optimalWidth'  => $optimalWidth, 'optimalHeight' => $optimalHeight );
    }



    /**
     * Get the optimal width and height for cropping
     * @access protected
     * @param type $newWidth
     * @param type $newHeight
     * @return array
     */
    protected function getOptimalCrop( $newWidth, $newHeight )
    {

        $heightRatio = $this->height / $newHeight;
        $widthRatio = $this->width / $newWidth;

        if ( $heightRatio < $widthRatio )
        {
            $optimalRatio = $heightRatio;
        }
        else
        {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = $this->height / $optimalRatio;
        $optimalWidth = $this->width / $optimalRatio;

        return array( 'optimalWidth'  => $optimalWidth, 'optimalHeight' => $optimalHeight );
    }



    /**
     *
     * Crop the image
     * @access protected
     * @param int $optimalWidth
     * @param int $optimalHeight
     * @param int $newWidth
     * @param int $newHeight
     * @return void
     */
    protected function crop( $optimalWidth, $optimalHeight, $newWidth, $newHeight )
    {
        /* Find center - this will be used for the crop */
        $cropStartX = ( $optimalWidth / 2) - ( $newWidth / 2 );
        $cropStartY = ( $optimalHeight / 2) - ( $newHeight / 2 );

        $crop = $this->imageResized;
        /* Now crop from center to exact requested size */
        $this->imageResized = imagecreatetruecolor( $newWidth, $newHeight );
        imagecopyresampled( $this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight );
    }



    /**
     *
     * Check file existence
     * @access protected
     * @return boolean
     */
    protected function isFile()
    {
        return file_exists( $this->file );
    }



    /**
     *
     * Check is uploaded image is in allowed extension
     * @access protected
     * @return boolean
     */
    protected function isAllowedExtensions()
    {
        $fileExtension = $this->getExtension();

        if ( in_array( $fileExtension, $this->extensions ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }



}




?>
