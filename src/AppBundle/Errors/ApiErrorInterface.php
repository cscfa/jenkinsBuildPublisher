<?php

namespace AppBundle\Errors;

interface ApiErrorInterface
{
    /**
     * Items contain href and meta properties. href is
     * url based. meta is array of key value
     * @return array[\stdClass]
     */
    public function getLinks();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return integer
     */
    public function getCode();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDetails();

    /**
     * Can optionally contain 'pointer' as uri and
     * 'parameter' as parameter
     *
     * @return \stdClass
     */
    public function getSource();

    /**
     * @return array[string => string]
     */
    public function getMeta();
}
