<?php
/*if(substr_count($_SERVER['HTTP_REFERER'],  $_SERVER['HTTP_HOST'])==0) {
	exit;	
}*/
session_start();

$name = isset($_GET['name']) ? $_GET['name'] : 'captcha';
$color = (isset($_GET['c']) && ($_GET['c']!==''))  ? $_GET['c'] : 'F1F1F1';
$fontcolor = (isset($_GET['f']) && ($_GET['f']!==''))  ? $_GET['f'] : '000';

function rgb2array($rgb) {
    return array(
        base_convert(substr($rgb, 0, 2), 16, 10),
        base_convert(substr($rgb, 2, 2), 16, 10),
        base_convert(substr($rgb, 4, 2), 16, 10),
    );
}
$background = rgb2array($color);
$fontcolor = rgb2array($fontcolor);

$count=5;	/* количество символов */
$width=100; /* ширина картинки */
$height=32; /* высота картинки */
$font_size_min=18; /* минимальная высота символа */
$font_size_max=22; /* максимальная высота символа */
$font_file="./Comic_Sans_MS.ttf"; /* путь к файлу относительно w3captcha.php */
$char_angle_min=-10; /* максимальный наклон символа влево */
$char_angle_max=10;	/* максимальный наклон символа вправо */
$char_angle_shadow=6;	/* размер тени */
$char_align=25;	/* выравнивание символа по-вертикали */
$start=5;	/* позиция первого символа по-горизонтали */
$interval=16;	/* интервал между началами символов */
$chars="0123456789"; /* набор символов */


$image=imagecreatetruecolor($width, $height);
$background_color=imagecolorallocate($image, $background[0], $background[1], $background[2]); /* rbg-цвет фона */
$font_color=imagecolorallocate($image, $fontcolor[0], $fontcolor[1], $fontcolor[2]); /* rbg-цвет тени */
$noiseColor = imagecolorallocate($image, 100, 120, 180);//Задаем цвет помех
$lineColor = imagecolorallocate($image, 148, 178, 196);
//$font_color=imagecolorallocate($image, 101, 188, 222); /* rbg-цвет тени */

imagefill($image, 0, 0, $background_color);

$str="";
if (isset($_SESSION[$name]) && trim($_SESSION[$name])!=='' && !(isset($_GET['rnd']))){
	$str=$_SESSION[$name];
}else{
	$num_chars=strlen($chars);
	for ($i=0; $i<$count; $i++)
	{
		$char=$chars[rand(0, $num_chars-1)];
		$str.=$char;
	}
	$_SESSION[$name]=$str;	
}

	for ($i=0; $i<$count; $i++)
	{
		$char=$str[$i];
		$font_size=rand($font_size_min, $font_size_max);
		$char_angle=rand($char_angle_min, $char_angle_max);
        /* Set noise*/
        imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
        /* Set line*/
        for( $s = 0; $s < $count*5; $s++ )
        { imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noiseColor); }
        
		imagettftext($image, $font_size, $char_angle, $start, $char_align, $font_color, $font_file, $char);
		imagettftext($image, $font_size, $char_angle+$char_angle_shadow*(rand(0, 1)*2-1), $start, $char_align, $background_color, $font_file, $char);

        $start+=$interval;
	}

if (function_exists("imagegif"))
{
	header("Content-type: image/gif");
	imagegif($image);
}
elseif (function_exists("imagepng"))
{
	header("Content-type: image/png");
	imagepng($image);
}
elseif (function_exists("imagejpeg"))
{
	header("Content-type: image/jpeg");
	imagejpeg($image);
}

imagedestroy($image);

?>
