<?php

namespace App\Http\JsonApi;

use Illuminate\Database\Eloquent\Model;

use App\Http\JsonApi;

class SerializeRecord {

    /*
     * The record to be serialized
     * @var Model
     */

    protected $record;

    public function __construct(Model $record) {
        $this->record = $record;
    }

    /*
     * Construct a JSON API response representing the given record.
     * if $authorizedUser argument is present this means a column filter
     * is to be consulted on which columns are to be allowed based
     * on the $authorizedUser's roles. Otherwise, all columns are returned.
     *
     * The filter to be used is from \App\Http\Filters\<ModelName>Filter
     *
     * @var Model (optional) $authorizedUser the person to filter against
     * @return array a JSON API record
     */

    public function toJsonApi(Model $authorizedUser = null): array
    {
        $modelName = class_basename($this->record);

        if ($authorizedUser) {
            $modelFilter = "\\App\\Http\\Filters\\".$modelName."Filter";
            $columns = (new $modelFilter($this->record))->serializerFilter($authorizedUser);
        } else {
            $appends = $this->record->getAppends();
            $fillable = $this->record->getFillable();

            if (empty($appends) && empty($fillable)) {
                // Use the actual set attributes if appends & fillables are empty
                $columns = array_keys($this->record->getAttributes());
            } else {
                if ($fillable) {
                    $columns = $fillable;
                } else {
                    $columns = [];
                }

                if ($appends) {
                    $columns = array_merge($columns, $appends);
                }
            }
        }

        $attributes = [ ];

        foreach ($columns as $column) {
            $value = $this->record->$column;
            if (gettype($value) == 'object' && method_exists($value, 'toDateTimeString')) {
                $value = $value->toDateTimeString();
            }
            $attributes[JsonApi::jsonName($column)] = $value;
        }

        return [
            'type'  => JsonApi::jsonName($modelName),
            'id'    => $this->record->id,
            'attributes' => $attributes,
        ];

    }

    public function toErrorJsonApi() {
        $record = $this->record;
        $errors = $record->getErrors();
        $errorList = [];

        if ($errors) {
            foreach ($errors->keys() as $key) {
                foreach ($errors->get($key) as $message) {
                    $column = JsonApi::jsonName($key);
                    $errorList[] = [
                        'code'    => 422,
                        'source'  => [ 'pointer' => "/data/attributes/$column" ],
                        'title'   => $message
                    ];
                }
            }
        }

        return [ 'errors' => $errorList ];
    }
}


 ?>
