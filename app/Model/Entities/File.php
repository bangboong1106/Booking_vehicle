<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed path
 */
class File extends ModelSoftDelete
{
	protected $table = "files";
	protected $fillable = ['file_id', 'file_name', 'file_type', 'mime', 'path', 'size', 'is_confirmed'];

    public function removeFile()
    {

	}
}