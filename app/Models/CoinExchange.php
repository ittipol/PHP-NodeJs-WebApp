<?php

namespace App\Models;

class CoinExchange extends Model
{
    protected $table = 'coin_exchanges';
    protected $fillable = ['amount','method','description','status','created_by'];

    public function setUpdatedAt($value) {}
}
