<?php

namespace uranium\seeder;

use uranium\model\AuctionModel;
use uranium\model\BidModel;

class AuctionSeeder{
    public static function run(): void{
        $auctionData = [
            "title"     => "test auction",
            "reference" => "NoClue",
            "user_id"   => "1",
            "description" => "This is my auctions description, I dont know what to put here to test it but something interesting will go here eventually!",
            "startStamp" => "1234",
            "allowOffers" => 0,
            "duration" => 24,
            "reservePrice" => 69420,
            "allowBuynow" => 0,
            "buynowPrice" => 0,
            "public" => 1
        ];
        $model = new AuctionModel();
        $model->rows[] = $auctionData;
        $model->save();

        $bidData = [
            [
                "reference" => "NoClue",
                "user"      => "2",
                "amount"    => 1.2344555555
            ],
            [
                "reference" => "NoClue",
                "user"      => "3",
                "amount"    => 1.5333333
            ]
        ];
        $bidModel = new BidModel();
        $bidModel->rows = $bidData;
        $bidModel->save();
    }
}
