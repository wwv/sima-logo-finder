<?php

class SimaLogoFinder {
	public static function hasOldLogo($imgname)	{
		$img = imagecreatefromjpeg($imgname);
		$img2 = imagecreatefromjpeg($imgname);
		$logo = imagecreatefromgif('logo.gif');

		$box = new imageBox(array('x' => 11, 'y' => 13));
		$box->createFromImage($logo);
		imagecopy($img2, $logo, $box->x, $box->y, 0, 0, $box->width, $box->height);	

		$part1 = $box->getPartOf($img);
		$part2 = $box->getPartOf($img2);

		$res = imagecompare($part1, $part2, 20, 20, 20, 33, 37);

		imagedestroy($img);
		imagedestroy($img2);
		return !isset($res['ErrorLevel']);
	}
}

class imageBox {
	public $width = 0;
	public $height = 0;
	public $x = 0;
	public $y = 0;
	public $images = array();
	public function __construct(array $params) {
		foreach ($params as $key=>$param) {
			$this->$key = $param;
		}
	}
	public function createFromImage($img) {
		$this->width = imagesx($img);
		$this->height = imagesy($img);
		$this->images[] = $img;
	}
	public function getPartOf($src) {
		$dst = imagecreatetruecolor($this->width, $this->height);
		imagecopy($dst, $src, 0, 0, $this->x, $this->y, $this->width, $this->height);
		$this->images[] = $dst;
		return $dst;
	}
	public function __destroy() {
		foreach ($this->images as $res) {
			imagedestroy($res);
		}
		$this->images = array();
	}
}

/**
* Image Comparing Function (C)2011 Robert Lerner, All Rights Reserved
* $image1                     STRING/RESOURCE          Filepath and name to PNG or passed image resource handle
* $image2                    STRING/RESOURCE          Filepath and name to PNG or passed image resource handle
* $RTolerance               INTEGER (0-/+255)     Red Integer Color Deviation before channel flag thrown
* $GTolerance               INTEGER (0-/+255)     Green Integer Color Deviation before channel flag thrown
* $BTolerance               INTEGER (0-/+255)     Blue Integer Color Deviation before channel flag thrown
* $WarningTolerance     INTEGER (0-100)          Percentage of channel differences before warning returned
* $ErrorTolerance          INTEGER (0-100)          Percentage of channel difference before error returned

*/
function imagecompare($image1, $image2, $RTolerance=0, $GTolerance=0, $BTolerance=0, $WarningTolerance=1, $ErrorTolerance=5)
     {
     if (is_resource($image1))
          $im = $image1;
     else
          if (!$im = imagecreatefrompng($image1))
               trigger_error("Image 1 could not be opened",E_USER_ERROR);
     
     if (is_resource($image2))
          $im2 = $image2;
     else
          if (!$im2 = imagecreatefrompng($image2))
               trigger_error("Image 2 could not be opened",E_USER_ERROR);
          


     $OutOfSpec = 0;

     if (imagesx($im)!=imagesx($im2))
          die("Width does not match.");
     if (imagesy($im)!=imagesy($im2))
          die("Height does not match.");


     //By columns
     for ($width=0;$width<=imagesx($im)-1;$width++)
          {
          for ($height=0;$height<=imagesy($im)-1;$height++)
               {
               $rgb = imagecolorat($im, $width, $height);
               $r1 = ($rgb >> 16) & 0xFF;
               $g1 = ($rgb >> 8) & 0xFF;
               $b1 = $rgb & 0xFF;
               
               $rgb = imagecolorat($im2, $width, $height);
               $r2 = ($rgb >> 16) & 0xFF;
               $g2 = ($rgb >> 8) & 0xFF;
               $b2 = $rgb & 0xFF;
               
               if (!($r1>=$r2-$RTolerance && $r1<=$r2+$RTolerance))
                    $OutOfSpec++;
                    
               if (!($g1>=$g2-$GTolerance && $g1<=$g2+$GTolerance))
                    $OutOfSpec++;
                    
               if (!($b1>=$b2-$BTolerance && $b1<=$b2+$BTolerance))
                    $OutOfSpec++;
               
               
               }
          }
     $TotalPixelsWithColors = (imagesx($im)*imagesy($im))*3;

     $RET['PixelsByColors'] = $TotalPixelsWithColors;
     $RET['PixelsOutOfSpec'] = $OutOfSpec;

     if ($OutOfSpec!=0 && $TotalPixelsWithColors!=0)
          {
          $PercentOut = ($OutOfSpec/$TotalPixelsWithColors)*100;
          $RET['PercentDifference']=$PercentOut;
          if ($PercentOut>=$WarningTolerance) //difference triggers WARNINGTOLERANCE%
               $RET['WarningLevel']=TRUE;
          if ($PercentOut>=$ErrorTolerance) //difference triggers ERRORTOLERANCE%
               $RET['ErrorLevel']=TRUE;
          }

     return $RET;
     }
?>