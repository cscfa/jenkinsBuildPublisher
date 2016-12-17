<?php

namespace AppBundle\Twig;

use AppBundle\Misc\Build\Build;
use AppBundle\Misc\Build\EmbeddedBuild;
use AppBundle\Entity\Status;

class StatusExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('buildStatus', array($this, 'displayBuildStatus'), array('is_safe' => array('html')))
        );
    }

    public function displayBuildStatus(Build $build)
    {
        $status = '';
        if ($build instanceof EmbeddedBuild) {
            $status = $build->getStatus();

            if ($status === null) {
                return '';
            }

            $labels = array('label', $this->getLabel($status));

            $status = '<span class="'.implode(' ', $labels).'">'.$status.'</span>';
        }

        return $status;
    }

    private function getLabel($status)
    {
        switch ($status) {
            case Status::SUCCESS:
                return 'label-success';
            case Status::FAILED:
                return 'label-danger';
            case Status::UNSTABLE:
                return 'label-warning';
            default:
                return 'label-default';
        }
    }

    public function getName()
    {
        return 'status_extension';
    }
}
