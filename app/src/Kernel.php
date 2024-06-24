<?php
/**
 * App kernel.
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * App kernel.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
