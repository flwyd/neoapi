<?php

namespace App\Models;

class Photo {

    const MUGSHOT_PATH = 'images/mugshots/id-%05u.jpg';
    const THUMBNAIL_PATH = 'images/mugshots/thumbs/thumb-%05u.jpg';

    const ASSET_DIR = 'public';

    public static function imagesUrl($personId) {
        $urls = [];

        $mugshotPath = sprintf(self::MUGSHOT_PATH, $personId);
        if (file_exists(base_path().'/'.self::ASSET_DIR.'/'.$mugshotPath)) {
            $urls['photo_url'] = asset($mugshotPath);
        }

        $thumbPath = sprintf(self::THUMBNAIL_PATH, $personId);
        if (file_exists(base_path().'/'.self::ASSET_DIR.'/'.$thumbPath)) {
            $urls['thumbnail_url'] = asset($thumbPath);
        }

        return $urls;
    }
}
