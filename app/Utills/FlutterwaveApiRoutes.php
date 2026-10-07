<?php

namespace App\Utills\FlutterwaveApiRoutes;

class FlutterwaveApiRoutes
{
    public static function getBillInformationPath(string $biller_code)
    {
        return '/billers/'.$biller_code.'/items';
    }
}
