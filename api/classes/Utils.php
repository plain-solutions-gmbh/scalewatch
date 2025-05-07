<?php

use Carbon\Carbon;

class Utils {

    public static function filter(array $data, null|int|array|string $query = null)
    {

        //Return all results
        if ($query === null) {
            return $data;
        }

        //Query is string: Get columns
        if (is_string($query)) {
            return array_map(fn($item) => ($item[$query] ?? null), $data);
        }
        
        //Query is integer: Get by index
        if (is_int($query)) {
            return $data[$query] ?? null;
        }

        $result = [];

        foreach ($data as $i => $item) {

            //Query is array: Get by query (AND-Operator)
            $match = true;
            foreach ($query as $key => $value) {
                if (($data[$key] ?? $value) !== $value) {
                    $match = false;
                }
            }

            $match ? $result[] = $data : null;
        }

        return $result;

    }

}