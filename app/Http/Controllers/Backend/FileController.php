<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\File;
use App\Model\Entities\ImportHistory;
use App\Repositories\FileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class FileController extends BackendController
{
    /**
     * UploadController constructor.
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        parent::__construct();
        $this->setRepository($fileRepository);
        $this->setBackUrlDefault('dashboard.index');
    }

    public function uploadFile(Request $request)
    {
        $file = $this->upload($request);
        return response()->json([
            'id' => $file->file_id,
            'path' => $file->path
        ]);
    }

    public function destroy($id, $action = 'delete')
    {
        /* $isValid = $this->getRepository()->getValidator()->validateDestroy($id);
         if (!$isValid) {
             return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
         }*/
        DB::beginTransaction();
        try {
            $entity = $this->getRepository()->search(['file_id_eq' => $id])->first();
            call_user_func_array([$entity, $action], []);
            if ($action == 'delete') {
                Storage::delete($entity->path);
            }
            DB::commit();
            $this->fireEvent('after_destroy', $entity);
            return response()->json([
                'message' => trans('messages.delete_success'),
            ]);
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.delete_failed'));
    }


    public function upload(Request $request)
    {
        $location = 'tmp_file_upload';

        $files = [];
        if ($request->ajax()) {
            if ($request->hasFile('file')) {
                $imageFiles = $request->file;

                if (is_array($imageFiles)) {
                    foreach ($imageFiles as $file) {
                        $files[] = $this->_uploadFile($file, $location);
                    }
                } else {
                    $files = $this->_uploadFile($imageFiles, $location);
                }
            }
        }
        return $files;
    }

    public function uploadImportFile($file, $total = 0, $ignoreCount = 0, $update = false, $module = null)
    {
        if (empty($file)) return;

        $importFile = $this->_uploadFile($file, 'imported_file');
        $importHistory = new ImportHistory();
        $importHistory->file_id = $importFile->file_id;
        $importHistory->module = $module;
        $importHistory->type = $update ? 'update' : 'create';
        $importHistory->success_record = $total - $ignoreCount;
        $importHistory->error_record = $ignoreCount;
        $importHistory->save();
    }

    public function getImage($id)
    {
        $width = Request::get('width', 120);
        $height = Request::get('height', 120);
        $full = Request::get('full', false);

        $image = $this->getRepository()->search(['file_id_eq' => $id])->first();
        if ($image != null) {
            $file_types = config('system.file_type');
            $imagePath = $image->path;
            $file_type = $image->file_type;
            foreach ($file_types as $key => $value) {
                if (strrpos($value, $image->file_type)) {
                    switch ($key) {
                        case 2:
                            $imagePath = 'css/backend/img/excel.png';
                            $file_type = 'png';
                            break;
                        case 3:
                            $imagePath = 'css/backend/img/word.png';
                            $file_type = 'png';
                            break;
                        case 4:
                            $imagePath = 'css/backend/img/pdf.png';
                            $file_type = 'png';
                            break;
                        default:
                            $imagePath = $image->path;
                            $file_type = $image->file_type;
                            break;
                    }
                }
            }
            if (!Storage::disk('public')->exists($imagePath)) {
                return Image::make(public_path('css/backend/images/no-image.png'))->fit($width, $height)->response();
            }
        } else {
            return Image::make(public_path('css/backend/images/no-image.png'))->fit($width, $height)->response();
        }


        $response = $full ? Image::make(public_path($imagePath)) : Image::make(public_path($imagePath))->fit($width, $height);
        return $response->response($file_type);
    }

    protected function _uploadFile(UploadedFile $file, $location = '')
    {
        if (!$file->isValid()) {
            return false;
        }

        $destinationFileName = round(microtime(true)) . '_' . $file->getClientOriginalName();
        $file->storeAs($location, $destinationFileName);

        $fileStorage = new File();
        $fileStorage->file_name = $file->getClientOriginalName();
        $fileStorage->file_type = $file->getClientOriginalExtension();
        $fileStorage->mime = $file->getMimeType();
        $fileStorage->path = $location . '/' . $destinationFileName;
        $fileStorage->size = $file->getSize();
        $fileStorage->file_id = (string)Str::uuid();
        $fileStorage->save();

        return $fileStorage;
    }

    public function moveFileFromTmpToMedia($fileId, $folder)
    {
        $image = $this->getRepository()->getFileWithID($fileId);
        if (!empty($image)) {
            $oldPath = $image->path;
            $uuid = (string)Str::uuid();
            $newPath = 'media' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $uuid . '_' . $image->file_name;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
            }

            $image->path = $newPath;
            $image->is_confirmed = 1;
            $image->save();
        }
        return $image;
    }

    public function downloadFile($id)
    {
        $message = 'Vui lòng kiểm tra lại tệp tin. Đường dẫn tệp tin không tồn tại';
        $file = $this->getRepository()->search(['file_id_eq' => $id])->first();
        if ($file != null) {

            $file_path = public_path($file->path);
            if (file_exists(($file_path))) {
                return Response::download(($file_path), $file->file_name, [
                    'Content-Length: ' . $file->size
                ]);
            } else {
                exit($message);
            }
        } else {
            exit($message);
        }
    }
}
