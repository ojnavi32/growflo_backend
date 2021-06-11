<?php

namespace App\Controller;

use App\Model\Stocks;
use App\Controller\BaseController;
use App\Model\Characteristic;

class StocksController extends BaseController
{
    private $db;
    private $requestMethod;
    private $stockID;

    private $model;
    private $model2;

    public function __construct($db, $requestMethod, $stockID)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->stockID = $stockID;

        $this->model = new Stocks($this->db);
        $this->model2 = new Characteristic($this->db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->stockID) {
                    $response = $this->getStock($this->stockID);
                } else {
                    $response = $this->getAllStocks();
                }
            break;
            case 'POST':
                $response = $this->createStock();
                break;
            case 'PUT':
                $response = $this->updateStock($this->stockID);
                break;
            case 'DELETE':
                $response = $this->deleteStock($this->stockID);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function getAllStocks()
    {
        $stocks = $this->model->findAll();
        foreach ($stocks as $stock) {
            $characteristics = $this->model2->findBy(['stock_id' => $stock['id']]);

            $stock['characteristics'] = implode(',', array_column($characteristics, 'name'));
            $result[] = $stock;
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    public function getStock($stockID)
    {
        $result = $this->model->find($stockID);

        if (! $result) {
            return $this->notFoundResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    public function createStock()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        $characteristics = explode(',', $input['characteristics']);

        unset($input['characteristics']);
        $stockID = $this->model->insert($input);

        foreach ($characteristics as $item) {
            $chars[] = [
                'name' => $item,
                'stock_id' => $stockID
            ];
        }

        if (isset($chars)) {
            $this->model2->insertMany($chars);
        }
        
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;

        return $response;
    }

    public function updateStock($id)
    {
        $stock = $this->model->find($id);
        if (! $stock) {
            return $this->notFoundResponse();
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $characteristics = explode(',', $input['characteristics']);

        unset($input['characteristics']);
        $this->model->update($id, $input);

        foreach ($characteristics as $item) {
            $chars[] = [
                'name' => $item,
                'stock_id' => $id
            ];
        }

        if (isset($chars)) {
            $this->model2->insertMany($chars);
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }

    public function deleteStock($id)
    {
        $stock = $this->model->find($id);
        if (! $stock) {
            return $this->notFoundResponse();
        }

        $this->model->delete($id);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;

        return $response;
    }
}
