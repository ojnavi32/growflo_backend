<?php

namespace App\Model;

use App\Model\DatabaseModel;
use App\Model\BaseModel;

class Characteristic extends BaseModel
{
    protected $table = 'characteristics';
    protected $base;

    protected $fillable = [
        'stock_id',
        'name'
    ];

    public function __construct($db)
    {
        $this->base = new DatabaseModel($db);
    }
}
