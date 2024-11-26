<?php
/**
 * @param array $array
 * @param bool $exit
 * @return mixed
 */
function print_array(array $array = array(), bool $exit = false): mixed
{
  echo '<pre>', print_r($array, true), '</pre>';
  if ($exit) exit();
}

/**
 * @param array $color
 * @param bool $reverse
 * @return int
 */
function color_grayscale(array $color, bool $reverse = false): int
{
  // R' = G' = B' = (R+G+B) / 3 = (30+128+255) / 3 = 138
  $r = round(($color['red'] + $color['green'] + $color['blue']) / 3);
  
  if ($r >= 0   && $r < 42)       return !$reverse ? 1 : 6;
  elseif ($r >= 42  && $r < 84)   return !$reverse ? 2 : 5;
  elseif ($r >= 84  && $r < 126)  return !$reverse ? 3 : 4;
  elseif ($r >= 126 && $r < 168)  return !$reverse ? 4 : 3;
  elseif ($r >= 168 && $r < 210)  return !$reverse ? 5 : 2;
  else                            return !$reverse ? 6 : 1;
}

/**
 * https://snipp.ru/php/hex-to-rgb#link-hex-v-rgb
 * @param string $hex_color
 * @return array
 */
function get_rgb_by_hex(string $hex_color): array
{
  return array_map('hexdec', str_split(trim($hex_color, '#'), 2));
}

/**
 * Функция определяет цвет, близкий к цвету из существующего списка
 * https://qna.habr.com/q/89347
 * @param array $color
 * @param array $beads_color
 * @return string
 */
function color_beads(array $color, array $beads_color): string
{
  $result = [];
  list($imgR, $imgG, $imgB) = [$color['red'], $color['green'], $color['blue']];
  foreach ($beads_color as $item) {
    if (empty($item)) { continue; }

    list($r, $g, $b) = get_rgb_by_hex($item);
    $delta = sqrt( pow($imgR - $r, 2) + pow($imgG - $g, 2) + pow($imgB - $b, 2) );
    $result[$item] = $delta;

    /*// 2.3 - примерно соответствует минимально различимому для человеческого глаза отличию между цветами (wiki)
    if ($delta >= 2 and $delta <= 2.5) {
      $result[$i] = $item;
    }*/
  }

  asort($result);
  return array_key_first($result);
}