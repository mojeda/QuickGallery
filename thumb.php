<?php
/* 
	thumb.php v1.1
	______________________________________________________________________ 
	Creates a thumbnailed image based on info passed to it via $_GET. 
	
	Images are cached on the server, so server processing overhead is only 
	needed for the first time the script runs on a particular image.
	______________________________________________________________________
	Requires:
		GD Library
	______________________________________________________________________
	Copyright: 
		(C) 2003 Chris Tomlinson. christo@mightystuff.net
		http://mightystuff.net
		
		This library is free software; you can redistribute it and/or
		modify it under the terms of the GNU Lesser General Public
		License as published by the Free Software Foundation; either
		version 2.1 of the License, or (at your option) any later version.
		
		This library is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
		Lesser General Public License for more details.
	
		http://www.gnu.org/copyleft/lesser.txt
	______________________________________________________________________
	Configuration:
		set $thumb_size to be a default maximum width/height if not passed via get
		set $image_error to be an image to be used when there is a problem parsing the image
		set $site_config['path_thumbnail'] to be a write-permissable folder on your server relative to the DOCUMENT_ROOT for storing images so they cache on the server.
		set $quality to be a value 0-100 for the resulting thumbnail jpeg quality
	______________________________________________________________________	
	Usage:
		<img src="thumb.php?file=FILE&size=SIZE">
		
		Where:
		FILE	=	the file to retrieve
		SIZE	=	the maximum size of the thumbnail in pixels
	______________________________________________________________________
	Changes:
		0.1 - first release
		0.2 - converted cache thumbnail from png to jpeg
		0.3 - fixed error where files weren't being cached properly
		0.4 - allowed non local urls (if allow_url_fopen is on), quality and nocache switches
		0.5 - allowed maximum x and y settings (for scaling images to fit non square sizes)
		0.6 - allowed tagging of images (with the get query placing the text in the bottom left hand corner of the image)
		0.7 - fixed gd_info error for php<4.3
		0.8 - added gif support (for gd 2.0.28)
		0.9 - now supports native outputting of png, jpg and gif formats
		1.0 - doesn't fail if the cache file can't be created
		1.1 - removed a few more notices
*/
//script configuration
ini_set('memory_limit','64M');
$site_config['document_root'] = $_SERVER['DOCUMENT_ROOT'];
$thumb_size = 211; //all thumbnails are this maximum width or height if not specified via get
$site_config['absolute_uri']=str_replace('///','//',str_replace('thumb.php?'.$_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']));
$site_config['path_thumbnail']=$site_config['absolute_uri'].'cache/';	//where to cache thumbnails on the server, relative to the DOCUMENT_ROOT
$image_error=$site_config['document_root'].$site_config['absolute_uri'].'/images/icons/image_error.png';	// used if no image could be found, or a gif image is specified

$thumb_size_x = 0;
$thumb_size_y = 0;

# Define quality of image
if (@$_GET["quality"]<>0) {
	$quality	= $_GET["quality"];
} else {
	$quality	= 80;
}

# Define size of image (maximum width or height)- if specified via get.
if (@$_GET["size"]<>0) {
	$thumb_size=intval($_GET["size"]);
}
if (intval(@$_GET["sizex"])>0)
{
	$thumb_size_x=intval($_GET["sizex"]);
	if (intval(@$_GET["sizey"])>0) 
	{
		$thumb_size_y=intval($_GET["sizey"]);
	} else {
		$thumb_size_y=$thumb_size_x;
	}
}

if (file_exists($_GET['file']))
{
	$filename=$_GET['file'];
} else {
	$filename=str_replace('//','/',$site_config['document_root'].$site_config['absolute_uri'].'/'.$_GET["file"]);
}

# If calling an external image, remove document_root
if (substr_count($filename, "http://")>0)


{
	$filename=str_replace($site_config['document_root'].$site_config['absolute_uri'].'/','',$filename);
}

$filename=str_replace("\'","'",$filename);
$filename=rtrim($filename);
$filename=str_replace("//","/",$filename);
$fileextension=substr($filename, strrpos ($filename, ".") + 1);

$cache_file=str_replace('//','/',$site_config['document_root'].$site_config['path_thumbnail'].md5($filename.@$thumb_size.@$thumb_size_x.@$thumb_size_y.@$quality).'.'.$fileextension);

# remove cache thumbnail?
if (@$_GET['nocache']==1)
{
	if (file_exists($cache_file))
	{
		#remove the cached thumbnail
		unlink($cache_file);
	}
}

if ((file_exists($cache_file)) && (@filemtime($cache_file)>@filemtime($filename)))
{
	header('Content-type: image/'.$fileextension);
	header("Expires: Mon, 26 Jul 2030 05:00:00 GMT");    
	header('Content-Disposition: inline; filename='.str_replace('/','',md5($filename.$thumb_size.$thumb_size_x.$thumb_size_y.$quality).'.'.$fileextension));
	echo (join('', file( $cache_file )));
	exit; # no need to create thumbnail - it already exists in the cache
}

# determine php and gd versions
$ver=intval(str_replace(".","",phpversion()));
if ($ver>=430)
{
	$gd_version=@gd_info();
}

# define the right function for the right image types
if (!$image_type_arr = @getimagesize($filename))
{
	header('Content-type: image/png');
	if(@$_GET['noerror'])
	{
		exit;
	} else {	
		echo (join('', file( $site_config['document_root'].$image_error )));
		exit;
	}
} 
$image_type=$image_type_arr[2];

switch ($image_type)
{
	case 2: # JPG
		if (!$image = @imagecreatefromjpeg ($filename))
		{
			# not a valid jpeg file
			$image = imagecreatefrompng ($image_error);
			$file_type="png";
			if (file_exists($cache_file))
			{
				# remove the cached thumbnail
				unlink($cache_file);
			}
		} 
		break;

	case 3: # PNG
		if (!$image = @imagecreatefrompng ($filename))
		{
			# not a valid png file
			$image = imagecreatefrompng ($image_error);
			$file_type="png";			
			if (file_exists($cache_file))
			{
				# remove the cached thumbnail
				unlink($cache_file);
			}
		}			 
		break;

	case 1: # GIF 
		if (!$image = @imagecreatefromgif ($filename))
		{
			# not a valid gif file
			$image = imagecreatefrompng ($image_error);
			$file_type="png";			
			if (file_exists($cache_file))
			{
				# remove the cached thumbnail
				unlink($cache_file);
			}
		}			 
		break;
	default:
		$image = imagecreatefrompng($image_error); 
		break;

}

# define size of original image	
$image_width = imagesx($image);
$image_height = imagesy($image);

# define size of the thumbnail	
if (@$thumb_size_x>0)
{
	# define images x AND y
	$thumb_width = $thumb_size_x;
	$factor = $image_width/$thumb_size_x;
	$thumb_height = intval($image_height / $factor); 
	if ($thumb_height>$thumb_size_y)
	{
		$thumb_height = $thumb_size_y; 
		$factor = $image_height/$thumb_size_y;
		$thumb_width = intval($image_width / $factor); 
	}		
} else {
	# define images x OR y
	$thumb_width = $thumb_size; 
	$factor = $image_width/$thumb_size;
	$thumb_height = intval($image_height / $factor); 
	if ($thumb_height>$thumb_size)
	{
		$thumb_height = $thumb_size; 
		$factor = $image_height/$thumb_size;
		$thumb_width = intval($image_width / $factor); 
	}
}

# create the thumbnail
if ($image_width < 4000)	//no point in resampling images larger than 4000 pixels wide - too much server processing overhead - a resize is more economical
{
	if (substr_count(strtolower($gd_version['GD Version']), "2.")>0)
	{
		//GD 2.0 
		$thumbnail = ImageCreateTrueColor($thumb_width, $thumb_height);
		imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
	} else {
		//GD 1.0 
		$thumbnail = imagecreate($thumb_width, $thumb_height);
		imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);			
	}	
} else {
	if (substr_count(strtolower($gd_version['GD Version']), "2.")>0)
	{
		# GD 2.0 

		$thumbnail = ImageCreateTrueColor($thumb_width, $thumb_height);
		imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
	} else {
		# GD 1.0 
		$thumbnail = imagecreate($thumb_width, $thumb_height);
		imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
	}
}

# insert string
if (@$_GET['tag']<>"")
{
	$font=1;
	$string= $_GET['tag'];
	$white = imagecolorallocate ($thumbnail, 255, 255, 255);
	$black = imagecolorallocate ($thumbnail, 0, 0, 0);
	imagestring ($thumbnail, $font, 3, $thumb_height-9, $string, $black);
	imagestring ($thumbnail, $font, 2, $thumb_height-10, $string, $white);
}

switch ($image_type)
{
	case 2:	# JPG
		header('Content-type: image/jpeg');
		header('Content-Disposition: inline; filename='.str_replace('/','',md5($filename.$thumb_size.$thumb_size_x.$thumb_size_y.$quality).'.jpeg'));
		@imagejpeg($thumbnail,$cache_file);
		imagejpeg($thumbnail);

		break;
	case 3: # PNG
		header('Content-type: image/png');
		header('Content-Disposition: inline; filename='.str_replace('/','',md5($filename.$thumb_size.$thumb_size_x.$thumb_size_y.$quality).'.png'));
		@imagepng($thumbnail,$cache_file);
		imagepng($thumbnail); 
		break;

	case 1:	# GIF 
		if (function_exists('imagegif'))
		{
			header('Content-type: image/gif');
			header('Content-Disposition: inline; filename='.str_replace('/','',md5($filename.$thumb_size.$thumb_size_x.$thumb_size_y.$quality).'.gif'));
			@imagegif($thumbnail,$cache_file);
			imagegif($thumbnail);  
		} else {
			header('Content-type: image/jpeg');
			header('Content-Disposition: inline; filename='.str_replace('/','',md5($filename.$thumb_size.$thumb_size_x.$thumb_size_y.$quality).'.jpg'));
			@imagejpeg($thumbnail,$cache_file);
			imagejpeg($thumbnail); 
		}
		break;
}

//clear memory
imagedestroy ($image);
imagedestroy ($thumbnail);

?>