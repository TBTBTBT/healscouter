<?php
function imageLoder($filename){
$fp = @imagecreatefromjpeg ( $filename );
$rgywb = [0,0,0,0,0];
if ($fp){
	 $width = imagesx($fp);
	  $height = imagesy($fp);
	  for($i = 0;$i<$width;$i++){
	  	for($j = 0;$j<$height;$j++){
	  		$pixelnum = imagecolorat ( $fp, $i ,$j);
	  		$r = ($pixelnum  >> 16) & 0xFF;
			$g = ($pixelnum  >> 8) & 0xFF;
	  		$rgywb[0] += $r;
			$rgywb[1] += $g;
			$rgywb[2] += ($r+ $g)/2;
			$rgywb[3] += ($r + $g + ($pixelnum & 0xFF)) / 3;

	  		//
	  	}
	  }
var_dump($rgywb);
	  for($i = 0;$i<4;$i++)$rgywb[$i] = floor($rgywb[$i] /($width * $height));
	  	$rgywb[4] = 255 - $rgywb[3];

}

return $rgywb;
}
?>
