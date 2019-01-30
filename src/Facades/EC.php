<?php
/**
 * Created by PhpStorm.
 * User: maiev
 * Date: 2019/1/30
 * Time: 10:53 AM
 */
namespace Maiev\EC;

use Illuminate\Support\Facades\Facade;

class EC extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'EC';
    }
}