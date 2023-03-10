<?php
function print_array($array = array(), $exit = false)
{
  echo '<pre>', print_r($array, true), '</pre>';
  if ($exit) exit();
}

function color_grayscale($color, $reverse = false)
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

      if ($r >= 0   && $r < 42)   return !$reverse ? 1 : 6;
  elseif ($r >= 42  && $r < 84)   return !$reverse ? 2 : 5;
  elseif ($r >= 84  && $r < 126)  return !$reverse ? 3 : 4;
  elseif ($r >= 126 && $r < 168)  return !$reverse ? 4 : 3;
  elseif ($r >= 168 && $r < 210)  return !$reverse ? 5 : 2;
  else                            return !$reverse ? 6 : 1;
}

$filename = '16';
$dice_color = !empty($_POST['diceColor']) && in_array($_POST['diceColor'], ['black', 'white'])
  ? trim($_POST['diceColor'])
  : 'black'
;
$dice_size = !empty($_POST['diceSize']) && in_array($_POST['diceSize'], ['small', 'middle', 'big'])
  ? trim($_POST['diceSize'])
  : 'real'
;
$dice_border = false;

if (!empty($filename)) {
  $step = 9;
  $square = 21;
  $original_img = $filename . '.jpg';
  $result_img = $filename . '_2.jpg';
  $size = getimagesize($original_img);
  $width_image = !empty($size[0]) ? $size[0] : 0;
  $height_image = !empty($size[1]) ? $size[1] : 0;
  $width_image_new = ceil($width_image / $step) * $square;
  $height_image_new = ceil($height_image / $step) * $square;
  $width_pixel = 0;
  $height_pixel = 0;

  $dice[1] = imageCreateFromJpeg("source/dice/{$dice_color}_{$dice_size}_1.jpg");
  $dice[2] = imageCreateFromJpeg("source/dice/{$dice_color}_{$dice_size}_2.jpg");
  $dice[3] = imageCreateFromJpeg("source/dice/{$dice_color}_{$dice_size}_3.jpg");
  $dice[4] = imageCreateFromJpeg("source/dice/{$dice_color}_{$dice_size}_4.jpg");
  $dice[5] = imageCreateFromJpeg("source/dice/{$dice_color}_{$dice_size}_5.jpg");
  $dice[6] = imageCreateFromJpeg("source/dice/{$dice_color}_{$dice_size}_6.jpg");

  /*
   * ???????????????????? ?????????????? ?????????????????????? ?? ???????????????? ????????
   * https://myrusakov.ru/php-color-pixel.html
   */
  // ???????????????? JPG-?????????????????????? ???? ?????????? Image.jpg
  $image = imageCreateFromJpeg($original_img);
  // ????????????????????????????????????????
  if ($dice_border === true) {
    $width_image_new++;
    $height_image_new++;
  }
  $image_new = imageCreateTrueColor($width_image_new, $height_image_new);
  for ($y = 0; $y <= $height_image; $y += $step) {
    $width_pixel = 0;
    $height_pixel++;
    for ($x = 0; $x <= $width_image; $x += $step) {
      $width_pixel++;
      // ???????????????? RGB ??????????
      $rgb = imageColorat($image, $x, $y);
      // ???????????????? ???????????? ???????????????? RGB
      $color = imageColorsForIndex($image, $rgb);
      // ???????????????????????? ?????????? ?????? ??????????????????
      $color_black = color_grayscale($color, $dice_color === 'white');
      /*
      $color = imageColorAllocate($image_new, $color_black, $color_black, $color_black);
      // ?????????????????? ??????????
      // imageSetPixel($image_new, $x, $y, $color);

      // ?????????????????? ???????????????????????????? imageRectangle / imageFilledRectangle
      imageFilledRectangle(
          $image_new,
          $x / $step * $square,
          $y / $step * $square,
          $x / $step * $square + $square,
          $y / $step * $square + $square,
          $color
      );*/

      // imagecopyresampled ??? ?????????????????????? ?? ?????????????????? ?????????????? ?????????????????????? ?? ????????????????????????????????
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

      // ?????????????????? ???????????????? ???????????????? (16777215 - white)
      if ($dice_border === true)
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

  //?????????????????????? ?????????????? ??????????????
  imageDestroy($image);
  imageDestroy($image_new);
}
?>


<!doctype html>
<html lang="en" class="h-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="DICES">
  <meta name="author" content="babuzinga">
  <meta name="generator" content="">
  <title>DICES</title>

  <link rel="stylesheet" href="/source/css/bootstrap.min.css">
</head>
<body class="d-flex flex-column h-100">
  <main>
    <section class="container mt-5">
      <div class="row py-lg-3">
        <div class="col-md-10 mx-auto">
          <h2 class="pb-2 border-bottom mb-3">Columns with icons</h2>

          <img src="<?= $original_img . '?t=' . time() ?>" alt="" width="49%">
          <img src="<?= $result_img . '?t=' . time() ?>" alt="" width="49%">
          <p>
            <?= $width_image, 'x', $height_image; ?> -> <?= $width_image_new, 'x', $height_image_new; ?>
            [<?= $width_pixel, 'x', $height_pixel, '=', $width_pixel * $height_pixel; ?>]
          </p>
        </div>
      </div>

      <div class="row py-lg-3">
        <div class="col-md-10 mx-auto">
          <form action="/index.php" method="post" enctype="multipart/form-data">
            Color:
            <div class="form-check">
              <input
                  class="form-check-input"
                  type="radio"
                  name="diceColor"
                  value="black"
                  id="diceColorBlack"
                  <?= ( !empty($dice_color) && $dice_color === 'black' ) ? 'checked' : ''; ?>
              >
              <label class="form-check-label" for="flexRadioDefault1">
                Black
              </label>
            </div>
            <div class="form-check mb-3">
              <input
                  class="form-check-input"
                  type="radio"
                  name="diceColor"
                  value="white"
                  id="diceColorWhite"
                  <?= ( !empty($dice_color) && $dice_color === 'white' ) ? 'checked' : ''; ?>
              >
              <label class="form-check-label" for="diceColorWhite">
                White
              </label>
            </div>

            <div class="mb-3">
              <label for="formFile" class="form-label">Default file input example</label>
              <input class="form-control" type="file" id="formFile" name="image">
            </div>

            <div class="col-auto">
              <button type="submit" class="btn btn-primary mb-3">Generate</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer mt-auto py-3 bg-light">
    <div class="container">
      <span class="text-muted">Hello world mazafaka.</span>
    </div>
  </footer>
  <script src="/source/js/bootstrap.min.js"></script>
</body>
</html>
