<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\SystemConfig;
use App\Repositories\SystemConfigRepository;
use Exception;
use App\Model\Entities\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;




/**
 * Class SystemConfigController
 * @package App\Http\Controllers\Backend
 */
class CompanyInfoController extends BackendController
{

    public function __construct(SystemConfigRepository $systemConfigRepository)
    {
        parent::__construct();
        $this->setRepository($systemConfigRepository);
        $this->setMenu('setting');
        $this->setTitle(trans('models.company_info.name'));
    }

    public function index()
    {
        $obj = $this->getRepository()->where('key', 'like', 'company.%')->get();

        $companyName = $obj
            ->filter(function ($value, $key) {
                return $value->key == "company.name";
            });

        $companyAddress = $obj
            ->filter(function ($value, $key) {
                return $value->key == "company.address";
            });
        $companyEmail = $obj
            ->filter(function ($value, $key) {
                return $value->key == "company.email";
            });
        $companyMobileNo = $obj
            ->filter(function ($value, $key) {
                return $value->key == "company.mobile_no";
            });
        $companyStamp = $obj
            ->filter(function ($value, $key) {
                return $value->key == "company.stamp";
            })->first();

        $stamp_path = '';
        if (!empty($companyStamp)) {
            $stamp_path = route('file.getImage', [
                'id' => $companyStamp->value,
                'width' => 200,
                'height' => 200
            ]);
        }
        $this->setViewData([
            'companyName' => $companyName->isEmpty() ? 'Công ty ABC' : $companyName->first()->value,
            'companyEmail' => $companyEmail->isEmpty() ? 'abc@xyz' : $companyEmail->first()->value,
            'companyAddress' => $companyAddress->isEmpty() ? 'Việt Nam' : $companyAddress->first()->value,
            'companyMobileNo' => $companyMobileNo->isEmpty() ? '0999.999.999' : $companyMobileNo->first()->value,
            'companyStampPath' => $stamp_path
        ]);

        return parent::index();
    }


    // Upload con dấu của công ty
    // CreatedBy nlhoang 28/08/2020
    public function stamp(Request $request)
    {
        $location = 'media/stamp';
        $file = [];
        if ($request->ajax()) {
            if ($request->hasFile('file')) {
                $imageFiles = $request->file;

                $file = $this->_uploadFile($imageFiles, $location);

                if (SystemConfig::where('key', 'company.stamp')->exists()) {
                    $systemConfig = SystemConfig::firstOrNew(array('key' => 'company.stamp'));
                    $systemConfig->value = $file->file_id;
                    $systemConfig->upd_id = getCurrentUserId();
                    $systemConfig->upd_date = date("Y-m-d H:i:s");
                    $systemConfig->save();
                } else {
                    DB::table('system_config')->insert(
                        array(
                            'key' => 'company.stamp',
                            'value' => $file->file_id,
                            'ins_id' => getCurrentUserId(),
                            'ins_date' => date("Y-m-d H:i:s")
                        )
                    );
                }
            }
        }
        return $file;
    }

    // Lưu thông tin vào thư mục
    // CreatedBy nlhoang 28/08/2020
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
}
