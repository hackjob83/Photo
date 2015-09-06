# Photo
Photo upload and cropping PHP class

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
 * @author jhackett <hackjob83@gmail.com>
 * Last edit : 2015-05-20
 */

