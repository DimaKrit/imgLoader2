<?php

namespace DimaKrit\TestProject;

class Download
{

    /**
     * @param string $url
     */

    public function imageDownload($url = 'https://storage.googleapis.com/imgfave/image_cache/1483572468343197.jpg')
    {

        if (!preg_match("/^https?:/i", $url) && filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Укажите корректную ссылку на удалённый файл.');
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_TIMEOUT => 60,

            CURLOPT_FOLLOWLOCATION => 1,

            CURLOPT_RETURNTRANSFER => 1,

            CURLOPT_NOPROGRESS => 0,

            CURLOPT_BUFFERSIZE => 1024,

            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,

        ]);

        $raw = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_errno($ch);

        curl_close($ch);

        if ($error === CURLOPT_TIMEOUT_MS) throw new \Exception('Превышен лимит ожидания.');
        if ($error === CURLE_ABORTED_BY_CALLBACK) throw new \Exception('Размер не должен превышать 5 Мбайт.');
        if ($info['http_code'] !== 200) throw new \Exception('Файл не доступен.');

        $fi = finfo_open(FILEINFO_MIME_TYPE);

        $mime = (string)finfo_buffer($fi, $raw);

        finfo_close($fi);

        if (strpos($mime, 'image') === false) throw new \Exception('Можно загружать только изображения.');

        $image = getimagesizefromstring($raw);

        $limitTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        $limitWidth = 1280;
        $limitHeight = 768;

        if (!in_array($image[2], $limitTypes)) throw new \Exception('Мы поддерживаем картинки только с типом JPG, PNG и GIF.');
        if ($image[1] > $limitHeight) throw new \Exception('Высота изображения не должна превышать 768 точек.');
        if ($image[0] > $limitWidth) throw new \Exception('Ширина изображения не должна превышать 1280 точек.');

        $name = md5($raw);

        $extension = image_type_to_extension($image[2]);

        $format = str_replace('jpeg', 'jpg', 'png', $extension);


        $path = realpath(dirname(__FILE__) . '/../');


        if (!file_exists($path . '/pics')) {
            mkdir($path . '/pics', 777);
        }


        if (!file_put_contents($path . '/pics/' . $name . '.' . $format, $raw)) {
            throw new \Exception('При сохранении изображения на диск произошла ошибка.');
        }
    }


}