<?php

namespace App\Http\JsonApi;

use \App\Http\JsonApi;
use Illuminate\Database\Eloquent\Model;

class DeserializeRecord
{
    protected $attributes;
    protected $resourceAttributes;
    protected $record;
    protected $resourceType;

    public function __construct($request, Model $record)
    {
        $this->request = $request;

        $this->resourceAttributes = $request->input('data.attributes');

        if (empty($this->resourceAttributes)) {
            throw new \InvalidArgumentException("Missing 'data.attributes' field in request");
        }

        $this->resourceType = $request->input('data.type');

        if (empty($this->resourceType)) {
            throw new \InvalidArgumentException("Missing 'data.type' field in request");
        }

        $this->attributes = [];
        $this->record = $record;
    }

    public static function fromJsonApi($request, $record, $authorizedUser = null)
    {
        (new DeserializeRecord($request, $record))->fillRecord($authorizedUser);
        return $record;
    }

    public function fillRecord($authorizedUser = null): void
    {
        $modelName = class_basename($this->record);
        $jsonModelName = JsonApi::jsonName($modelName);

        if ($authorizedUser) {
            $filterName = "\\App\\Http\\Filters\\$modelName"."Filter";
            $modelColumns = (new $filterName($this->record))->deserializerFilter($authorizedUser);
        } else {
            $modelColumns = $this->record->getFillable();

            if (empty($modelColumns)) {
                throw new \RuntimeException("Model $modelName has no fillables set.");
            }
        }

        $jsonColumnToModel = [];
        foreach ($modelColumns as $attribute) {
            $jsonColumnToModel[JsonApi::jsonName($attribute)] = $attribute;
        }

        foreach ($this->resourceAttributes as $key => $value) {
            if (array_key_exists($key, $jsonColumnToModel)) {
                $column = $jsonColumnToModel[$key];
                $this->record->$column = $value;
            }
        }
    }
}
