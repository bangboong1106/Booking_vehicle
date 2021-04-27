<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\Template;
use App\Model\Entities\TemplateLayout;
use App\Repositories\Base\CustomRepository;
use App\Validators\TemplateValidator;
use Illuminate\Support\Facades\Auth;

class PartnerTemplateRepository extends CustomRepository
{
    function model()
    {
        return Template::class;
    }

    public function validator()
    {
        return TemplateValidator::class;
    }


    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));
        
        $query['partner_id_eq'] = Auth::user()->partner_id;

        $queryBuilder = $this->search($query, [], true)->with(['getFile']);
        return $queryBuilder->paginate($perPage);
    }

    // Lấy mẫu trộn theo Id
    // CreatedBy nlhoang 04/04/2020
    public function getTemplateByTemplateId($templateId)
    {
        return Template::where('id', $templateId)->first();
    }

    // Lấy danh sách mẫu trộn theo người dùng
    // CreatedBy nlhoang 04/04/2020
    public function getTemplateByUserId($userId, $type)
    {
        return Template::where('type', $type)->where('partner_id', Auth::user()->partner_id)->orderBy('ins_date', 'desc')->get();
    }

    // Lấy danh sách trường trộn của mẫu
    // CreatedBy nlhoang 05/04/2020
    public function getTemplateLayoutByType($type)
    {
        return TemplateLayout::where('type', $type)
            ->where('del_flag', 0)
            ->orderBy('sort_order')
            ->get();
    }


    // Lấy danh sách trường trộn của mẫu
    // CreatedBy nlhoang 05/04/2020
    public function getTemplateLayouts()
    {
        return TemplateLayout::where('del_flag', 0)->get();
    }

}