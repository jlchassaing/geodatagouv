<?php
/**
 * Created by PhpStorm.
 * User: jlchassaing
 * Date: 05/10/2018
 * Time: 09:56
 */

namespace GeoDataGouvBundle;

use GeoDataGouvBundle\DependencyInjection\GeoDataGouvExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GeoDataGouvBundle extends Bundle
{
    protected $name = 'GeoDataGouvBundle';

    public function getContainerExtension()
    {
        return new GeoDataGouvExtension();
    }

}