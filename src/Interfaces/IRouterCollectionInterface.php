<?php

namespace Millennium\Router\Interfaces;

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
