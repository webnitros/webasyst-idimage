<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 16.04.2025
 * Time: 13:02
 */

namespace IdImage\Support;

use waImageGd;

class GenerateThumb
{
    public static function create(string $source, string $target, int $size = 224)
    {
        $img = new waImageGd($source);
        $w = $img->width;
        $h = $img->height;

        // Если хотя бы одна сторона меньше — не увеличиваем, просто центрируем на холсте
        if ($w != $size || $h != $size) {
            $src = imagecreatefromstring(file_get_contents($source));

            // Получаем размеры исходного изображения
            $w = imagesx($src);
            $h = imagesy($src);

            // Создаем новый холст 224x224 с белым фоном
            $dst = imagecreatetruecolor($size, $size);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefill($dst, 0, 0, $white);

            // Вычисляем координаты для центрирования
            $dst_x = (int)(($size - $w) / 2);
            $dst_y = (int)(($size - $h) / 2);

            // Копируем исходное изображение на центр холста
            imagecopy($dst, $src, $dst_x, $dst_y, 0, 0, $w, $h);

            // Сохраняем результат в файл JPEG
            imagejpeg($dst, $target, 90);

            // Очищаем память
            imagedestroy($dst);
            imagedestroy($src);
        } else {
            // Если больше или равно — обычная обработка
            $img->resize($size, $size);
            $img->crop($size, $size);
            $img->save($target, 90);
        }

        return $target;
    }
}
