<?php

namespace App\Repositories;

use App\Model\Entities\File;
use App\Repositories\Base\CustomRepository;
use App\Validators\FileValidator;

class FileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return File::class;
    }

    public function validator()
    {
        return FileValidator::class;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object
     */
    public function getFileWithID($id)
    {
        if ($id)
            return $this->search([
                'file_id_eq' => $id
            ])->first();
        return null;
    }
}