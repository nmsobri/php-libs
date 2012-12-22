<?php

# ========================================================================#
#
#  Author   : Slier
#  Version  : 1.0
#  Date     : 17-Jan-10
#  Purpose  :  Resizes and saves image
#  Requires : Requires PHP5, GD library.
#  Example  :
#  include('class.image.php');
#  $obj = new Image('input.jpg', array('.jpg','.png'));
#  $obj->resize(150, 100, 0);
#  $obj->save('output.jpg', 100);
#
# ========================================================================#

Class Image
{
    /* Class variables */

    protected $image;
    protected $width;
    protected $height;
    protected $imageResized;
    protected $extensions = array( '.jpg', '.jpeg', '.png', '.gif' );



    /**
     *
     * @param string $file image file name
     * @param array $extensions allowable extension
     * @throws Exception
     */
    public function __construct( $file, array $extensions = null )
    {
        $this->extensions = !is_null( $extensions ) ? $extensions : $this->extensions;

        try
        {
            if ( !$this->isFile( $file ) )
            {
                throw new Exception( 'File does not exist' );
            }

            if ( !$this->isAllowedExtensions( $file ) )
            {
                throw new Exception( 'File extension is not allowed' );
            }

            /* Open up the file */
            $this->image = $this->openImage( $file );

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
     * @param int $newWidth new width of an image
     * @param int $newHeight new height of an image
     * @param string $option how to resize, Posibble value [exact,portrait,landscape,auto,crop]
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
     * @param type $savePath
     * @param type $imageQuality
     */
    public function save( $savePath, $imageQuality = "100" )
    {
        /* Get extension */
        $extension = strrchr( $savePath, '.' );
        $extension = strtolower( $extension );

        switch ( $extension )
        {
            case '.jpg':
            case '.jpeg':
                if ( imagetypes() & IMG_JPG )
                {
                    imagejpeg( $this->imageResized, $savePath, $imageQuality );
                }
                break;

            case '.gif':
                if ( imagetypes() & IMG_GIF )
                {
                    imagegif( $this->imageResized, $savePath );
                }
                break;

            case '.png':
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
     *
     * Create blank image
     * @param type $fileName
     * @return boolean
     */
    protected function openImage( $fileName )
    {
        /* Get extension */
        $extension = $this->getExtension( $fileName );

        switch ( $extension )
        {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg( $fileName );
                break;

            case '.gif':
                $img = @imagecreatefromgif( $fileName );
                break;

            case '.png':
                $img = @imagecreatefrompng( $fileName );
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
     * @param type $newWidth
     * @param type $newHeight
     * @return type
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
     * @param int $optimalWidth
     * @param int $optimalHeight
     * @param int $newWidth
     * @param int $newHeight
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
     * @return boolean
     */
    protected function isFile( $file )
    {
        return file_exists( $file );
    }



    /**
     *
     * Get file extension
     * @param string $file
     * @return string
     */
    protected function getExtension( $file )
    {
        return strtolower( strrchr( $file, '.' ) );
    }



    /**
     *
     * Get file name
     * @param type $file
     * @return type
     */
    protected function getFileName( $file )
    {
        $file = str_replace( '\\', '/', $file );
        return substr( $file, strrpos( $file, '/' ) + 1 );
    }



    /**
     *
     * Check is uploaded image is in allowed extension
     * @param type $file
     * @return boolean
     */
    protected function isAllowedExtensions( $file )
    {
        $fileName = $this->getFileName( $file );
        $fileExtension = $this->getExtension( $fileName );

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
