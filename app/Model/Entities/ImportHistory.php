<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed path
 * @property mixed file_id
 * @property mixed|null module
 * @property mixed|string type
 * @property int|mixed success_record
 * @property int|mixed error_record
 */
class ImportHistory extends ModelSoftDelete
{
	protected $table = "import_history";
	protected $fillable = ['file_id', 'type', 'success_record', 'error_record', 'module','memo'];
}
