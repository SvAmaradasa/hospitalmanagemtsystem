<?php

/**
 * Created by PhpStorm.
 * User: Madhawa Ariyarathna
 * Date: 8/28/2016
 * Time: 12:04 PM
 */

namespace App\Enum;

abstract class UserRole extends BasicEnum
{
    const ADMINISTRATOR = 'Administrator';
    const MANAGER = 'Manager';
    const ACCOUNTANT = 'Accountant';
    const NURSE = 'Nurse';
    const RECEPTIONIST = 'Receptionist';
    const PHARMACIST = 'Pharmacist';
    const DOCTOR = 'Doctor';
}