<?php
function imageLoder($filename){
$fp = @imagecreatefromjpeg ( $filename );
$pixelnum = 0;
if ($fp){
   $pixelnum = imagecolorat ( $fp, 10 ,10);
}
return $pixelnum;
}
?>
