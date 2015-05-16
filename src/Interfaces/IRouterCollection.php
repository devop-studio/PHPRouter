<?php

namespace Millennium\Interfaces;

interface IRouterCollection
{

    /**
     * 
     * @param string $filename
     * 
     * @return array
     */
    public function collectRouters($filename);
}
