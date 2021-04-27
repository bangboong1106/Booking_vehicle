<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 7/23/18
 * Time: 19:31
 */

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\FileRepository;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Intervention\Image\Facades\Image;
use Storage;
use App\Model\Entities\File;

use Validator;
use Illuminate\Support\Str;

class FileApiController extends ApiController
{
    public function __construct(FileRepository $fileRepository)
    {
        parent::__construct();
        $this->setRepository($fileRepository);
    }

    public function upload(Request $request, $location)
    {
        $files = [];
        if ($request->hasFile('file')) {
            $imageFiles = $request->file();

            if (is_array($imageFiles) && sizeof($imageFiles) > 1) {
                foreach ($imageFiles as $file) {
                    $files[] = $this->_uploadFile($file, $location);
                }
            } else {
                $files = $this->_uploadFile(reset($imageFiles), $location);
            }
        }
        return $files;
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
        $fileStorage->size = $file->getSize();
        $fileStorage->path = $location . '/' . $destinationFileName;
        $fileStorage->file_id = (string)Str::uuid();
        $fileStorage->save();

        return $fileStorage;
    }

    public function getImage($fileId)
    {
        $width = Request::get('width', 120);
        $height = Request::get('height', 120);
        $full = Request::get('full', false);

        $image = $this->getRepository()->search(['file_id_eq' => $fileId])->first();
        $file_types = config('system.file_type');
        $imagePath = '';
        $file_type = '';
        foreach ($file_types as $key => $value) {
            if (strrpos($value, $image->file_type)) {
                switch ($key) {
                    case 1:
                        $imagePath = $image->path;
                        $file_type = $image->file_type;
                        break;
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
                }
                break;
            }
        }
        if ($full) {
            $response = Image::make(public_path($imagePath));
        } else {
            $response = Image::make(public_path($imagePath))->fit($width, $height);
        }

        return $response->response($file_type);
    }

    public function getImageUrl($fileId)
    {
        $image = $this->getRepository()->search(['file_id_eq' => $fileId])->first();
        $imagePath = empty($image) ? "" : AppConstant::getImagePath($image->path, $image->file_type);
        return $imagePath;
    }

    public function uploadFiles(Request $request)
    {
        try {
            $validationType = '';
            $folder = DIRECTORY_SEPARATOR;
            if (!empty($request->typeUpload)) {
                switch ($request->typeUpload) {
                    case AppConstant::UPLOAD_FILE_TYPE_IMAGE:
                        $validationType = '|max:10240|mimes:jpeg,jpg,png,gif';
                        $folder = $folder . 'images';
                        break;
                    case AppConstant::UPLOAD_FILE_TYPE_DOCUMENT:
                        $validationType = '|max:20480|mimes:xls,xlsx,doc,docx,pdf';
                        $folder = $folder . 'documents';
                        break;
                    default:
                        $validationType = '';
                        $folder = $folder . '';
                        break;
                }
            }

            $validation = Validator::make($request->all(), [
//                'file' => 'required' . $validationType,
//                'typeUpload' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $file = $this->upload($request, 'tmp_file_upload' . $folder);
                if (!empty($file) && !empty($file->file_id)) {
                    $res = [
                        'fileId' => $file->file_id
                    ];

                    return response()->json([
                        'errorCode' => HttpCode::EC_OK,
                        'errorMessage' => '',
                        'data' => $res
                    ]);
                } else {
                    return response()->json([
                        'errorCode' => HttpCode::EC_UPLOAD_FILE_ERROR,
                        'errorMessage' => HttpCode::EM_UPLOAD_FILE_ERROR
                    ]);
                }
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    function moveFileFromTmpToMedia($fileId, $folder)
    {
        $image = $this->getRepository()->getFileWithID($fileId);
        if (!empty($image)) {
            $oldPath = $image->path;
            $uuid = (string)Str::uuid();
            $newPath = 'media' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $uuid . '_' . $image->file_name;
            if (!Storage::exists($newPath)) {
                Storage::move($oldPath, $newPath);
            }

            $image->path = $newPath;
            $image->is_confirmed = 1;
            $image->save();
        }
        return $image;
    }

    public function getFile($fileId)
    {
        $rsFile = null;
        $file = $this->getRepository()->search(['file_id_eq' => $fileId])->first();
        if (!empty($file) && !empty($file->path)) {
            $file_types = config('system.file_type');
            foreach ($file_types as $key => $value) {
                if (strrpos(strtolower($value), strtolower($file->file_type))) {
                    $rsFile['type'] = $key;
                    $rsFile['url'] = env('BASE_IMAGE_URL', '') . $file->path;
                    $rsFile['name'] = $file->file_name;
                    switch ($key) {
                        case 1:
                            $thumbnail = getenv('BASE_IMAGE_URL', '') . $file->path;
                            break;
                        case 2:
                            $thumbnail = getenv('BASE_IMAGE_URL', '') . 'css/backend/img/excel.png';
                            break;
                        case 3:
                            $thumbnail = getenv('BASE_IMAGE_URL', '') . 'css/backend/img/word.png';
                            break;
                        case 4:
                            $thumbnail = getenv('BASE_IMAGE_URL', '') . 'css/backend/img/pdf.png';
                            break;
                        default:
                            $thumbnail = getenv('BASE_IMAGE_URL', '') . $file->path;
                    }
                    $rsFile['thumbnail'] = $thumbnail;
                    $rsFile['insDate'] = $file->ins_date;
                    break;
                }
            }
        }
        return $rsFile;
    }
}