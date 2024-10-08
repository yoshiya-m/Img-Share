<?php

namespace Helpers;

class ValidationHelper
{
    public static function integer($value, float $min = -INF, float $max = INF): int
    {
      
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range"=>(int) $max]);
        if ($value === false) throw new \InvalidArgumentException("The provided value is not a valid integer.");
        return $value;
    }

    public static function string($value): string {
        if (!is_string($value)) throw new \InvalidArgumentException("The provided value is not a string.");
        return $value;
    }   
}