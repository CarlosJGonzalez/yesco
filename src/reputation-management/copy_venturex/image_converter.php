<? 
createimageinstantly();
        //$targetFolder = '/gw/media/uploads/processed/';
        //$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
        //$img3 = $targetPath.'img3.png';
        //print_r(getimagesize('http://www.vapor-rage.com/wp-content/uploads/2014/05/sample.jpg'));
        function createimageinstantly($img1='',$img2='',$img3=''){
            $x=$y=600;
            header('Content-Type: image/png');
            $targetFolder = '/reviews/reviews_img/';
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

            $img1 = $targetPath.'five_stars.png';
            $img2 = $targetPath.'gmb.png';
            $img3 = $targetPath.'icon-calendar.png';

            $outputImage = imagecreatetruecolor(600, 355);

            // set background to white
            $white = imagecolorallocate($outputImage, 255, 255, 255);
            imagefill($outputImage, 0, 0, $white);

            $first = imagecreatefrompng($img1);
            $second = imagecreatefrompng($img2);
            $third = imagecreatefrompng($img3);

            //imagecopyresized ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
            imagecopyresized($outputImage,$first,400,50,0,0, $x, $y,$x,$y);
           // imagecopyresized($outputImage,$second,0,0,0,0, $x, $y,$x,$y);
            //imagecopyresized($outputImage,$third,200,200,0,0, 100, 100, 204, 148);

            // Add the text
            //imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
            //$white = imagecolorallocate($im, 255, 255, 255);
            $text = 'School Name Here';
            $font = 'OldeEnglish.ttf';
            imagettftext($outputImage, 32, 0, 150, 150, $white, $font, $text);

            $filename =$targetPath .round(microtime(true)).'.png';
			
			//$outputImage = resizeImage($img1);
			//file_put_contents($filename, outputImage);
            imagepng($outputImage, $filename);

            imagedestroy($outputImage);
        }
		function resizeImage($filename)
		{
			$percent = 0.20;
			// Get new dimensions
			list($width, $height) = getimagesize($filename);
			$new_width = $width * $percent;
			$new_height = $height * $percent;

			// Resample
			$image_p = imagecreatetruecolor($new_width, $new_height);
			$image = imagecreatefrompng($filename);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			return $image_p;
		}

?>