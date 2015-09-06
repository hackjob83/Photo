<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

/**
 * Description of Photo
 * 
 * The purpose of the class is to take an image that has been / is being uploaded through a http upload
 * The various validation methods will still need to be observed and created as the class does not handle that
 * Largely due to the fact that certain restrictions may change from program to program
 * 
 * This class can take the uploaded image, move it and rename it.
 * It also can take the image and make a square crop of the image.
 * The class can also resize the image while keeping the original ratios
 * 
 * Plans to add merging functions to this as well for the rare case that we need to merge two images together for whatever reason
 * 
 * 
 * usage :
 * 
 * # create the object and pass it the image
 * $photo = new Photo($image);  
 * 
 * # set all the variables needed to process and save the image
 * $photo->set_orig_name($_FILES['file']['name']);
 * $photo->set_new_name(time() . '-' . md5($email));
 * $photo->set_orig_dir('uploaded_files/full_orig/');
 * $photo->set_cropped_dir('uploaded_files/');
 * 
 * # call the function that will move and save the origianl photo
 * $photo->save_orig();
 * 
 * # The various crop and resize options that are currently available in the class
 * $photo->square_crop("thumb");
 * $photo->square_crop("med");
 * $photo->square_crop("large");
 * $photo->new_size("thumb");
 * $photo->new_size("med");
 * $photo->new_size("large");
 *
 * 
 * @author hackjob83
 * Last edit : 2015-05-20
 */


/**
 * Private variables that will be used in the class
 */
class Photo {

    private $orig_name;
    private $temp_name;
    private $new_name;
    private $file_type;
    private $orientation;
    private $orig_x;
    private $orig_y;
    private $cropped_dir;
    private $orig_dir;
    private $thumb_size;
    private $med_size;
    private $large_size;
    private $quality;

    
    /**
     * Constructor function
     * Sets the values for a list of variabhles
     * Also grabs the image dimensions and orientation during the process
     * 
     * @param object $image
     */
    public function __construct($image) {
        $this->temp_name = $image;

        $image = getimagesize($this->temp_name);
        
        $this->orig_x                               = $image[0];
        $this->orig_y                               = $image[1];
        $this->file_type                            = strtolower(preg_replace('/^.*?\//', '', $image['mime']));
        $this->thumb_size                           = 200;
        $this->med_size                             = 300;
        $this->large_size                           = 450;
        $this->quality                              = 100;  //default

        if ($this->file_type == 'jpg' || $this->file_type == 'jpeg') {
            $exif = @read_exif_data($this->temp_name);

            if (isset($exif['Orientation'])) {
                $this->orientation                      = $exif['Orientation'];
            } else {
                $this->orientation                      = 1;
            }
        } else {
            $this->orientation                          = 1;
        }
    }

    /**
     * Set the original name for the image
     * 
     * @param string $name
     */
    public function set_orig_name($name) {
        $this->orig_name = $name;
    }

    /**
     * Returns the string that was set as the original name
     * 
     * @return string
     */
    public function get_orig_name() {
        return $this->orig_name;
    }

    /**
     * Set the new name for the image
     * 
     * @param string $name
     */
    public function set_new_name($name) {
        $this->new_name = $name;
    }

    /**
     * Return the string that was set as the new name
     * 
     * @return string
     */
    public function get_new_name() {
        return $this->new_name;
    }

    /**
     * Set the directory that the original image file will be saved to 
     * 
     * @param string $dir
     */
    public function set_orig_dir($dir) {
        $this->orig_dir = $dir;
    }

    /**
     * Return the string that was set as the origial directory
     * 
     * @return string
     */
    public function get_orig_dir() {
        return $this->orig_dir;
    }

    /**
     * Set the directory that the cropped or edited images files will be saved to 
     * 
     * @param string $dir
     */
    public function set_cropped_dir($dir) {
        $this->cropped_dir = $dir;
    }

