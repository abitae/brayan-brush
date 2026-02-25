<?php
namespace App\Traits;

use Carbon\Carbon;

trait UtilsTrait
{
    function dateNow(String $format)
    {
       return now()->setTimezone('America/Lima')->format($format);
    }
}
