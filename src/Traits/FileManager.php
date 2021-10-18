<?php

namespace Jiannius\Atom\Traits;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

trait FileManager
{
	/**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootFileManager()
    {
        static::saving(function($file) {
            $file->name = substr($file->name, 0, 200);
        });

        static::deleting(function($file) {
            if ($path = $file->data->path ?? null) {
                if (env('APP_ENV') !== 'production' && Str::startsWith($path, 'prod/')) {
                    abort(500, 'Do not delete production file in ' . env('APP_ENV') . ' environment!');
                }
                else {
                    (self::getStorage())->delete($path);
                }
            }
        });
    }

    /**
     * Scope for is image
     * 
     * @param Builder $query
     * @param boolean $bool
     * @return Builder
     */
    public function scopeIsImage($query, $bool)
    {
        if ($bool === true) return $query->where('mime', 'like', 'image/%');
        if ($bool === false) return $query->where('mime', 'not like', 'image/%');
    }

    /**
     * Get is image attribute
     * 
     * @return boolean
     */
    public function getIsImageAttribute()
    {
        return Str::startsWith($this->mime, 'image/');
    }

    /**
     * Get size attribute
     * 
     * @param float $size
     * @return string
     */
    public function getSizeAttribute($size)
    {
        if ($size <= 0) return null;
        else if ($size < 1) return round($size * 1000, 2) . ' KB';
        else return round($size, 2) . ' MB';
    }

    /**
     * Get file type attribute
     * 
     * @return string
     */
    public function getTypeAttribute()
    {
        if ($this->mime === 'youtube') return $this->mime;

        $type = (explode('/', $this->mime))[1];

        if ($type === 'ld+json') return 'jsonld';
        if ($type === 'svg+xml') return 'svg';
        if ($type === 'plain') return 'txt';
        if (in_array($type, ['msword', 'vnd.openxmlformats-officedocument.wordprocessingml.document'])) return 'word';
        if (in_array($type, ['vnd.ms-powerpoint', 'vnd.openxmlformats-officedocument.presentationml.presentation'])) return 'ppt';
        if (in_array($type, ['vnd.ms-excel', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) return 'excel';

        return $type;
    }

    /**
     * Upload file to digital ocean
     * and create a file entry in db
     * 
     * @return File
     */
    public static function upload()
    {
        if (request()->has('upload.files')) {
            return self::saveFromUploadedFiles(request()->file('upload.files'));
        }
        else if (request()->has('upload.urls')) {
            return self::saveFromImageUrls(request()->input('upload.urls'));
        }
        else if (request()->has('upload.youtube')) {
            return self::saveFromYoutube(request()->input('upload.youtube'));
        }
    }

    /**
     * Get the storage instace
     * 
     * @return Storage
     */
    public static function getStorage()
    {
        config([
            'filesystems.disks.do' => [
                'driver' => 's3',
                'key' => env('DO_SPACES_KEY'),
                'secret' => env('DO_SPACES_SECRET'),
                'region' => env('DO_SPACES_REGION'),
                'bucket' => env('DO_SPACES_BUCKET'),
                'endpoint' => env('DO_SPACES_ENDPOINT'),
            ],
        ]);

        return Storage::disk('do');
    }

    /**
     * Save from uploaded files
     * 
     * @param UploadedFile $inputs
     * @return File
     */
    public static function saveFromUploadedFiles($inputs)
    {
        $ids = [];

        foreach ($inputs as $input) {
            $data = self::compressFile($input);
            $file = File::create([
                'name' => $data->name,
                'mime' => $data->mime,
                'size' => $data->size,
                'url' => $data->url,
                'data' => [
                    'path' => $data->path,
                    'dimension' => $data->dimension,
                ],
            ]);

            array_push($ids, $file->id);
        }

        return File::whereIn('id', $ids)->get();
    }

    /**
     * Save image from web url
     * 
     * @param array $urls
     * @return File
     */
    public static function saveFromImageUrls($urls)
    {
        $ids = [];

        foreach ($urls as $url) {
            $img = Image::make($url);
            $mime = $img->mime();

            $file = File::create([
                'name' => $url,
                'mime' => $mime,
                'url' => $url,
                'data' => [
                    'dimension' => $img->width() . 'x' . $img->height(),
                ],
            ]);

            array_push($ids, $file->id);
        }

        return File::whereIn('id', $ids)->get();
    }

    /**
     * Save from youtube video id
     * 
     * @param array $vids
     * @return File
     */
    public static function saveFromYoutube($vids)
    {
        $ids = [];

        foreach ($vids as $vid) {
            $file = File::create([
                'name' => $vid,
                'mime' => 'youtube',
                'url' => 'https://www.youtube.com/embed/' . $vid,
                'data' => [
                    'vid' => $vid,
                ],
            ]);

            array_push($ids, $file->id);
        }

        return File::whereIn('id', $ids)->get();
    }

    /**
     * Compress file
     * 
     * @param UploadedFile $input
     * @return object
     */
    public static function compressFile($input)
    {
        $storage = self::getStorage();
        $name = $input->getClientOriginalName();
        $mime = $input->getClientMimeType();
        $size = $input->getSize();
        $path = $input->path();

        if (Str::startsWith($mime, 'image/')) {
            $img = Image::make($path);

            $img->resize(1200, 1200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save(null, 60);

            clearstatcache();

            $size = $img->filesize();
            $dimension = $img->width() . 'x' . $img->height();
        }

        $dopath = $storage->putFile(env('DO_SPACES_FOLDER'), $path, 'public');

        return (object)[
            'name' => $name,
            'mime' => $mime,
            'size' => round($size/1024/1024, 5),
            'url' => env('DO_SPACES_CDN') . '/' . $dopath,
            'path' => $dopath,
            'dimension' => $dimension ?? null,
        ];
    }
}