    /**
     * Return the string that was set as the cropped dir
     * 
     * @return string
     */
    public function get_cropped_dir() {
        return $this->cropped_dir;
    }

    /**
     * Set the quality of the new images that are created.
     * Default is set to 100 in constructor. 
     * This function only works for JPEG files (0-100).
     * PNG files require a number (1-10).
     * 
     * @param int $quality
     */
    public function set_quality($quality) {
        $this->quality = $quality;
    }
    
    /**
     * Return the set quality for JPEG image files created from this class
     * 
     * @return int
     */
    public function get_quality() {
        return $this->quality;
    }

    /**
     * Returns the set file type for the image.
     * Will typically return the 'jpg', 'png', 'gif', etc.
     * 
     * @return string
     */
    public function get_file_type() {
        return $this->file_type;
    }

    /**
     * This function effectively moves and saves the image file.
     * All the data needed is passed through various other functions, so no variables need to be passed.
     * Will stop and echo "Problem moving photo" if there is a problem -- may want to take out, good for testing
     */
    public function save_orig() {
        if (!(@move_uploaded_file($this->temp_name, $this->orig_dir . $this->new_name . '.' . $this->file_type))) {
            echo 'Problem moving photo';
        }
    }

    /**
     * Function takes the variable size to determine the size of the new image.
     * Makes quare crops of the image, will trim whatever necessary from left/right or top/bottom
     * It will center the image before it begins to crop it.
     * The size variable is to be "thumb", "med", or "large" and corresponds to the int sizes by those names in the constructor
     * For the sake of unique and original photo names, the size int is appended as a prefix to the image name.
     * 
     * Can accept 'jpg', 'png', or 'gif' formats and can be modified to accept more
     * 
     * @param string $size
     */
    public function square_crop($size) {

        // Check for valid dimensions
        if ($this->orig_x <= 0 || $this->orig_y <= 0)
            return false;

        // Determine format from MIME-Type
        switch ($this->file_type) {
            case 'jpg':
            case 'jpeg':
                $image_data = imagecreatefromjpeg($this->orig_dir . $this->new_name . '.' . $this->file_type);
                break;
            case 'png':
                $image_data = imagecreatefrompng($this->orig_dir . $this->new_name . '.' . $this->file_type);
                break;
            case 'gif':
                $image_data = imagecreatefromgif($this->orig_dir . $this->new_name . '.' . $this->file_type);
                break;
            default:
                // Unsupported format
                return false;
        }

        // Verify import
        if ($image_data == false){
            return false;
        }

        // Calculate measurements
        if ($this->orig_x > $this->orig_y) {
            // For landscape images
            $x_offset = ($this->orig_x - $this->orig_y) / 2;
            $y_offset = 0;
            $square_size = $this->orig_x - ($x_offset * 2);
        } else {
            // For portrait and square images
            $x_offset = 0;
            $y_offset = ($this->orig_y - $this->orig_x) / 2;
            $square_size = $this->orig_y - ($y_offset * 2);
        }

        // Resize and crop
        switch ($size) {
            case 'thumb':
                $size = $this->thumb_size;
                break;
            case 'med':
                $size = $this->med_size;
                break;
            case 'large':
                $size = $this->large_size;
                break;
            default :
                return FALSE;
        }
        $canvas = imagecreatetruecolor($size, $size);
        if (imagecopyresampled(
                        $canvas, $image_data, 0, 0, $x_offset, $y_offset, $size, $size, $square_size, $square_size
        )) {

            //the orientation is pulled from the exif data when the photo is uploaded
            //fix orientation in mobile uploads before creating new image
            switch ($this->orientation) {
                case 3:
                    //rotate image 180 degrees left
                    $canvas = imagerotate($canvas, 180, 0);
                    break;
                case 6:
                    //rotate image 90 degrees to the right
                    $canvas = imagerotate($canvas, -90, 0);
                    break;
                case 8:
                    //rotate image 90 degrees to the left
                    $canvas = imagerotate($canvas, 90, 0);
                    break;
            }

            // Create thumbnail
            $dest_image = $this->cropped_dir . $size . '-' . $this->new_name . '.' . $this->file_type;
            switch (strtolower(preg_replace('/^.*\./', '', $dest_image))) {
                case 'jpg':
                case 'jpeg':
                    return imagejpeg($canvas, $dest_image, $this->quality);
                    break;
                case 'png':
                    return imagepng($canvas, $dest_image, 8);
                    break;
                case 'gif':
                    return imagegif($canvas, $dest_image);
                    break;
                default:
                    // Unsupported format
                    return false;
            }
        } else {
            return false;
            //echo "Nope";
        }
    }

