<?php

namespace Millennium\Interfaces;

interface IRouterCollectionInterface
{

    /**
     * 
     * @param string $filename
     * 
     * @return array
     */
    public function collectRouters($filename);
}