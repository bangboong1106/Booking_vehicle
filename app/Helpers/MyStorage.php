<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * Class FileService
 * @package App\Services
 */
class MyStorage
{
    public function __call($name, $arguments)
    {
        return call_user_func_array([Storage::class, $name], $arguments);
    }

    public function path($file)
    {
        return str_replace('/', '\\', storage_path($file));
    }

    /**
     * @param $fileName
     * @param $content
     * @return mixed
     */
    public function uploadToTmp($fileName, $content)
    {
        $newFilePath = getTmpUploadDir(date('Y-m-d')) . DIRECTORY_SEPARATOR . $fileName;
        $this->deleteTmpDaily();
        if ($this->isUploadFile($content)) {
            $r = Storage::putFileAs(getTmpUploadDir(date('Y-m-d')), $content, $fileName);
            if (!$r) {
                throw new  UploadException(trans('messages.file_upload_failed', ['file' => $newFilePath]));
            }
            return $newFilePath;
        }

        $r = $this->put($newFilePath, $content);
        if (!$r) {
            throw new UploadException(trans('messages.file_upload_failed', ['file' => $newFilePath]));
        }
        return $newFilePath;
    }

    /**
     * @param $fileName
     */
    public function download($fileName)
    {

    }


    public function url($fileName)
    {
        if (!$fileName) {
            return '';
        }
        if (strpos($fileName, 'http') !== false) {
            return $fileName;
        }
        $fileName = str_replace('\\', '/', $fileName);
        return urldecode(Storage::url($fileName));
    }

    public function withOutUrl($fileName)
    {
        $url = $this->url('__prefix__');
        $url = str_replace('__prefix__', '', $url);
        $fileName = str_replace($url, '', $fileName);
        return $fileName;
    }


    public function moveFromTmpToMedia($filePath, $newName = '')
    {
        if (!Storage::exists($filePath)) {
            throw new UploadException(trans('messages.file_does_not_exist', ['file' => $filePath]));
        }
        $newFilePath = getMediaDir($newName ? $newName : $filePath);
        $nameBackup = $newFilePath . '_' . time();
        if (Storage::exists($newFilePath)) {
            // rename
            Storage::move($newFilePath, $nameBackup);
        }
        try {
            $r = Storage::move($filePath, $newFilePath);
            if (!$r) {
                throw new UploadException(trans('messages.file_upload_failed', ['file' => $filePath]));
            }
            if (Storage::exists($nameBackup)) {
                // rename
                Storage::delete($nameBackup);
            }
            return $newFilePath;
        } catch (\Exception $exception) {
            // rollback
            if (Storage::exists($nameBackup)) {
                // rename
                Storage::move($nameBackup, $newFilePath);
            }
            throw $exception;
        }
    }

    public function put($file, $content)
    {
        if (!$this->isUploadFile($content)) {
            $content = $this->base64ToFile($content);
        }
        return Storage::put($file, $content);
    }

    public function base64ToFile($fileData)
    {
        @list($type, $fileData) = explode(';', $fileData);
        @list(, $fileData) = explode(',', $fileData);
        return base64_decode($fileData);
    }

    public function isUploadFile($data)
    {
        return $data instanceof UploadedFile;
    }

    /**
     * @return mixed
     */
    public function deleteTmpDaily()
    {
        $directory = getTmpUploadDir(today()->subDays(1)->format('Y-m-d'));
        return Storage::deleteDirectory($directory);
    }
}