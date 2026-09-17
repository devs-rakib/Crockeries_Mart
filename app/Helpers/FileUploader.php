<?php
namespace App\Helpers;

class FileUploader
{
    private static array $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static int $maxSize = 5242880;

    public static function upload(array $file, string $directory, string $prefix = ''): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > self::$maxSize) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::$allowedImageTypes)) {
            return null;
        }

        $uploadDir = UPLOAD_PATH . '/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $directory . '/' . $filename;
        }
        return null;
    }

    public static function delete(string $path): bool
    {
        $fullPath = UPLOAD_PATH . '/' . $path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public static function resize(string $path, int $maxWidth = 800, int $maxHeight = 800): bool
    {
        $fullPath = UPLOAD_PATH . '/' . $path;
        if (!file_exists($fullPath)) return false;

        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) return false;

        list($origWidth, $origHeight) = $imageInfo;
        if ($origWidth <= $maxWidth && $origHeight <= $maxHeight) return true;

        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $newWidth = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'jpeg':
                $source = imagecreatefromjpeg($fullPath);
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagejpeg($resized, $fullPath, 85);
                break;
            case 'png':
                $source = imagecreatefrompng($fullPath);
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagepng($resized, $fullPath);
                break;
            case 'webp':
                $source = imagecreatefromwebp($fullPath);
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagewebp($resized, $fullPath, 85);
                break;
            default:
                return false;
        }
        return true;
    }
}
