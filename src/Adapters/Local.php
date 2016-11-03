<?php

namespace Flagrow\Upload\Adapters;

use Carbon\Carbon;
use Flagrow\Upload\Contracts\UploadAdapter;
use Flagrow\Upload\File;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Local implements UploadAdapter
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param File $file
     * @param UploadedFile $upload
     * @param string $contents
     * @return File
     */
    public function upload(File $file, UploadedFile $upload, $contents)
    {
        $today = (new Carbon())->toDateString();

        $file->path = sprintf(
            "%s/%s",
            $today,
            $file->base_name
        );

        if (!$this->filesystem->write(
            $upload->getPath(),
            $contents
        )
        ) {
            return false;
        }


        $file->url = $this->filesystem->getPathPrefix() . $file->path;

        return $file;
    }

    /**
     * In case deletion is not possible, return false.
     *
     * @param File $file
     * @return File|bool
     */
    public function delete(File $file)
    {
        if ($this->filesystem->delete($file->path)) {
            return $file;
        }

        return false;
    }

    /**
     * Whether the upload adapter works on a specific mime type.
     *
     * @param string $mime
     * @return bool
     */
    public function forMime($mime)
    {
        // We allow all, no checking.
        return true;
    }
}