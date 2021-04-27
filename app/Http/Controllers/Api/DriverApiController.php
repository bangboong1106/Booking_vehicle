<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\DriverLocationLog;
use App\Repositories\DriverConfigFileRepository;
use App\Repositories\DriverFileRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\VersionReviewRepository;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Mockery\Exception;
use Spatie\Geocoder\Geocoder;
use Validator;

class DriverApiController extends ApiController
{

    protected $driverRepos;
    protected $fileRepos;
    protected $driverFileRepos;
    protected $driverConfigFileRepos;
    protected $vehicleRepos;
    protected $versionReviewRepos;

    public function getDriverRepos()
    {
        return $this->driverRepos;
    }

    public function setDriverRepos($driverRepos)
    {
        $this->driverRepos = $driverRepos;
    }

    public function getFileRepos()
    {
        return $this->fileRepos;
    }

    public function setFileRepos($fileRepos)
    {
        $this->fileRepos = $fileRepos;
    }

    public function getDriverFileRepos()
    {
        return $this->driverFileRepos;
    }

    public function setDriverFileRepos($driverFileRepos)
    {
        $this->driverFileRepos = $driverFileRepos;
    }

    public function getDriverConfigFileRepos()
    {
        return $this->driverConfigFileRepos;
    }

    public function setDriverConfigFileRepos($driverConfigFileRepos)
    {
        $this->driverConfigFileRepos = $driverConfigFileRepos;
    }

    public function getVehicleRepos()
    {
        return $this->vehicleRepos;
    }

    public function setVehicleRepos($vehicleRepos)
    {
        $this->vehicleRepos = $vehicleRepos;
    }

    public function getVersionReviewRepos()
    {
        return $this->versionReviewRepos;
    }

    public function setVersionReviewRepos($versionReviewRepos)
    {
        $this->versionReviewRepos = $versionReviewRepos;
    }

    public function __construct(
        DriverRepository $driverRepository,
        FileRepository $fileRepository,
        DriverFileRepository $driverFileRepository,
        DriverConfigFileRepository $driverConfigFileRepository,
        VehicleRepository $vehicleRepository,
        VersionReviewRepository $versionReviewRepository
    ) {
        parent::__construct();
        $this->setDriverRepos($driverRepository);
        $this->setFileRepos($fileRepository);
        $this->setDriverFileRepos($driverFileRepository);
        $this->setDriverConfigFileRepos($driverConfigFileRepository);
        $this->setVehicleRepos($vehicleRepository);
        $this->setVersionReviewRepos($versionReviewRepository);
    }

