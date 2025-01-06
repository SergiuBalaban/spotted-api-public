<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

abstract class ModelAbstract extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getCreatedAtAttribute(string $value): string
    {
        $date = Carbon::parse($value);

        return $date->format('Y-m-d H:i');
    }

    public function getUpdatedAtAttribute(string $value): string
    {
        $date = Carbon::parse($value);

        return $date->format('Y-m-d H:i');
    }
}
