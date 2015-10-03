<?
class cl_images
{
	public function resizeimg($filename, $smallimage, $w, $h) 
	{ 
		$ratio		= $w/$h;
		$size_img	= getimagesize($filename); 
		if (($size_img[0] <= $w) && ($size_img[1] <= $h))
		{
			copy($filename, $smallimage);
			return true; 
		}
		$src_ratio	= $size_img[0] / $size_img[1]; 
		
		if ($ratio < $src_ratio) 
		{
			$h = $w / $src_ratio; 
		}
		else 
		{
			$w = $h * $src_ratio; 
		}
		$dest_img					= imagecreatetruecolor($w, $h);
		$white						= imagecolorallocate($dest_img, 255, 255, 255);
		if ($size_img[2]==2)		$src_img = imagecreatefromjpeg($filename);
		else if ($size_img[2]==1)	$src_img = imagecreatefromgif($filename);
		else if ($size_img[2]==3) 	$src_img = imagecreatefrompng($filename);
		
		imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);
		if ($size_img[2]==2)		 imagejpeg($dest_img, $smallimage);
		else if ($size_img[2]==1)	imagegif($dest_img, $smallimage);
		else if ($size_img[2]==3)	imagepng($dest_img, $smallimage);
		imagedestroy($dest_img);
		imagedestroy($src_img);
		return true;
	}
	
	public function resizeimg_plus($filename, $smallimage, $w, $h)
	{ 
		$ratio		= $w/$h; 
		$size_img	= getimagesize($filename);
		
		if (($size_img[0] <= $w) && ($size_img[1] <= $h))
		{
			copy($filename, $smallimage);
			return true; 
		}
		
		$w_save	= $w;
		$h_save	= $h;
		
		$src_ratio	= $size_img[0] / $size_img[1]; 
		
		if ($ratio < $src_ratio) 
		{ 
			$h = $w / $src_ratio;
			if ($h < $h_save)
			{
				$w	+=	$h_save - $h;
				$h	=	$h_save;
			}
		} 
		else 
		{ 
			$w = $h * $src_ratio;
			if ($w < $w_save)
			{
				$h	+=	$w_save - $w;
				$w	=	$w_save;
			}
		}
		
		$dest_img	= imagecreatetruecolor($w, $h);   
		$white		= imagecolorallocate($dest_img, 255, 255, 255);        
		if ($size_img[2]		== 2) $src_img = imagecreatefromjpeg($filename);
		else if ($size_img[2]	== 1) $src_img = imagecreatefromgif($filename);
		else if ($size_img[2]	== 3) $src_img = imagecreatefrompng($filename); 
		
		imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);
		
		if ($size_img[2]		== 2) imagejpeg($dest_img, $smallimage);
		else if ($size_img[2]	== 1) imagegif($dest_img, $smallimage);
		else if ($size_img[2]	== 3) imagepng($dest_img, $smallimage); 
		
		imagedestroy($dest_img); 
		imagedestroy($src_img); 
		return true;          
	}	
	
	public function dropimg($filename, $smallimage, $w, $h)
	{
		$size_img	= getimagesize($filename); 
		
		if (($size_img[0] < $w) && ($size_img[1] < $h))
		{
			copy($filename, $smallimage);
			return true; 
		}
		
		$w_save			= $size_img[0];
		$h_save			= $size_img[1];
		
		if ($w_save > $w)
			$l = round(($w_save - $w) / 2);
		else
			$l = 0;
		if ($h_save > $h)
			$t = round(($h_save - $h) / 2);
		else
			$t = 0;
		
		$dest_img	= imagecreatetruecolor($w, $h);   
		$white		= imagecolorallocate($dest_img, 255, 255, 255);
		if ($size_img[2]		== 2) $src_img = imagecreatefromjpeg($filename);
		else if ($size_img[2]	== 1) $src_img = imagecreatefromgif($filename);
		else if ($size_img[2]	== 3) $src_img = imagecreatefrompng($filename); 
		imagecopyresampled($dest_img, $src_img, 0, 0, $l, $t, $w, $h, $w, $h);
		
		if ($size_img[2]		== 2) imagejpeg($dest_img, $smallimage);
		else if ($size_img[2]	== 1) imagegif($dest_img, $smallimage);
		else if ($size_img[2]	== 3) imagepng($dest_img, $smallimage); 
		
		imagedestroy($dest_img); 
		imagedestroy($src_img); 
		return true;          
	}
	
	public function set_size($filename, $smallimage, $w, $h)
	{ 
		$ratio		= $w/$h; 
		$size_img	= getimagesize($filename);
		
		if (($size_img[0] <= $w) && ($size_img[1] <= $h))
		{
			copy($filename, $smallimage);
			return true; 
		}
		
		$w_save	= $w;
		$h_save	= $h;
		
		$src_ratio	= $size_img[0] / $size_img[1]; 
		
		if ($ratio < $src_ratio) 
		{ 
			$h = $w / $src_ratio;
			if ($h < $h_save)
			{
				$w	+=	$h_save - $h;
				$h	=	$h_save;
			}
		} 
		else 
		{ 
			$w = $h * $src_ratio;
			if ($w < $w_save)
			{
				$h	+=	$w_save - $w;
				$w	=	$w_save;
			}
		}
		
		$dest_img	= imagecreatetruecolor($w, $h);
		$white		= imagecolorallocate($dest_img, 255, 255, 255);
		if ($size_img[2]		== 2) $src_img = imagecreatefromjpeg($filename);
		else if ($size_img[2]	== 1) $src_img = imagecreatefromgif($filename);
		else if ($size_img[2]	== 3) $src_img = imagecreatefrompng($filename);
		
		imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);
		
		if ($w > $w_save)
			$l = round(($w - $w_save) / 2);
		else
			$l = 0;
		if ($h > $h_save)
			$t = round(($h - $h_save) / 2);
		else
			$t = 0;
		
		$dest_img_2	= imagecreatetruecolor($w_save, $h_save);
		$white		= imagecolorallocate($dest_img_2, 255, 255, 255);
		
		imagecopyresampled($dest_img_2, $dest_img, 0, 0, $l, $t, $w_save, $h_save, $w_save, $h_save);
		
		if ($size_img[2]		== 2) imagejpeg($dest_img_2, $smallimage);
		else if ($size_img[2]	== 1) imagegif($dest_img_2, $smallimage);
		else if ($size_img[2]	== 3) imagepng($dest_img_2, $smallimage); 
		
		imagedestroy($dest_img); 
		imagedestroy($dest_img_2); 
		imagedestroy($src_img); 
		
		return true;
	}	
	
	public function get_image_size($url_small_img, $url_norm_img, $file_name)
	{
		$data_info = array();
	
		if (file_exists($url_norm_img . $file_name))
		{
			$image_size					= getimagesize($url_small_img . $file_name); 
			$data_info['small_width']	= $image_size[0];
			$data_info['small_height']	= $image_size[1];
		}
		
		if (file_exists($url_small_img . $file_name))
		{
			$image_size					= getimagesize($url_norm_img . $file_name); 
			$data_info['norm_width']	= $image_size[0];
			$data_info['norm_height']	= $image_size[1];
		}
		
		return $data_info;
	}
	
	public function save_image($url_norm_img, $url_small_img, $url_drop_img, $norm_w, $norm_h, $small_w, $small_h, $drop_w, $drop_h, $input_type_file = 'image')
	{
		$expansion		= strrchr($_FILES[$input_type_file]['name'], ".");
	    $image			= date("YmdHis", time()) . substr($input_type_file, 0, 2) . $expansion;
	    //$image		= date("YmdHis", time()) . substr($input_type_file, 0, 2) . '.jpg');
		
		$init_img		= $_FILES[$input_type_file]['tmp_name'];
		$norm_img		= $url_norm_img . $image;
		$small_img		= $url_small_img . $image;
		$drop_img		= $url_drop_img . $image;
	 	
		if ($expansion != '.swf')
		{
			$this->resizeimg($init_img, $norm_img, $norm_w, $norm_h);
			$this->resizeimg($norm_img, $small_img, $small_w, $small_h);
			$this->set_size($norm_img, $drop_img, $drop_w, $drop_h); 
		}
		else
		{
			move_uploaded_file($init_img, $norm_img);
		
		}
		
		return $image;
	}
	
	public function delete_image($url_norm_img, $url_small_img, $url_drop_img, $file_name)
	{
		if (file_exists($url_norm_img . $file_name))
			unlink ($url_norm_img . $file_name);
		if (file_exists($url_small_img . $file_name))
			unlink ($url_small_img . $file_name);
		if (!empty($url_drop_img) && file_exists($url_drop_img . $file_name))
			unlink ($url_drop_img . $file_name);
		
		return true;
	}
	
	public function get_copy($url_norm_img, $url_small_img, $url_drop_img, $file_name, $type_img = 'image')
	{
		$expansion		= strrchr($file_name, ".");
	    $image			= date("YmdHis", time()) . substr($type_img, 0, 2) . $expansion;
		
		if (copy($url_norm_img . $file_name, $url_norm_img . $image)
				&& copy($url_small_img . $file_name, $url_small_img . $image)
				&& copy($url_drop_img . $file_name, $url_drop_img . $image))
			return $image;
		
		return false;
	}
}

?>