<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    protected array $filters = [];
    protected Request $request;

    public function __construct(protected Builder $builder)
    {
        $this->request = \request();
    }

    public function apply(): Builder
    {
        foreach ($this->request->only($this->filters) as $filter => $value) {
            if (method_exists($this, $filter)) {
                $value ? $this->$filter($value) :  $this->$filter();
            }
        }
        return $this->builder;
    }
}
