<?php

use App\common;
use App\Enum\UserRole;
use App\User;
use Illuminate\Database\Seeder;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->username = 'admin';
        $user->password = common::encryptPassword('abc@1234');
        $user->userRole = UserRole::ADMINISTRATOR;
        $user->employee()->associate(1);
        $user->save();

        $user = new User();
        $user->username = 'pharmacist';
        $user->password = common::encryptPassword('abc@1234');
        $user->userRole = UserRole::PHARMACIST;
        $user->employee()->associate(2);
        $user->save();

        $user = new User();
        $user->username = 'opddoctor';
        $user->password = common::encryptPassword('abc@1234');
        $user->userRole = UserRole::DOCTOR;
        $user->doctor()->associate(1);
        $user->save();

        $user = new User();
        $user->username = 'channellingdoctor';
        $user->password = common::encryptPassword('abc@1234');
        $user->userRole = UserRole::DOCTOR;
        $user->doctor()->associate(2);
        $user->save();
    }
}
