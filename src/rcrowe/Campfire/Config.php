<?php

namespace rcrowe\Campfire;

class Config
{
    protected $config = array(
        'subdomain' => null,
        'room'      => null,
        'key'       => null,
    );

    public function __construct(array $config = array())
    {
        $this->config = $config;

        // Check we have all the config options we need
        foreach (array('subdomain', 'room', 'key') as $item) {

            if (!isset($this->config[$item]) OR !$this->config[$item]) {
                throw new Exceptions\ConfigException('Unable to find config item: '.$item);
            }
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function get($item)
    {
        return (isset($this->config[$item])) ? $this->config[$item] : null;
    }
}
