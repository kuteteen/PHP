<?php
/**
 * File: IMGModel.php
 * Author: Simon Jarvis
 * Modified by: Miguel Fermín, Mansur ATİK
 * Based in: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 */

class IMGModel {

	public $image;
	public $image_type;
	public $options = array(
		"font"   => "",
		"left"   => 0,
		"top"    => 0,
		"align"  => "center",
		"valign" => "top",
		"size"   => 16,
		"color"  => array(255,255,255),
		"rotate" => 0,

		"draw_width"	 => 0,
		"draw_height"	=> 0,
		"draw_left"	  => 0,
		"draw_top"	   => 0,
		"draw_fit"	   => "normal",
		"draw_translateX" => 0,
		"draw_translateY" => 0,

		"fill_from_x"  => 0,
		"fill_from_y"  => 0,
		"fill_to_x"    => 0,
		"fill_to_y"    => 0,
		"fill_color"   => array(255,0,0)
	);

	public function __construct($filename = null){
		if (!empty($filename)) {
			$this->load($filename);
		}
	}
	public function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if ($this->image_type == IMAGETYPE_JPEG) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif ($this->image_type == IMAGETYPE_GIF) {
			$this->image = imagecreatefromgif($filename);
		} elseif ($this->image_type == IMAGETYPE_PNG) {
			$this->image = imagecreatefrompng($filename);
		} else {
			throw new Exception("The file you're trying to open is not supported");
		}
		return $this;
	}
	public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
		if ($image_type == IMAGETYPE_JPEG) {
			imagejpeg($this->image,$filename,$compression);
		} elseif ($image_type == IMAGETYPE_GIF) {
			imagegif($this->image,$filename);
		} elseif ($image_type == IMAGETYPE_PNG) {
			imagepng($this->image,$filename);
		}
		if ($permissions != null) {
			chmod($filename,$permissions);
		}
	}
	public function output($image_type=IMAGETYPE_JPEG, $quality = 80) {
		if ($image_type == IMAGETYPE_JPEG) {
			header("Content-type: image/jpeg");
			imagejpeg($this->image, null, $quality);
		} elseif ($image_type == IMAGETYPE_GIF) {
			header("Content-type: image/gif");
			imagegif($this->image);
		} elseif ($image_type == IMAGETYPE_PNG) {
			header("Content-type: image/png");
			imagepng($this->image);
		}
	}
	public function getWidth() {
		return imagesx($this->image);
	}
	public function getHeight() {
		return imagesy($this->image);
	}
	public function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = round($this->getWidth() * $ratio);
		$this->resize($width,$height);
	}
	public function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = round($this->getHeight() * $ratio);
		$this->resize($width,$height);
	}
	public function square($size) {
		$new_image = imagecreatetruecolor($size, $size);
		if ($this->getWidth() > $this->getHeight()) {
			$this->resizeToHeight($size);

			imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
		} else {
			$this->resizeToWidth($size);

			imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
		}
		$this->image = $new_image;
	}
	public function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getHeight() * $scale/100;
		$this->resize($width,$height);
	}
	public function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);

		imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);

		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}
	public function cut($x, $y, $width, $height) {
    		$new_image = imagecreatetruecolor($width, $height);
		imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
		imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);
		$this->image = $new_image;
	}
	public function maxarea($width, $height = null)	{
		$height = $height ? $height : $width;

		if ($this->getWidth() > $width) {
			$this->resizeToWidth($width);
		}
		if ($this->getHeight() > $height) {
			$this->resizeToheight($height);
		}
	}
	public function minarea($width, $height = null)	{
		$height = $height ? $height : $width;

		if ($this->getWidth() < $width) {
			$this->resizeToWidth($width);
		}
		if ($this->getHeight() < $height) {
			$this->resizeToheight($height);
		}
	}
	public function cutFromCenter($width, $height) {

		if ($width < $this->getWidth() && $width > $height) {
			$this->resizeToWidth($width);
		}
		if ($height < $this->getHeight() && $width < $height) {
			$this->resizeToHeight($height);
		}

		$x = ($this->getWidth() / 2) - ($width / 2);
		$y = ($this->getHeight() / 2) - ($height / 2);

		return $this->cut($x, $y, $width, $height);
	}
	public function maxareafill($width, $height, $red = 0, $green = 0, $blue = 0) {
	    $this->maxarea($width, $height);
	    $new_image = imagecreatetruecolor($width, $height);
	    $color_fill = imagecolorallocate($new_image, $red, $green, $blue);
	    imagefill($new_image, 0, 0, $color_fill);
	    imagecopyresampled(	$new_image,
	    					$this->image,
	    					floor(($width - $this->getWidth())/2),
	    					floor(($height-$this->getHeight())/2),
	    					0, 0,
	    					$this->getWidth(),
	    					$this->getHeight(),
	    					$this->getWidth(),
	    					$this->getHeight()
	    				);
	    $this->image = $new_image;
	}




	/*
		@Author : Mansur ATIK
		@Description : Create new image area and draw on
	*/
	public function Create($width,$height,$type = "jpg"){
		$this->image = imagecreatetruecolor($width, $height);
		if($type == "png"){
			imagealphablending($this->image,false);
			$col=imagecolorallocatealpha($this->image,255,255,255,127);
			imagefilledrectangle($this->image,0,0,$width, $height,$col);
			imagealphablending($this->image,true);
			imagesavealpha($this->image,true);
		}
	}


	public function Fill_From($x,$y){
		$w = $this->getWidth();
		$h = $this->getHeight();
		$this->options["fill_from_x"] = $x;
		$this->options["fill_from_y"] = $y;
		if(!is_numeric($x)){
			$i = 1;
			if(strstr($x,"-")){
				$i = -1;
				$x = str_replace("-","",$x);
			}
			$this->options["fill_from_x"] = (strstr($x,"%") ?  (($w / 100 * str_replace("%","",$x)) * $i)  : (str_replace("px","",$x) * $i)  );
		}
		if(!is_numeric($y)){
			$i = 1;
			if(strstr($y,"-")){
				$i = -1;
				$y = str_replace("-","",$y);
			}
			$this->options["fill_from_y"] = (strstr($y,"%") ?  (($h / 100 * str_replace("%","",$y)) * $i)  : (str_replace("px","",$y) * $i)  );
		}
		return $this;
	}
	public function Fill_Size($w,$h){
		$x = $this->options["fill_from_x"];
		$y = $this->options["fill_from_y"];
		if(is_numeric($w)){
			$x += $w;
		}else{
			$x = (strstr($w,"%") ?  ($x + ($this->getWidth() / 100 * str_replace("%","",$w)))  : ($x + str_replace("px","",$w))  );
		}
		if(is_numeric($h)){
			$y += $h;
		}else{
			$y = (strstr($h,"%") ?  ($y + ($this->getHeight() / 100 * str_replace("%","",$h)))  : ($y + str_replace("px","",$h))  );
		}
		$this->options["fill_to_x"] = $x;
		$this->options["fill_to_y"] = $y;
		return $this;
	}
	public function Fill_Color($e){
		$this->options["fill_color"] = $e;
		return $this;
	}
	public function Fill_Rect(){
		$c = $this->options["fill_color"];
		$color = (count($c) == 3 ? imagecolorallocate($this->image, $c[0], $c[1], $c[2]) : imagecolorallocatealpha($this->image, $c[0], $c[1], $c[2], $c[3])) ;
		imagefilledrectangle(
			$this->image,
			$this->options["fill_from_x"],
			$this->options["fill_from_y"],
			$this->options["fill_to_x"],
			$this->options["fill_to_y"],
			$color
		);
		return $this;
	}



	public function Text_Font($font){
		if(file_exists($font)) $this->options["font"] = $font;
		return $this;
	}
	public function Text_Left($e){
		$this->options["left"] = $e;
		return $this;
	}
	public function Text_Top($e){
		$this->options["top"] = $e;
		return $this;
	}
	public function Text_Align($e){
		$this->options["align"] = $e;
		return $this;
	}
	public function Text_Valign($e){
		$this->options["valign"] = $e;
		return $this;
	}
	public function Text_Size($e){
		$this->options["size"] = $e;
		return $this;
	}
	public function Text_Color($e){
		$this->options["color"] = $e;
		return $this;
	}
	public function Text_Rotate($e){
		$this->options["rotate"] = $e;
		return $this;
	}
	public function Text_Write($text){
		$c = $this->options["color"];
		$color = (count($c) == 3 ? imagecolorallocate($this->image, $c[0], $c[1], $c[2]) : imagecolorallocatealpha($this->image, $c[0], $c[1], $c[2], $c[3])) ;
		$x   = $this->options["left"];
		$y   = $this->options["top"];
		if($this->options["font"] != ""){
			$tb = imagettfbbox($this->options["size"],$this->options["rota"],$this->options["font"],$text);
			$tw  = $tb[2]-$tb[0];
			$th  = ($tb[3] < 0 ? $tb[3]-$tb[5] : $tb[3]+$tb[5]);
			if($this->options["align"] == "center"){
				$x = $x - ($tw/2);
			}
			if($this->options["align"] == "right"){
				$x = $x - $tw ;
			}
			if($this->options["valign"] == "bottom"){
				$y = $y - $this->options["size"];
			}
			if($this->options["valign"] == "middle"){
				$y = $y + ($th/2);
			}
			if($this->options["valign"] == "top"){
				$y = $y + ($this->options["size"]);
			}
			imagettftext($this->image, $this->options["size"], $this->options["rota"], $x, $y, $color, $this->options["font"], $text);
		}else{
			imagestring($this->image,5,$x,$y,$text,$color);
		}
		return $this;
	}



	public function Draw_Width($e){
		$this->options["draw_width"] = $e;
		return $this;
	}
	public function Draw_Height($e){
		$this->options["draw_height"] = $e;
		return $this;
	}
	public function Draw_Left($e){
		$this->options["draw_left"] = $e;
		return $this;
	}
	public function Draw_Top($e){
		$this->options["draw_top"] = $e;
		return $this;
	}
	public function Draw_Fit($e){
		$this->options["draw_fit"] = $e;
		return $this;
	}
	public function Draw_TranslateX($e){
		$this->options["draw_translateX"] = $e;
		return $this;
	}
	public function Draw_TranslateY($e){
		$this->options["draw_translateY"] = $e;
		return $this;
	}
	public function Draw_Image($e){
		$img = new IMGModel($e);
		$w = $img->getWidth();
		$h = $img->getHeight();
		if($this->options["draw_fit"] == "normal"){
			if($this->options["draw_width"] != 0 || $this->options["draw_height"] != 0){
				$img->resize( ($this->options["draw_width"] == 0 ? $w :$this->options["draw_width"] ) , ($this->options["draw_height"] == 0 ? $w :$this->options["draw_height"] )  );
			}
		}
		if($this->options["draw_fit"] == "contain"){
			$img->maxarea( ($this->options["draw_width"] == 0 ? $w :$this->options["draw_width"] ) , ($this->options["draw_height"] == 0 ? $w :$this->options["draw_height"] )  );
		}
		if($this->options["draw_fit"] == "cover"){
			$img->minarea( ($this->options["draw_width"] == 0 ? $w :$this->options["draw_width"] ) , ($this->options["draw_height"] == 0 ? $w :$this->options["draw_height"] )  );
		}
		$w  = $img->getWidth();
		$h  = $img->getHeight();
		$x  = $this->options["draw_left"];
		$y  = $this->options["draw_top"];
		$tx = $this->options["draw_translateX"];
		$ty = $this->options["draw_translateY"];
		if(is_numeric($tx)){
			$x += $tx;
		}else{
			$i = 1;
			if(strstr($tx,"-")){
				$i = -1;
				$tx = str_replace("-","",$tx);
			}
			if(strstr($tx,"%")){
				$tx = str_replace("%","",$tx);
				$x = $x + (($w / 100 * $tx) * $i);
			}else{
				$tx = str_replace("px","",$tx);
				$x = $x + ($tx * $i);
			}
		}
		if(is_numeric($ty)){
			$y += $ty;
		}else{
			$i = 1;
			if(strstr($ty,"-")){
				$i = -1;
				$ty = str_replace("-","",$ty);
			}
			if(strstr($ty,"%")){
				$ty = str_replace("%","",$ty);
				$y = $y + (($h / 100 * $ty) * $i);
			}else{
				$ty = str_replace("px","",$ty);
				$y = $y + ($ty * $i);
			}
		}
		imagecopy($this->image, $img->image, $x, $y, 0, 0, $w, $h);
		return $this;
	}



}
