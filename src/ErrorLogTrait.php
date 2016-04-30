<?php
namespace chilimatic\lib\Database;

/**
 * Class ErrorLogTrait
 * @package chilimatic\lib\Database
 */
trait ErrorLogTrait
{
    /**
     * @var \SplQueue|null
     */
    private $traitErrorLog;


    /**
     * @param $type
     * @param $message
     * @param null $data
     */
    public function log($type, $message, $data = null)
    {
        if (!$this->traitErrorLog) {
            $this->traitErrorLog = new \SplQueue();
        }

        $this->traitErrorLog->enqueue(
            [
                'type' => $type,
                'message' => $message,
                'data' => $data
            ]
        );
    }

    /**
     * @return null|\SplQueue
     */
    public function getTraitErrorLog()
    {
        return $this->traitErrorLog;
    }
}