<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PayAccount extends Authenticatable
{
    protected $table = 'pay_accounts';  //設定讀取哪個資料表
    protected $guarded=[];

}
