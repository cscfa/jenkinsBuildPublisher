<?php

namespace AppBundle\Misc\Build;

use AppBundle\Misc\Build\Build;

class EmbeddedBuild extends Build
{

    private $status;

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


}