<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

abstract class QueryFilter 
{
    abstract public function rules(): array;

    public function applyTo($query, array $filters)
    {
        $rules = $this->rules();

        $validator = Validator::make($filters, $rules);

        $this->valid = $validator->valid();

        foreach ($this->valid as $name => $value) {
            $this->applyFilter($query, $name, $value);
        }
        
        return $query;
    }

    protected function applyFilter($query, $name, $value)
    {
        $method = 'filterBy' . Str::studly($name);

        if (method_exists($this, $method)) {
            $this->$method($query, $value);
        } else {
            $query->where($name, $value);
        }
    }

    public function valid()
    {
        return $this->valid;
    }
}