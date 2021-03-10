<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket';  //設定讀取哪個資料表
    protected $guarded=[];
}
