<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageStorageService
{
    public function storeProductImage(UploadedFile $file, Product $product): string
    {
        $uuid = (string) Str::uuid();
        $base = "products/{$product->id}/{$uuid}";

        $source = $this->load($file);

        $this->save($source, "{$base}-full.webp", 1600, null, false);
        $this->save($source, "{$base}-card.webp", 800, 800, true);
        $this->save($source, "{$base}-thumb.webp", 400, 400, true);

        unset($source);

        return $base;
    }

    public function deleteProductImage(?string $base): void
    {
        if (! $base) {
            return;
        }

        foreach (['full', 'card', 'thumb'] as $size) {
            Storage::disk('public')->delete("{$base}-{$size}.webp");
        }
    }

    public function productImageUrl(?string $base, string $size = 'card'): ?string
    {
        if (! $base) {
            return null;
        }

        return Storage::disk('public')->url("{$base}-{$size}.webp");
    }

    public function storeBannerImage(UploadedFile $file): string
    {
        $path = 'banners/'.Str::uuid().'.webp';
        $source = $this->load($file);

        $this->save($source, $path, 1600, 533, true);
        unset($source);

        return $path;
    }

    public function storeVariantImage(UploadedFile $file): string
    {
        $path = 'variants/'.Str::uuid().'.webp';
        $source = $this->load($file);

        $this->save($source, $path, 800, 800, true);
        unset($source);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function url(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function load(UploadedFile $file): \GdImage
    {
        $contents = file_get_contents($file->getRealPath());
        $image = $contents !== false ? @imagecreatefromstring($contents) : false;

        if (! $image) {
            throw new RuntimeException('Berkas gambar tidak dapat dibaca.');
        }

        return $image;
    }

    private function save(\GdImage $source, string $path, int $width, ?int $height, bool $crop): void
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        if ($crop && $height) {
            $targetRatio = $width / $height;
            $srcRatio = $srcW / $srcH;

            if ($srcRatio > $targetRatio) {
                $newH = $srcH;
                $newW = (int) round($srcH * $targetRatio);
            } else {
                $newW = $srcW;
                $newH = (int) round($srcW / $targetRatio);
            }

            $srcX = (int) (($srcW - $newW) / 2);
            $srcY = (int) (($srcH - $newH) / 2);

            $canvas = imagecreatetruecolor($width, $height);
            imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $width, $height, $newW, $newH);
        } else {
            $ratio = min(1, $width / $srcW);
            $newW = max(1, (int) round($srcW * $ratio));
            $newH = max(1, (int) round($srcH * $ratio));

            $canvas = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
        }

        Storage::disk('public')->makeDirectory(dirname($path));

        ob_start();
        imagewebp($canvas, null, 82);
        $binary = ob_get_clean();
        unset($canvas);

        Storage::disk('public')->put($path, $binary);
    }
}
