<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApihouseModel;

class AccessDocument extends ApihouseModel
{
    protected $table = 'access_document';

    public $timestamps = true;
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'modified_date';

    protected $fillable = [
        'person_id',
        'type',
        'status',
        'source_year',
        'access_date',
        'access_any_time',
        'name',
        'comments',
        'expiry_date',
        'create_date',
        'modified_date',
    ];

    public static function findForQuery($query) {
        if (empty($query['year'])) {
            throw new InvalidArgumentException('Missing year');
        }

        $sql = self::where('year', $query['year']);

        if (isset($query['person_id'])) {
            $sql = self::where('person_id', $query['person_id']);
        }

        return $sql->orderBy('type', 'asc')->get();
    }
}