    public function updateUserInfo(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'fullName' => '',
                'phone' => '',
                'sex' => '',
                'birthday' => 'date_format:Y-m-d',
                'address' => '',
                'description' => '',
                'email' => '',
                'avatarFileId' => '',
                'files' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $userId = Auth::User()->id;
                $userObj = $this->getDriverRepos()->getFullInfoDriverWithUserId($userId);
                $userObj->mobile_no = $request['phone'];
                $userObj->birth_date = $request['birthday'];
                $userObj->address = $request['address'];
                $userObj->note = $request['description'];
                $userObj->sex = $request['sex'];
                $userObj->full_name = $request['fullName'];

                $avatarUrl = '';
                $user = $userObj->tryGet('adminUser');
                $avatarId = $user->avatar_id;

                if (!empty($request['avatarFileId'])) {
                    $avatarId = $request['avatarFileId'];
                    $result = app('App\Http\Controllers\Api\FileApiController')->moveFileFromTmpToMedia($avatarId, 'avatars');
                    $avatarUrl = getenv('BASE_IMAGE_URL', '') . $result->path;
                } else {
                    $avatarUrl = app('App\Http\Controllers\Api\FileApiController')->getImageUrl($avatarId);
                }
                if (null != $user && '' != $user->id) {
                    $user->email = $request['email'];
                    $user->avatar_id = $avatarId;
                    $user->save();
                }

                $filesObj = $request['files'];
                if (null != $filesObj && !empty($filesObj)) {
                    $this->getDriverFileRepos()->deleteWhere([
                        'driver_id' => $userObj->id
                    ]);
                    foreach ($filesObj as $fileObj) {
                        $files = $fileObj['files'];
                        if (null != $files && !empty($files)) {
                            foreach ($files as $file) {
                                $entity = $this->getDriverFileRepos()->findFirstOrNew([]);
                                $entity->driver_id = $userObj->id;
                                $entity->driver_config_file_id = $fileObj['id'];
                                if (!empty($fileObj['expireDate'])) {
                                    $entity->expire_date = $fileObj['expireDate'];
                                }
                                if (!empty($fileObj['registerDate'])) {
                                    $entity->register_date = $fileObj['registerDate'];
                                }
                                $entity->file_id = $file['fileId'];
                                $entity->save();
                                $image = app('App\Http\Controllers\Api\FileApiController')->moveFileFromTmpToMedia($file['fileId'], 'drivers');
                            }
                        } else {
                            $entity = $this->getDriverFileRepos()->findFirstOrNew([]);
                            $entity->driver_id = $userObj->id;
                            $entity->driver_config_file_id = $fileObj['id'];
                            if (!empty($fileObj['expireDate'])) {
                                //                                $entity->expire_date = $fileObj['expireDate'];
                                $entity->expire_date = DateTime::createFromFormat('Y-m-d', $fileObj['expireDate'])->format('Y-m-d');
                            }
                            if (!empty($fileObj['registerDate'])) {
                                //                                $entity->register_date = $fileObj['registerDate'];
                                $entity->register_date = DateTime::createFromFormat('Y-m-d', $fileObj['registerDate'])->format('Y-m-d');
                            }
                            $entity->save();
                        }
                    }
                }

                $userObj->save();
                $driverFiles = $this->getDriverFiles($userId);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'fullName' => $userObj->full_name,
                        'sex' => $userObj->sex,
                        'birthday' => $userObj->birth_date,
                        'phone' => $userObj->mobile_no,
                        'email' => $userObj->tryGet('adminUser')->email,
                        'address' => $userObj->address,
                        'avatarUrl' => $avatarUrl,
                        'files' => $driverFiles
                    ]
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function userInfoEnquiry(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), []);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $userId = Auth::User()->id;
                $userObj = $this->getDriverRepos()->getFullInfoDriverWithUserId($userId);

                $avatarUrl = '';
                //                $avatarId = $userObj->tryGet('adminUser')->avatar_id;
                if (!empty($userObj->avatar_id)) {
                    $avatarUrl = app('App\Http\Controllers\Api\FileApiController')->getImageUrl($userObj->avatar_id);
                }

                $driverFiles = $this->getDriverFiles($userId);

                // Bổ sung thêm đoạn check Reviewed
                $version = $this->getVersionReviewRepos()->getCurrentVersionReview();

                $driveRes = [
                    'fullName' => $userObj->full_name,
                    'sex' => $userObj->sex,
                    'birthday' => $userObj->birth_date,
                    'phone' => $userObj->mobile_no,
                    'email' => $userObj->tryGet('adminUser')->email,
                    'address' => $userObj->address,
                    'avatarUrl' => $avatarUrl,
                    'files' => $driverFiles,
                    'version' => isset($version) ? $version->version : ''
                ];
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $driveRes
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    private function getDriverFiles($userId)
    {
        $driverConfigList = $this->getDriverConfigFileRepos()->getAll();
        if (!$driverConfigList->isEmpty()) {
            $driver = $this->getDriverRepos()->getDriverByUserId($userId);
            if (!empty($driver)) {
                foreach ($driverConfigList as $fileConfig) {
                    $driverFiles = $this->getDriverFileRepos()->getDriverFile($driver->id, $fileConfig->id);
                    $files = array();
                    if (!empty($driverFiles) && count($driverFiles) > 0) {
                        foreach ($driverFiles as $file) {
                            if (!empty($file->file_id)) {
                                $files[] = [
                                    'fileId' => $file->file_id,
                                    'fileUrlImage' => app('App\Http\Controllers\Api\FileApiController')->getImageUrl($file->file_id),
                                    'registerDate' => $file->register_date,
                                    'expireDate' => $file->expire_date
                                ];
                            }
                            $fileConfig->registerDate = $file->register_date;
                            $fileConfig->expireDate = $file->expire_date;
                        }
                    }
                    $fileConfig->files = $files;
                }
            }
        }
        return $driverConfigList;
    }

    public function updateUserAvatar(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'avatar' => 'required|max:10240|mimes:jpeg,jpg,png,gif'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $result = app('App\Http\Controllers\Api\FileApiController')->uploadAvatar($request);
                if (!empty($result) && 0 < $result->getData()->id && !empty($result->getData()->path)) {
                    $url = getenv('BASE_IMAGE_URL', '') . $result->getData()->path;
                    $res = [
                        'avatarUrl' => $url
                    ];

                    return response()->json([
                        'errorCode' => HttpCode::EC_OK,
                        'errorMessage' => '',
                        'data' => $res
                    ]);
                } else {
                    return response()->json([
                        'errorCode' => HttpCode::EC_UPLOAD_FILE_ERROR,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_UPLOAD_FILE_ERROR)
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

    public function updateReadyStatus(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $userId = Auth::User()->id;
                $driver = $this->getDriverRepos()->getDriverByUserId($userId);
                $driver->ready_status = $request['status'];
                $driver->save();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => 'OK',
                    'data' => []
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    // Update location xe cho android
    // Áp dụng cho các xe ko có gpsId
    public function updateLocation(Request $request)
    {
        try {
            $userId = Auth::User()->id;
            $latitude = $request['latitude'];
            $longitude = $request['longitude'];
            $current_location = $request['current_location'];

            $driver = $this->getDriverRepos()->getDriverByUserId($userId);
            if ($driver != null) {
                $vehicleIds = $this->getVehicleRepos()->getVehicleWithoutGPSByDriverId($driver->id);
                if (isset($vehicleIds) && 0 < sizeof($vehicleIds)) {
                    DB::beginTransaction();
                    foreach ($vehicleIds as $id) {
                        $vehicle = $this->getVehicleRepos()->getItemById($id);
                        $vehicle->latitude = $latitude;
                        $vehicle->longitude = $longitude;
                        $vehicle->current_location = $current_location;
                        $vehicle->save();
                    }
                    DB::commit();
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'message' => 'ok'
                ]
            ]);
        } catch (Exception $exception) {
            logError($exception);
            DB::rollBack();
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    // Update location xe cho iOs
    // Áp dụng cho các xe ko có gpsId
    public function updateLocationDirectly(Request $request)
    {
        try {
            $driverLocationLog = new DriverLocationLog();
            //            $driverLocationLog->d_uniqueID = $request['device']['uniqueID'];
            $driverLocationLog->request_data = $request->getContent();
            $driverLocationLog->save();

            $userId = Auth::User()->id;
            $location = $request['location'];
            if (null != $location) {
                $latitude = $location[0]['coords']['latitude'];
                $longitude = $location[0]['coords']['longitude'];
                // TODO: Loại bỏ gọi google api để lấy address
                $current_location = '';
                //                $current_location = $this->getAddressFromCoordinates($latitude, $longitude);
                $driver = $this->getDriverRepos()->getDriverByUserId($userId);
                if ($driver != null) {
                    $vehicleIds = $this->getVehicleRepos()->getVehicleWithoutGPSByDriverId($driver->id);
                    if ($vehicleIds != null && 0 < sizeof($vehicleIds)) {
                        $vehicles = $this->getVehicleRepos()->getItemsByIds($vehicleIds);
                        if ($vehicles != null) {
                            foreach ($vehicles as $vehicle) {
                                DB::beginTransaction();
                                $vehicle->latitude = $latitude;
                                $vehicle->longitude = $longitude;
                                //                                $vehicle->current_location = $current_location ? $current_location['formatted_address'] : '';
                                $vehicle->current_location = '';
                                $vehicle->save();
                                DB::commit();
                            }
                        }
                    }
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'message' => 'ok'
                ]
            ]);
        } catch (Exception $exception) {
            logError($exception);
            DB::rollBack();
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    protected function getAddressFromCoordinates($latitude, $longitude)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $geocoder = new Geocoder($client);
            $geocoder->setApiKey(env('GOOGLE_MAP_API_KEY', ''));

            return $geocoder->getAddressForCoordinates($latitude, $longitude);
        } catch (Exception $exception) {
            logError($exception);
            return '';
        }
    }
}
