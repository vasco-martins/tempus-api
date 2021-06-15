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
        'project_model_id',
        'in_edit',
        'in_create',
        'is_searchable',
        'can_edit',
    ];

    public function model()
    {
        return $this->belongsTo(ProjectModel::class, 'project_model_id');
    }

    public function validations()
    {
        return $this->hasMany(ModelFieldValidation::class);
    }

}
