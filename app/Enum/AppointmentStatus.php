<?php

/**
 * Created by PhpStorm.
 * User: Madhawa Ariyarathna
 * Date: 8/17/2016
 * Time: 12:04 PM
 */

namespace App\Enum;

abstract class AppointmentStatus extends BasicEnum
{
    const BRAND_NEW = 'New';
    const PAID = 'Paid';
    const IN_PROGRESS = 'In Progress';
    const CLOSE = 'Close';
    const CANCELED = 'Canceled';
    const REFUNDED = 'Refunded';
}