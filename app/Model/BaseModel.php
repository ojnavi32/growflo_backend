<?php

namespace App\Model;

class BaseModel
{
    public function findAll()
    {
        try {
            $statement = "SELECT * FROM {$this->table}";
            
            return $this->base->select($statement);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    public function findBy(array $conditions)
    {
        $insert_values = [];
        foreach ($conditions as $key => $item) {
            $insert_values = array_merge($insert_values, [$key => $item]);
            $arr[] = "WHERE {$key} = :{$key}";
        }
        
        $wheres = implode(' AND ', $arr);
        try {
            $statement = "SELECT * FROM {$this->table} {$wheres}";
            return $this->base->select($statement, $insert_values);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        try {
            $statement = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

            return $this->base->select($statement, compact('id'));
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    public function insert(array $input)
    {
        try {
            $fields = $this->getFields();
            $fieldPlaceholder = $this->getFieldPlaceholder();

            $statement = "INSERT INTO {$this->table} ({$fields}) VALUES ({$fieldPlaceholder})";

            return $this->base->insert($statement, $input);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    public function insertMany(array $input)
    {
        try {
            $fields = $this->getFields();
            $insert_values = [];
            foreach ($input as $item) {
                $question_marks[] = '('.$this->getFieldPlaceholder().')';
                $insert_values = array_merge($insert_values, $item);
            }
            $statement = "INSERT INTO {$this->table} ({$fields}) VALUES " . implode(',', $question_marks);
            return $this->base->update($statement, $insert_values);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    public function update($id, array $input)
    {
        try {
            $fieldPlaceholder = $this->updateFieldPlaceholder($input);
            $statement = "UPDATE {$this->table} SET {$fieldPlaceholder} WHERE id = :id";

            $input['id'] = $id;
            return $this->base->update($statement, $input);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $statement = "DELETE from {$this->table} WHERE id = :id";
            
            return $this->base->delete($statement, compact('id'));
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    private function getFields()
    {
        return implode(',', $this->fillable);
    }

    private function getFieldPlaceholder()
    {
        $arr = array_map(function ($value) {
            return ':'.$value;
        }, $this->fillable);
        return implode(',', $arr);
    }

    private function updateFieldPlaceholder($items)
    {
        foreach ($items as $key => $item) {
            $arr[] = $key . ' = :' . $item;
        }
        return implode(',', $arr);
    }
}
