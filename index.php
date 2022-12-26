<?php
  function print_array($array = array(), $exit = false)
  {
    echo '<pre>', print_r($array, true), '</pre>';
    if ($exit) exit();
  }

  function color_grayscale($color)
  {
    // R' = G' = B' = (R+G+B) / 3 = (30+128+255) / 3 = 138
    $r = round(($color['red'] + $color['green'] + $color['blue']) / 3);

    if ($r >= 0 && $r < 42)         return 0;
    elseif ($r >= 42 && $r < 84)    return 51;
    elseif ($r >= 84 && $r < 126)   return 102;
    elseif ($r >= 126 && $r < 168)  return 153;
    elseif ($r >= 168 && $r < 210)  return 204;
    else                            return 256;

    return $r;
  }

  $step         = 10;
  $original_img = '17.jpg';
  $result_img   = '17_2.jpg';
  $size         = getimagesize($original_img);
  $width_image  = !empty($size[0]) ? $size[0] : 0;
  $height_image = !empty($size[1]) ? $size[1] : 0;

  /*
   * Перебираем пиксели изображения и получаем цвет
   * https://myrusakov.ru/php-color-pixel.html
   */
  $result     = [];
  // Загрузка JPG-изображения из файла Image.jpg
  $image      = imageCreateFromJpeg($original_img);
  // Создание изображения
  $image_new = imagecreatetruecolor($width_image, $height_image);
  for ($y = 0; $y <= $height_image; $y += $step) {
    for ($x = 0; $x <= $width_image; $x += $step) {
      // Получаем RGB точки
      $rgb = imagecolorat($image, $x, $y);
      // Получаем массив значений RGB
      $color = imagecolorsforindex($image, $rgb);
      $result[] = $color;

      $color_black = color_grayscale($color);
      $color = imagecolorallocate($image_new, $color_black, $color_black, $color_black);
      // Рисование точки
      //imagesetpixel($image_new, $x, $y, $color);

      // Рисование прямоугольника imageRectangle / imageFilledRectangle
      imageFilledRectangle($image_new, $x, $y, $x + $step, $y + $step, $color);
    };
  };

  imagejpeg($image_new, $result_img);

  //Освобождаем ресурсы сервера
  imageDestroy($image);
  imageDestroy($image_new);

  //print_array($result);
?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <img src="<?= $original_img . '?t=' . time() ?>" alt="" width="400px">
  <img src="<?= $result_img . '?t=' . time() ?>" alt="" width="400px">
</body>
</html>
