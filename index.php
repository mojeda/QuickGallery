<?php
$gallery = $_GET['gallery'];
// Removes all forward slashes (/) from define album to prevent path traversal.
$gallery = str_replace(chr(47), '', $gallery);
//You can now disable multiple folders from showing up in the list.
$disable = array("cache","folder2","folder3");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php if(!isset($gallery)) { echo "Quick Gallery"; } else { echo "Quick Gallery - ".$gallery.""; } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
      html, body { background: #ebebeb; height: 100%; padding-top: 10px; } .active { background-color: #eee; border-radius: 3px; } h1 { color: #222; text-align: center; } .row-fluid { height: 100%; } .gallery img { background: #222; border-radius: 3px; padding: 10px; display: inline-block; margin: 10px; border: 1px #fff solid; }
    </style>
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/fancybox/2.1.5/jquery.fancybox.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/fancybox/2.1.5/jquery.fancybox.css" media="screen" />
    <script type="text/javascript">
      $(document).ready(function() {
        $("a[rel=gallery]").fancybox({
          'transitionIn'    : 'none',
          'transitionOut'   : 'none',
          'titlePosition'   : 'over',
          'titleFormat'   : function(title, currentArray, currentIndex, currentOpts) {
            return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
          }
        });
      });
    </script>
</head>

<body>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="col-md-2">
        <div class="well">
          <ul class="nav nav-list">
            <?php 
              $dirs = array_filter(glob('*'), 'is_dir');
                foreach ( $dirs as $key => $value ) {
                  if (in_array($value, $disable) === FALSE) {
                    //This is not set to work if you didn't have an nginx/apache2 rewrite rule for folders
                    //You can create a rewrite rule and modify the link accordingly below.
                      echo '<li><a href="index.php?gallery='.$value.'" '.(($value==$gallery)?'class="active"':"").'>'.$value.'</a>';
                  }
                }
            ?>
          </ul>
        </div>
        <p style="text-align: center;"><a href="https://github.com/mojeda/QuickGallery" target="_blank">Quick Gallery</a> by <a href="http://www.mojeda.com/" target="_blank">Michael Ojeda</a></p>
      </div>
      <div class="col-md-10 gallery">
        <?php
          $imgdir = $gallery . '/';
          $allowed_types = array('png','jpg','jpeg','gif');
          $dimg = opendir($imgdir);
          while($imgfile = readdir($dimg))
          {
            if( in_array(strtolower(substr($imgfile,-3)),$allowed_types) OR
              in_array(strtolower(substr($imgfile,-4)),$allowed_types) )
            {$a_img[] = $imgfile;}
          }
           $totimg = count($a_img);
           for($x=0; $x < $totimg; $x++){ echo "<a href='" . $imgdir . $a_img[$x] . "' rel='gallery'><img src='thumb.php?file=$imgdir".$a_img[$x]."' /></a>"; }
        ?>
      </div>
    </div>
  </div>
</body>
</html>
