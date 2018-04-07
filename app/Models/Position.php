<?php

namespace App\Models;

use App\Models\ApihouseModel;

class Position extends ApihouseModel
{
    protected $table = 'position';

    protected $fillable = [
        'all_rangers',
        'auto_signout',
        'count_hours',
        'max',
        'min',
        'new_user_eligible',
        'on_sl_report',
        'short_title',
        'title',
        'training_position_id',
        'type',
    ];

    protected $casts = [
        'all_rangers'   => 'bool',
        'auto_signout'  => 'bool',
        'new_user_eligible' => 'bool',
        'on_sl_report' => 'bool',
    ];

    protected $rules = [
        'title' => 'required',
        'min'   => 'integer',
        'max'   => 'integer',
        'training_position_id'  => 'nullable|exists:position,id',
    ];

    public static function findAll()
    {
        return self::orderBy('title')->get();
    }
}
