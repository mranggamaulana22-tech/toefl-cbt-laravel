<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhotoService
{
    public function storeOptimized(UploadedFile $file): string
    {
        $fallbackPath = $file->store('profile-photos', 'public');
        if (! is_string($fallbackPath) || $fallbackPath === '') {
            throw new \RuntimeException('Failed to store uploaded profile photo.');
        }

        if (! extension_loaded('gd')) {
            return $fallbackPath;
        }

        $sourceBinary = $file->get();
        $sourceImage = $sourceBinary !== false ? @imagecreatefromstring($sourceBinary) : false;

        if ($sourceImage === false) {
            return $fallbackPath;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        if ($sourceWidth < 1 || $sourceHeight < 1) {
            imagedestroy($sourceImage);

            return $fallbackPath;
        }

        $maxEdge = 512;
        $scale = min($maxEdge / $sourceWidth, $maxEdge / $sourceHeight, 1);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
        imagefill($targetImage, 0, 0, $transparent);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        ob_start();
        $mimeType = $file->getMimeType() ?? 'image/jpeg';
        $encodedExtension = 'jpg';

        if ($mimeType === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($targetImage, null, 82);
            $encodedExtension = 'webp';
        } elseif ($mimeType === 'image/png' && function_exists('imagepng')) {
            imagepng($targetImage, null, 6);
            $encodedExtension = 'png';
        } elseif (function_exists('imagejpeg')) {
            imagejpeg($targetImage, null, 82);
            $encodedExtension = 'jpg';
        } else {
            ob_end_clean();
            imagedestroy($sourceImage);
            imagedestroy($targetImage);

            return $fallbackPath;
        }

        $optimizedBinary = ob_get_clean();
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if ($optimizedBinary === false || $optimizedBinary === '') {
            return $fallbackPath;
        }

        if ($fallbackPath) {
            Storage::disk('public')->delete($fallbackPath);
        }

        $optimizedPath = 'profile-photos/' . Str::uuid() . '.' . $encodedExtension;
        $stored = Storage::disk('public')->put($optimizedPath, $optimizedBinary);
        if (! $stored) {
            throw new \RuntimeException('Failed to store optimized profile photo.');
        }

        return $optimizedPath;
    }
}