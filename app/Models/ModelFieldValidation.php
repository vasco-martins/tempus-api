<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelFieldValidation extends Model
{
    use HasFactory;

    protected $table = 'model_fields_validations';

    protected $fillable = [
        'project_model_id',
        'name',
        'value'
    ];

    public function modelField()
    {
        return $this->belongsTo(ModelField::class);
    }
}
