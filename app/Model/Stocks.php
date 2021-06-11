<?php

namespace App\Model;

use App\Model\DatabaseModel;
use App\Model\BaseModel;

class Stocks extends BaseModel
{
    protected $table = 'stocks';
    protected $base;

    protected $fillable = [
        'name',
        'barcode',
        'unit_price',
        'cost_price',
        'unit_per_tray'
    ];

    public function __construct($db)
    {
        $this->base = new DatabaseModel($db);
    }

    public function getWithCharacteristics()
    {
        $fields = array_map(function ($value) {
            return $this->table.'.'.$value;
        }, $this->fillable);
        $fields = implode(',', $fields);

        $statement = "SELECT {$fields}, characteristics.name as characteristics FROM {$this->table} LEFT JOIN characteristics ON {$this->table}.id = characteristics.stock_id";
        echo $statement;
        exit;
        return $this->base->select($statement);
    }
}
