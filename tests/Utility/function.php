<?php

if (!function_exists('create')) {
    function create($model, $attributes = [], $count = null, $states = []) {
        $factory = $model::factory($count);

        foreach ($states as $state) {
            $factory = $factory->$state();
        }

        return $factory->create($attributes);
    }
}

if (!function_exists('make')) {
    function make($model, $attributes = [], $count = null) {
        return $model::factory($count)->make($attributes);
    }
}

if (!function_exists('raw')) {
    function raw($model, $attributes = [], $count = null) {
        return $model::factory($count)->raw($attributes);
    }
}
