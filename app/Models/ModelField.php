<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelField extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'type',
        'database_name',
        'in_view',
        'in_edit',
        'in_create'
    ];

    public function model()
    {
        return $this->hasMany(ProjectModel::class);
    }

    public function validations()
    {
        return $this->hasMany(ModelFieldValidation::class);
    }

}
