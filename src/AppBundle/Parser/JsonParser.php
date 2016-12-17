<?php

namespace AppBundle\Parser;

interface JsonParser
{

    public function parseArray(array $array);

    public function parse($item);

}
