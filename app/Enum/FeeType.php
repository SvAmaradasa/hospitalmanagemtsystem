<?php

/**
 * Created by PhpStorm.
 * User: Madhawa Ariyarathna
 * Date: 8/17/2016
 * Time: 12:02 PM
 */

namespace App\Enum;

abstract class FeeType extends BasicEnum
{
    const OPD = 'OPD';
    const CHANNELLING = 'Channelling';
    const SCAN = 'Scan';
    const X_RAY = 'X-Ray';
    const LAB = 'Lab';
}