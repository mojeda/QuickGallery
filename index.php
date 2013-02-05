<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quick Gallery</title>
<style>
  body { background: #222; padding: 0; margin: 0; }
	img { min-height: 100px; max-height: 100px; vertical-align: top; background: #ebebeb; border-radius: 3px; border: 1px #000000 solid; box-shadow: 0 0 10px #000; padding: 10px; margin-bottom: 15px; }
	ul.gallery { list-style: none; padding: 10px 0; text-align: center; }
	li.thumb { display: inline; margin: 0 10px; }
	.container { max-width: 90%; margin-right: auto; margin-left: auto; }
	a { color: #ebebeb; text-decoration: none; font-size: 10px; }
</style>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/fancybox/2.1.4/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/fancybox/2.1.4/jquery.fancybox.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel=gallery]").fancybox({
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'titlePosition' 	: 'over',
			'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
				return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
			}
		});
	});
</script>
</head>

<body>
<?php
$imgdir = 'images/';
$allowed_types = array('png','jpg','jpeg','gif');
$dimg = opendir($imgdir);
while($imgfile = readdir($dimg))
{
  if( in_array(strtolower(substr($imgfile,-3)),$allowed_types) OR
	  in_array(strtolower(substr($imgfile,-4)),$allowed_types) )
  {$a_img[] = $imgfile;}
}
echo "<ul class='gallery'>";
 $totimg = count($a_img);
 for($x=0; $x < $totimg; $x++){echo "<li class='thumb'><a href='" . $imgdir . $a_img[$x] . "' rel='gallery'><img src='" . $imgdir . $a_img[$x] . "' /></a></li>";}
echo "</ul>";
?>

<center><a href="http://github.com/mojeda/QuickGallery">Quick Gallery Script by Michael Ojeda</a></center>
</body>
</html>
