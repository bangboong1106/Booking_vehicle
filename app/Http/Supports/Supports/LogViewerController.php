<?php

namespace App\Http\Supports;

use App\Http\Controllers\Base\BaseController;
use App\Services\LogViewerService;
use Illuminate\Support\Facades\Request;

class LogViewerController extends BaseController
{
    public $request;

    public function __construct()
    {
        $this->request = app('request');
        parent::__construct();
    }

    public function index()
    {
        if ($this->request->input('l')) {
            LogViewerService::setFile(base64_decode($this->request->input('l')));
        }

        if ($this->request->input('dl')) {
            return $this->download(LogViewerService::pathToLogFile(base64_decode($this->request->input('dl'))));
        }
        if ($this->request->has('del')) {
            app('files')->delete(LogViewerService::pathToLogFile(base64_decode($this->request->input('del'))));
            return $this->redirect(route('frontend.log.viewer', ['password' => Request::get('password')]));
        }
        if ($this->request->has('delall')) {
            foreach (LogViewerService::getFiles(true) as $file) {
                app('files')->delete(LogViewerService::pathToLogFile($file));
            }
            return $this->redirect(route('frontend.log.viewer', ['password' => Request::get('password')]));
        }

        return $this->render('frontend.log-viewer.index',[
            'logs' => LogViewerService::all(),
            'files' => LogViewerService::getFiles(true),
            'current_file' => LogViewerService::getFileName()
        ]);
    }


    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }

        return app('redirect')->to($to);
    }

    private function download($data)
    {
        if (function_exists('response')) {
            return response()->download($data);
        }

        // For laravel 4.2
        return app('\Illuminate\Support\Facades\Response')->download($data);
    }
}
