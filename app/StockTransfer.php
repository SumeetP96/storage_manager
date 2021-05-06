<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $guarded = [];

    const INTER_GODOWN = 1;
    const PURCHASE = 2;
    const SALES = 3;
}