    /**
     * Function that takes a size variable and creates new images scaled down to that size.
     * Will maintain the same aspect and ratios as the origial, with the largest side being equal to the variable passed.
     * The size variable is to be "thumb", "med", or "large" and corresponds to the int values set in constructor.
     * 
     * For the sake of unique and origianl names, the size as string is appended as a prefix to the image file name.
     * This will also assure that if quare cropped images need to be made from the same origianls in a program,
     *      they will remain separate and unique names, and can be differenciated between.
     * 
     * Can accept 'jpg', 'png', and 'gif' files and can be modified to accept others
     * 
     * @param string $size
     */
    public function new_size($size) {

        switch ($size) {
            case 'large':
                $max_dimension = $this->large_size;
                break;
            case 'med':
                $max_dimension = $this->med_size;
                break;
            case 'thumb':
                $max_dimension = $this->thumb_size;
        }

        // Check for valid dimensions
        if ($this->orig_x <= 0 || $this->orig_y <= 0)
            return false;

        //get the original width and height to determine the ratio
        $ratio = $this->orig_x / $this->orig_y;

        // Determine format from MIME-Type
        switch ($this->file_type) {
            case 'jpg':
            case 'jpeg':
                $origImage = imagecreatefromjpeg($this->orig_dir . $this->new_name . '.' . $this->file_type);
                break;
            case 'png':
                $origImage = imagecreatefrompng($this->orig_dir . $this->new_name . '.' . $this->file_type);
                break;
            case 'gif':
                $origImage = imagecreatefromgif($this->orig_dir . $this->new_name . '.' . $this->file_type);
                break;
            default:
                // Unsupported format
                return false;
        }
        


        $targetWidth = $targetHeight = min($max_dimension, max($this->orig_x, $this->orig_y));
        if ($ratio < 1) { // portrait
            $targetWidth = round($targetHeight * $ratio);
        } else { // landscape
            $targetHeight = round($targetWidth / $ratio);
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($targetImage, $origImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $this->orig_x, $this->orig_y);
        
        //the orientation is pulled from the exif data when the photo is uploaded
        //fix orientation in mobile uploads before creating new image
        switch ($this->orientation) {
            case 3:
                //rotate image 180 degrees left
                $targetImage = imagerotate($targetImage, 180, 0);
                break;
            case 6:
                //rotate image 90 degrees to the right
                $targetImage = imagerotate($targetImage, -90, 0);
                break;
            case 8:
                //rotate image 90 degrees to the left
                $targetImage = imagerotate($targetImage, 90, 0);
                break;
        }

        $dest = $this->cropped_dir . $size . '-' . $this->new_name . '.' . $this->file_type;
        
        //switch based on MIME type -- so that it does not try to make a jpg out of a png
        switch ($this->file_type) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($targetImage, $dest, $this->quality);
                imagedestroy($origImage);
                imagedestroy($targetImage);
                break;
            case 'png':
                imagepng($targetImage, $dest, 8);
                imagedestroy($origImage);
                imagedestroy($targetImage);
                break;
            case 'gif':
                imagegif($targetImage, $dest);
                imagedestroy($origImage);
                imagedestroy($targetImage);
                break;
            default:
                // Unsupported format
                return false;
        }
    }

}

?>
