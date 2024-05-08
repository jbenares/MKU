<?php
	class upload {
		function upload_img($pic_size, $pic_type, $pic_tmp, $pic_name, $folder, $current_filename) {
			$dir="My_Uploads/".$folder; //Your upload directory
			//return $dir;
			//echo $pic_type;		  		  		  			
			
			/*	Upload Class customized by: Michael Francis C. Catague, ECE, MIT	*/				
	  		if ($pic_size > 0) {	  		  		
	    	    $filename = basename($pic_name);				
			    $target="$dir/".$filename;
			  
		  	    //echo $target . "<br>";
		  	    //echo $pic_id;
		  	    //print_r($pic); 		  		  
		  	  		
  		        if(!copy($pic_tmp,$target))
				{
		          die("Error: A problem occured while uploading $filename. Aborting...<p>");	 

				}
		        else {
			      if(!empty($current_filename)) {
				    if(file_exists($dir.'/'.$current_filename)) 
				      unlink($dir.'/'.$current_filename);
					  echo "test";
		          }
		          
		          //return $filename;      
				  return $dir.'/'.$filename;
	        	} 		  
		  	}
		  	else return $current_filename;
  		}
		
		function imageResize($width, $height, $target) {
			
			if ($width > $height) {
			$percentage = ($target / $width);
			} else {
			$percentage = ($target / $height);
			}
			
			//gets the new value and applies the percentage, then rounds the value
			$width = round($width * $percentage);
			$height = round($height * $percentage);
			
			return "width=\"$width\" height=\"$height\"";
		
		} 
	}
?>
