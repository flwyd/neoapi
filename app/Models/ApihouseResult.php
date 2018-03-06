<?php


namespace App\Models;

abstract class ApihouseResult {
    public function toJson() {
        if (empty($this->results)) {
            throw new \RuntimeException(class_basename($this) . ' does not have a results table defined.');
        }

        $json = [ ];

        foreach ($this->results as $column) {
            $json[$column] = $this->$column;
        }

        return $json;
    }
}
