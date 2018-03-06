<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Factory as Validator;
use App\Http\JsonApi\SerializeRecord;
use App\Http\JsonApi\DeserializeRecord;

abstract class ApihouseModel extends Model
{

    /**
     * Don't use created_at/updated_at.
     * @var bool
     */
    public $timestamps = false;

    protected $errors;

    protected $rules;

    protected $results;

    public function toJsonApi($authorizedUser = null)
    {
        return (new SerializeRecord($this))->toJsonApi($authorizedUser);
    }

    public function fromJsonApi($request, $authorizedUser = null)
    {
        return DeserializeRecord::fromJsonApi($request, $this, $authorizedUser);
    }

    public static function recordExists($id) : bool {
        return get_called_class()::where('id', $id)->exists();
    }

    public function validate($rules = null) {
        if ($rules === null) {
            $rules = $this->rules;
        }

        if (empty($rules))
            return true;

        $validator = \Validator::make($this->getAttributes(), $rules);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }

    public function save($options = []) {
        if (!$this->isDirty()) {
            return true;
        }

        if (!$this->validate()) {
            return false;
        }

        return parent::save($options);
    }

    public function getResults() {
        return $this->results;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getRules() {
        return $this->rules;
    }
}
