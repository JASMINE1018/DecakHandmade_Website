<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BannerService
{
    public function __construct(
        private readonly ImageStorageService $images,
        private readonly AuditLogService $audit,
    ) {}

    public function create(array $data, ?UploadedFile $image, Admin $admin): Banner
    {
        $path = $this->images->storeBannerImage($image);

        $banner = Banner::create([
            'title' => $data['title'],
            'text' => $data['text'] ?? null,
            'image_path' => $path,
            'link_url' => $data['link_url'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        $this->audit->record('banner.created', $banner, null, $banner->only(['title', 'is_active']), $admin);

        return $banner;
    }

    public function update(Banner $banner, array $data, ?UploadedFile $image, Admin $admin): Banner
    {
        $before = $banner->only(['title', 'text', 'link_url', 'is_active', 'starts_at', 'ends_at', 'sort_order']);
        $oldPath = $banner->image_path;

        $banner->fill([
            'title' => $data['title'],
            'text' => $data['text'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        if ($image) {
            $banner->image_path = $this->images->storeBannerImage($image);
        }

        DB::transaction(function () use ($banner, $before, $admin, $oldPath, $image) {
            $banner->save();
            $this->audit->record('banner.updated', $banner, $before, $banner->only(array_keys($before)), $admin);

            if ($image && $oldPath) {
                $this->images->delete($oldPath);
            }
        });

        return $banner;
    }

    public function delete(Banner $banner, Admin $admin): void
    {
        $path = $banner->image_path;
        $before = $banner->only(['title']);

        DB::transaction(function () use ($banner, $admin, $path, $before) {
            $banner->delete();
            $this->audit->record('banner.deleted', $banner, $before, null, $admin);
            $this->images->delete($path);
        });
    }
}
