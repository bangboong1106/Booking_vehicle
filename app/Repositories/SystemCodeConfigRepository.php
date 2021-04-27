<?php

namespace App\Repositories;

use App\Model\Entities\SystemCodeConfig;
use App\Repositories\Base\CustomRepository;

class SystemCodeConfigRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return SystemCodeConfig::class;
    }

    public function validator()
    {
        return \App\Validators\SystemCodeConfigValidator::class;
    }


    public function getSystemCodeConfig($type, $active = false)
    {
        if ($type) {
            if ($active) {
                return $this->search([
                    'type_eq' => $type,
                    'sort_type' => 'des',
                    'sort_field' => 'ins_date'
                ])->lockForUpdate()->first();
            } else {
                return $this->search([
                    'type_eq' => $type,
                    'sort_type' => 'des',
                    'sort_field' => 'ins_date'
                ])->first();
            }
        }

        return null;
    }
}