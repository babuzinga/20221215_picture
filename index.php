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
    //return $r;

    /*if ($r >= 0 && $r < 42)         return 0;
    elseif ($r >= 42 && $r < 84)    return 51;
    elseif ($r >= 84 && $r < 126)   return 102;
    elseif ($r >= 126 && $r < 168)  return 153;
    elseif ($r >= 168 && $r < 210)  return 204;
    else                            return 255;*/

        if ($r >= 0 && $r < 42)     return 1;
    elseif ($r >= 42 && $r < 84)    return 2;
    elseif ($r >= 84 && $r < 126)   return 3;
    elseif ($r >= 126 && $r < 168)  return 4;
    elseif ($r >= 168 && $r < 210)  return 5;
    else                            return 6;
  }

  $step             = 10;
  $square           = 21;
  $border           = true;
  $filename         = '19';
  $original_img     = $filename . '.jpg';
  $result_img       = $filename . '_2.jpg';
  $size             = getimagesize($original_img);
  $width_image      = !empty($size[0]) ? $size[0] : 0;
  $height_image     = !empty($size[1]) ? $size[1] : 0;
  $width_image_new  = ceil($width_image / $step) * $square;
  $height_image_new = ceil($height_image / $step) * $square;
  $width_pixel      = 0;
  $height_pixel     = 0;

  $dice[1]          = imageCreateFromJpeg('dice/1.jpg');
  $dice[2]          = imageCreateFromJpeg('dice/2.jpg');
  $dice[3]          = imageCreateFromJpeg('dice/3.jpg');
  $dice[4]          = imageCreateFromJpeg('dice/4.jpg');
  $dice[5]          = imageCreateFromJpeg('dice/5.jpg');
  $dice[6]          = imageCreateFromJpeg('dice/6.jpg');

  /*
   * Перебираем пиксели изображения и получаем цвет
   * https://myrusakov.ru/php-color-pixel.html
   */
  // Загрузка JPG-изображения из файла Image.jpg
  $image      = imageCreateFromJpeg($original_img);
  // Создание изображения
  if ($border === true) { $width_image_new++; $height_image_new++; }
  $image_new = imageCreateTrueColor($width_image_new, $height_image_new);
  for ($y = 0; $y <= $height_image; $y += $step) {
    $width_pixel = 0;
    $height_pixel ++;
    for ($x = 0; $x <= $width_image; $x += $step) {
      $width_pixel ++;
      // Получаем RGB точки
      $rgb = imageColorat($image, $x, $y);
      // Получаем массив значений RGB
      $color = imageColorsForIndex($image, $rgb);
      // Формирование цвета для отрисовки
      $color_black = color_grayscale($color);
      /*
      $color = imageColorAllocate($image_new, $color_black, $color_black, $color_black);
      // Рисование точки
      // imageSetPixel($image_new, $x, $y, $color);

      // Рисование прямоугольника imageRectangle / imageFilledRectangle
      imageFilledRectangle(
          $image_new,
          $x / $step * $square,
          $y / $step * $square,
          $x / $step * $square + $square,
          $y / $step * $square + $square,
          $color
      );*/

      // imagecopyresampled — Копирование и изменение размера изображения с ресемплированием
      imageCopyResampled(
        $image_new,
        $dice[$color_black],
        $x / $step * $square,
        $y / $step * $square,
        0,
        0,
        $square,
        $square,
        $square,
        $square
      );

      // Рисование контуров костяшек (16777215 - white)
      if ($border === true)
        imageRectangle(
          $image_new,
          $x / $step * $square,
          $y / $step * $square,
          $x / $step * $square + $square,
          $y / $step * $square + $square,
          11111111
        );
    };
  };

  imageJpeg($image_new, $result_img);

  //Освобождаем ресурсы сервера
  imageDestroy($image);
  imageDestroy($image_new);
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
  <pre>
<?= $width_image, 'x', $height_image; ?> -> <?= $width_image_new, 'x', $height_image_new; ?> [<?= $width_pixel, 'x', $height_pixel, '=', $width_pixel * $height_pixel; ?>]
  </pre>
  <img src="<?= $original_img . '?t=' . time() ?>" alt="" width="500px">
  <img src="<?= $result_img . '?t=' . time() ?>" alt="" width="500px">
</body>
</html>
