<?php

namespace uranium\seeder;

use uranium\core\EncryptionHelper;
use uranium\model\UserModel;

class UserSeeder{
    public static function run(): void{
        $userData = [
            [
                "username" => "test",
                "email"    => "test@test.test",
                "password" =>  EncryptionHelper::generateHash("Password1!")
            ],
            [
                "username" => "bidder1",
                "email"    => "bidder1@test.test",
                "password" => EncryptionHelper::generateHash("bidder1")
            ],
            [
                "username" => "bidder2",
                "email"    => "bidder2@test.test",
                "password" => EncryptionHelper::generateHash("bidder2")
            ]
        ];
        $model = new UserModel();
        $model->rows = $userData;
        $model->save();
    }
}
