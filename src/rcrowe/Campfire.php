<?php

namespace rcrowe;

class Campfire
{
    /**
     * @var rcrowe\Campfire\Config
     */
    protected $config;

    /**
     * @var rcrowe\Campfire\Queue
     */
    protected $queue;

    /**
     * @var rcrowe\Campfire\Transport
     */
    protected $transport;

    public function __construct(array $config = array(), $http = NULL)
    {
        $this->config    = new Campfire\Config($config);
        $this->queue     = new Campfire\Queue;
        $this->transport = new Campfire\Transport($this->config, $http);
    }

    public function queue($msg)
    {
        return $this->queue->add($msg);
    }

    public function send($msg = NULL)
    {
        if ($msg !== NULL)
        {
            $this->queue->add($msg);
        }

        // Check there's something in the queue to send
        if (count($this->queue) === 0)
        {
            throw new Campfire\Exceptions\TransportException('Queue is empty');
        }

        // Loop over and send each item in the queue
        foreach ($this->queue as $msg)
        {
            $this->transport->send($msg);
        }

        $this->queue->remove();

        return TRUE;
    }
}