<?php
namespace App\Helpers;

use Jenssegers\Agent\Agent;

class AgentHelper
{
    protected $agent;

    public function __construct($userAgent)
    {
        $this->agent = new Agent();
        $this->agent->setUserAgent($userAgent);
    }

    public function getDeviceName()
    {
        return $this->agent->device();
    }

    public function getBrowser()
    {
        return $this->agent->browser() . ' ' . $this->agent->version($this->agent->browser());
    }
}
