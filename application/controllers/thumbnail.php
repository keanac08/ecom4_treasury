<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Thumbnail extends CI_Controller {
	/**
	 * This code is an improvement over Alex's code that can be found here -> http://stackoverflow.com/a/11376379
	 * 
	 * This funtion creates a thumbnail with size $thumbnail_width x $thumbnail_height.
	 * It supports JPG, PNG and GIF formats. The final thumbnail tries to keep the image proportion.
	 * 
	 * Warnings and/or notices will also be thrown if anything fails.
	 * 
	 * Example of usage:
	 * 
	 * <code>
	 * require_once 'create_thumbnail.php';
	 * 
	 * $success = createThumbnail(__DIR__.DIRECTORY_SEPARATOR.'image.jpg', __DIR__.DIRECTORY_SEPARATOR.'image_thumb.jpg', 60, 60, array(255,255,255)); // creates a thumbnail called image_thumb.jpg with 60x60 in size and with a white background
	 * 
	 * echo $success ? 'thumbnail was created' : 'something went wrong';
	 * </code>
	 * 
	 * @author Pedro Pinheiro (https://github.com/pedroppinheiro).
	 * @param string $filepath The image's complete path. Example: C:\xampp\htdocs\project\image.jpg
	 * @param string $thumbpath The path to create the thumbnail + name of the thumbnail. Example: C:\xampp\htdocs\project\image_thumbnail.jpg
	 * @param int $thumbnail_width Width of the thumbnail. Only integers allowed.
	 * @param int $thumbnail_height Height of the thumbnail. Only integers allowed.
	 * @param int[int] | 'transparent' An array containing the values of red, green, and blue to be used as the image's background color, or use the string 'transparent' to define the background as transparent (only applicable to png images). This parameter is optional, so if no value is provided, then the default background will be black.
	 * @return boolean Returns true if the thumbnail was created successfully, false otherwise.
	 */
	 
	public function index(){
		
		$files = glob('\\\ecommerce4\c$\wamp64\www\treasury\resources\images\emp_pics\*.{jpg,JPG}', GLOB_BRACE);
		foreach($files as $file){
		
		echo $this->createThumbnail(
				$file,
				'\\\ecommerce4\c$\wamp64\www\treasury\resources\images\emp_pics_thumb\\'.basename($file),
				90,
				90
			);
		}
	}
	
	private function createThumbnail($filepath, $thumbpath, $thumbnail_width, $thumbnail_height, $background=false) {
		list($original_width, $original_height, $original_type) = getimagesize($filepath);
		if ($original_width > $original_height) {
			$new_width = $thumbnail_width;
			$new_height = intval($original_height * $new_width / $original_width);
		} else {
			$new_height = $thumbnail_height;
			$new_width = intval($original_width * $new_height / $original_height);
		}
		$dest_x = intval(($thumbnail_width - $new_width) / 2);
		$dest_y = intval(($thumbnail_height - $new_height) / 2);
		if ($original_type === 1) {
			$imgt = "ImageGIF";
			$imgcreatefrom = "ImageCreateFromGIF";
		} else if ($original_type === 2) {
			$imgt = "ImageJPEG";
			$imgcreatefrom = "ImageCreateFromJPEG";
		} else if ($original_type === 3) {
			$imgt = "ImagePNG";
			$imgcreatefrom = "ImageCreateFromPNG";
		} else {
			return false;
		}
		$old_image = $imgcreatefrom($filepath);
		$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background
		// figuring out the color for the background
		if(is_array($background) && count($background) === 3) {
		  list($red, $green, $blue) = $background;
		  $color = imagecolorallocate($new_image, $red, $green, $blue);
		  imagefill($new_image, 0, 0, $color);
		// apply transparent background only if is a png image
		} else if($background === 'transparent' && $original_type === 3) {
		  imagesavealpha($new_image, TRUE);
		  $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
		  imagefill($new_image, 0, 0, $color);
		}
		imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
		$imgt($new_image, $thumbpath);
		return file_exists($thumbpath);
	}
}
