<?php
if(!empty($_GET['url'])){
  $image_file = $_GET['url'];
}
$thumb_width = 200;
if(!empty($_GET['width'])){
  $thumb_width = $_GET['width'];
}

list($original_width, $original_height) = getimagesize($image_file);
$proportion = $original_width/$original_height;
$thumb_height = $thumb_width / $proportion;
if($proportion < 1){
  $thumb_height = $thumb_width;
  $thumb_width = $thumb_width * $proportion;
}

$file_type = strtolower(end(explode('.', $image_file)));

$original_img;
$thumb_img;

if ($file_type === "jpg") {
  $original_img = imagecreatefromjpeg($image_file);
  $thumb_img = imagecreatetruecolor($thumb_width, $thumb_height);
} elseif ($file_type === "png") {
  $original_img = imagecreatefrompng($image_file);
  $thumb_img = imagecreatetruecolor($thumb_width, $thumb_height);

  //透過解除
  imagealphablending($thumb_img, false);
  imagesavealpha($thumb_img, true);
} elseif ($file_type === "bmp") {
  $original_img = imagecreatefrombmp($image_file);
  $thumb_img = imagecreatetruecolor($thumb_width, $thumb_height);
}

imagecopyresized($thumb_img, $original_img, 0, 0, 0, 0,$thumb_width, $thumb_height, $original_width, $original_height);

if ($file_type === "jpg") {
  imagejpeg($thumb_img);
} elseif ($file_type === "png") {
  imagepng($thumb_img);
} elseif ($file_type === "bmp") {
  imagebmp($thumb_img);
}

imagedestroy($thumb_img);
imagedestroy($original_img);

?>
