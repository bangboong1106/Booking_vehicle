<?php


namespace App\Listeners;


use Exception;
use Throwable;

class CustomBaseListener
{
    public function handle($eventData, $queue)
    {
        $errorDescription = "";
        try {
            $this->handleBusiness($eventData);
            return true;

        } catch (Exception $e) {
            $errorDescription = $e;
            logError($e . ' - Data :' . $queue);
        } catch (Throwable $e) {
            $errorDescription = $e;
            logError($e . ' - Data :' . $queue);
        }

        $queue->attempts = $queue->attempts + 1;
        $queue->error_description = $errorDescription;
        $queue->updated_at = now();
        $queue->save();
        return false;
    }

    protected function handleBusiness($eventData)
    {

    }

    /**
     * @param $eventData
     * @param $exception
     */
    public function failed($eventData, $exception)
    {
        logError($exception . ' - Data : ' . json_encode($eventData));
    }

}