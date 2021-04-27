<?php

namespace App\Listeners;

use OwenIt\Auditing\Events\Auditing;
use OwenIt\Auditing\Exceptions\AuditingException;

class AuditingListener
{
    /**
     * Handle the Audited event.
     *
     * @param \OwenIt\Auditing\Events\Auditing $event
     * @return bool
     */
    public function handle(Auditing $event)
    {
        $modal = $event->model;
        try {
            $data = $modal->toAudit();
        } catch (AuditingException $e) {
            $data = [];
        }

        if (empty($data) || (empty($data['old_values']) && empty($data['new_values'])) ) {
            return false;
        }
    }
}