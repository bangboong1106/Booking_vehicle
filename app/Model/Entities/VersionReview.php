<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class VersionReview extends ModelSoftDelete
{
    protected $table = "version_review";
    protected $_alias = "versionReview";

    protected $fillable = ['version', 'reviewed'];

}