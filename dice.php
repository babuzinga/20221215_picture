<?php include_once('header.php'); ?>

<?php

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
   * Перебираем пиксели изображения и получаем цвет
   * https://myrusakov.ru/php-color-pixel.html
   */
  // Загрузка JPG-изображения из файла Image.jpg
  $image = imageCreateFromJpeg($original_img);
  // Создание изображения
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
      // Получаем RGB точки
      $rgb = imageColorat($image, $x, $y);
      // Получаем массив значений RGB
      $color = imageColorsForIndex($image, $rgb);
      // Формирование цвета для отрисовки
      $color_black = color_grayscale($color, $dice_color === 'white');
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

  //Освобождаем ресурсы сервера
  imageDestroy($image);
  imageDestroy($image_new);
}
?>

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

<?php include_once('footer.php'); ?>