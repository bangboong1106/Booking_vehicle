<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\SystemCodeConfig;
use App\Repositories\SystemCodeConfigRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Exception;

/**
 * Class SystemCodeConfigController
 * @package App\Http\Controllers\Backend
 */
class SystemCodeConfigController extends BackendController
{

    public function __construct(SystemCodeConfigRepository $systemCodeConfigRepository)
    {
        parent::__construct();
        $this->setRepository($systemCodeConfigRepository);
        $this->setBackUrlDefault('system-code-config.index');
        $this->setConfirmRoute('system-code-config.confirm');
        $this->setMenu('setting');
        $this->setTitle(trans('models.system_code_config.name'));
    }

    public function _prepareIndex()
    {
        $types = config('system.system_code_type');
        $this->setViewData([
            'types' => $types
        ]);
    }

    public function _prepareForm()
    {
        $types = config('system.system_code_type');
        $this->setViewData([
            'types' => $types
        ]);
    }

    /**
     * @param $type
     * @param null $id
     * @param bool $active : Đối với chuyến ,BDM ,DHKH sinh tự động thì lấy mã sẽ active đã sử dụng luôn
     * TH sinh chuyến,BDM,DHKH tự động 2 user cùng lấy mã thì user2 sẽ chờ user1 lấy mã xong
     * @return null|string
     *
     */
    public function generateSystemCode($type, $id = null, $active = false)
    {
        try {
            DB::beginTransaction();
            $date = '';

            $systemCodeConfig = null;
            if ($id != null) {
                $systemCodeConfig = $this->getRepository()->findFirstOrNew(['id' => $id]);
            } else
                $systemCodeConfig = $this->getRepository()->getSystemCodeConfig($type, $active);
            if ($systemCodeConfig == null)
                return Str::random(8);

            if ($systemCodeConfig->is_generate_time)
                $date = AppConstant::convertDate(now()->toDateString(), 'ymd');

            $prefix = $systemCodeConfig->prefix;
            $suffix_length = $systemCodeConfig->suffix_length;

            $last_suffix = 0;
            if ($systemCodeConfig->end_suffix) {
                $last_suffix = $systemCodeConfig->end_suffix;
            }

            $suffix = null;
            $code = null;
            do {
                $last_suffix = $last_suffix + 1;
                $suffix = sprintf('%0' . $suffix_length . 'd', $last_suffix);
                $code = $prefix . $date . $suffix;
                $condition = $this->existSystemCode($code, $type);

            } while ($condition);
            $systemCodeConfig->code_tmp = $code;
            $systemCodeConfig->suffix_tmp = $suffix;
            if ($active) {
                $systemCodeConfig->end_suffix = $suffix;
            }
            $systemCodeConfig->save();

            DB::commit();

            return $code;

        } catch (Exception $e) {
            logError($e);
            DB::rollBack();
            return Str::random(8);
        }
    }

    public function generateSystemCodeForExcels($type, $numberCode, $active = false)
    {
        $date = '';
        $systemCodeList = [];
        try {
            DB::beginTransaction();

            $systemCodeConfig = $this->getRepository()->getSystemCodeConfig($type);
            if ($systemCodeConfig->is_generate_time)
                $date = AppConstant::convertDate(now()->toDateString(), 'ymd');

            if ($systemCodeConfig == null) {
                for ($i = 0; $i < $numberCode; $i++) {
                    $systemCodeList[] = Str::random(8);
                }
                return $systemCodeList;
            }

            $prefix = $systemCodeConfig->prefix;
            $suffix_length = $systemCodeConfig->suffix_length;

            $last_suffix = 0;
            if ($systemCodeConfig->end_suffix) {
                $last_suffix = $systemCodeConfig->end_suffix;
            }

            $suffix = null;
            $code = null;
            for ($i = 0; $i < $numberCode; $i++) {
                do {
                    $last_suffix = $last_suffix + 1;
                    $suffix = sprintf('%0' . $suffix_length . 'd', $last_suffix);
                    $code = $prefix . $date . $suffix;
                    if ($i == 0)
                        $condition = $this->existSystemCode($code, $type);
                    else
                        $condition = false;
                } while ($condition);
                $systemCodeList[] = $code;
            }

            $systemCodeConfig->code_tmp = $code;
            $systemCodeConfig->suffix_tmp = $suffix;
            if ($active) {
                $systemCodeConfig->end_suffix = $suffix;
            }
            $systemCodeConfig->save();

            DB::commit();

            return $systemCodeList;

        } catch (Exception $e) {
            logError($e);
            DB::rollBack();
            for ($i = 0; $i < $numberCode; $i++) {
                $systemCodeList[] = Str::random(8);
            }
            return $systemCodeList;
        }

    }

    public function existSystemCode($code, $type)
    {
        $generateSystemCodeConfig = AppConstant::getListGenerateSystemCodeConfig();
        $conditionType = false;
        foreach ($generateSystemCodeConfig as $config) {
            if ($config['type'] == $type) {
                $conditionType = $this->getRepository()->existSystemCode($config['table'], $config['attribute'], $code);
                break;
            }
        }
        if ($conditionType)
            return true;
        return false;
    }

    public function getCode()
    {
        $code = "";
        try {
            $id = Request::get('id');
            if (isset($id)) {
                $code = $this->generateSystemCode(config('constant.sc_order'), $id);
            } else {
                $code = $this->generateSystemCode(config('constant.sc_order'));
            }
        } catch (Exception $e) {
            logError($e);
        }

        $html = [
            "code" => $code
        ];
        $this->setData($html);
        return $this->renderJson();
    }

    public function getCodeConfig()
    {
        $data = SystemCodeConfig::select("id", "prefix as title",
            DB::raw('concat(system_code_config.prefix,"",LPAD("1", system_code_config.suffix_length,"0")) as preview'))
            ->where('type', '=', config('constant.sc_order'))
            ->where('prefix', 'LIKE', '%' . request('q') . '%')
            ->orderBy('prefix', 'asc')
            ->paginate(PHP_INT_MAX);
        return response()->json($data);
    }

}
