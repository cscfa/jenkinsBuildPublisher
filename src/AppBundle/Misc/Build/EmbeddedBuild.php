<?php

namespace AppBundle\Misc\Build;

use AppBundle\Misc\Build\Build;
use AppBundle\Entity\Build as BuildEntity;
use AppBundle\Entity\BuildFile;

class EmbeddedBuild extends Build
{

    private $status;

    private $dbFiles = array();

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