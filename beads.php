<?php include_once('header.php'); ?>

<?php

$beads_color = [
  ['#f8f7ee', '#fdfdf7', '#eed7ab', '#985b17', '#96dd5b', '#45d865', '#3ebd80', '#76e48d',],
  ['#ffdb5f', '#fead26', '#f7c274', '#d66908', '#92f2ed', '#29c7b9', '#57aa9b', '#b9d8a6',],
  ['#fb8417', '#f86b07', '#c51f1c', '#7c0610', '#629b73', '#093530', '#084874', '#2c5374',],
  ['#629b73', '#f56bb4', '#f53e9b', '#d03d76', '#6bd4ef', '#1190b7', '#00518a', '#1978a5',],
  ['#faa4d1', '#ffb7ea', '#8f3555', '#e97fb9', '#267eae', '#0e296d', '#1a345b', '#1c3e81',],
  ['#ceb8ec', '#f487f0', '#b460c3', '#62607c', '#1c141b', '#bcbfb2', '#3f5c62', '#988f8f',],
  ['#85c3e6', '#6197de', '#32a3d7', '#6098e0', '', '', '', '#2f4541',],
];
$beads_color_array = [];
foreach ($beads_color as $item) { $beads_color_array = array_merge($beads_color_array, $item); }



$filename = '22';
if (!empty($filename)/* && is_file($filename) && file_exists($filename)*/) {
  $columns = 35;
  $original_img = $filename . '.jpg';
  $result_img = $filename . '_3.jpg';
  $size = getimagesize($original_img);
  $width_image = !empty($size[0]) ? $size[0] : 0;
  $height_image = !empty($size[1]) ? $size[1] : 0;

  $step = round($width_image / $columns);
  $square = 21;
  $width_image_new = ceil($width_image / $step) * $square;
  $height_image_new = ceil($height_image / $step) * $square;
  $width_pixel = 0;
  $height_pixel = 0;

  // Загрузка JPG-изображения из файла Image.jpg
  $image = imageCreateFromJpeg($original_img);
  // imagecreatetruecolor — Создаёт новое полноцветное изображение
  $image_new = imageCreateTrueColor($width_image_new, $height_image_new);
  for ($y = 0; $y <= $height_image; $y += $step) {
    $width_pixel = 0;
    $height_pixel++;
    for ($x = 0; $x <= $width_image; $x += $step) {
      $width_pixel++;
      // Получаем RGB точки
      // imageColorat - Возвращает индекс цвета пиксела на заданных координатах на изображении image
      $rgb = imageColorat($image, $x, $y);
      // Получаем массив значений RGB
      // imageColorsForIndex - Возвращает ассоциативный массив с красным, зеленым,
      // синим и альфа ключами, содержащий соответствующие значения для заданного индекса цвета.
      $color = imageColorsForIndex($image, $rgb);
      // Формирование цвета для отрисовки
      $color_hex = color_beads($color, $beads_color_array);
      list($r, $g, $b) = get_rgb_by_hex($color_hex);
      $color = imageColorAllocate($image_new, $r, $g, $b);
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
      );

      // Рисование контуров костяшек (16777215 - white)

        imageRectangle(
          $image_new,
          $x / $step * $square,
          $y / $step * $square,
          $x / $step * $square + $square,
          $y / $step * $square + $square,
          16777215
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
        <h2 class="pb-2 border-bottom mb-3">BEADS</h2>

        <img src="<?= $original_img . '?t=' . time() ?>" alt="" width="49%">
        <img src="<?= $result_img . '?t=' . time() ?>" alt="" width="49%">
        <p>
          <?= $width_image ?? 0, 'x', $height_image ?? 0; ?> -> <?= $width_image_new ?? 0, 'x', $height_image_new ?? 0; ?>
          [<?= $width_pixel ?? 0, 'x', $height_pixel ?? 0, '=', ($width_pixel ?? 0) * ($height_pixel ?? 0); ?>]
        </p>
      </div>
    </div>

    <div class="row py-lg-3">
      <table>
        <?php foreach ($beads_color as $row) : ?>
        <tr>
          <?php foreach ($row as $color) : ?>
          <td style="height: 20px; background-color: <?= $color; ?>"></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </section>
</main>

<?php include_once('footer.php'); ?>