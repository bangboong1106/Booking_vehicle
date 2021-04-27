<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\AppInfoRepository;
use Illuminate\Http\Request;

class PartnerGetStartedController extends BackendController
{
    public function __construct(AppInfoRepository $appInfoRepository) {
        parent::__construct();
        $this->setRepository($appInfoRepository);
        $this->setBackUrlDefault('partner-get-started.index');
        $this->setMenu('partner_get_started');
        $this->setTitle(trans('models.get_started.name'));
        $this->setMenu('partner_get_started');
    }

    public function _checkPermission($action = 'view') {
        if ( \Auth::user()->role != 'partner') {
            $this->_redirectToHome()->send();
        }
        return true;
    }

    public function redirect($id)
    {
        $data = $this->_getDataIndex();

        $entities = $this->getRepository()->getListForBackend($data);

        return view('backend.partner_get_started.redirect', [
            'id' => $id,
            'data' => $entities
        ]);
    }

    public function getLinkAppByMail(Request $request)
    {
        $params = $this->_getParams();

        if (!$this->getRepository()->getValidator()->validateCreate($params)) {
            return $this->_backToStart()->withErrors(trans('messages.send_mail_failed'));
        }

        $email = $request->email;

        $entities = $this->getRepository()->getListForBackend($this->_getDataIndex())->items();

        $data = [
            'data' => $entities
        ];

        if ($email) {
            Mail::send('layouts.backend.elements.email.link_app_by_mail', $data, function ($message) use ($email) {
                $message->to($email)->subject(config('constant.APP_NAME')." - Bộ ứng dụng quản trị vận tải");
                $message->from('report@'.config('constant.APP_COMPANY').'.com.vn', 'Phần mềm '. config('constant.APP_NAME'));
            });
        }

        return redirect()->route('partner_get-started.index')->with('success', trans('messages.send_mail_success') . $email);
    }

    public function redirectToPage($route)
    {
        return redirect()->route($route)->with('open_import_excel','open');
    }

    public function index()
    {
        $this->setViewData([
            'orders' => [ 3, 1, 2, 0]
        ]);

        return parent::index();
    }
}
