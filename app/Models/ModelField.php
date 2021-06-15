<?php

namespace App\Models;

use App\Fields\Field;
use App\Helpers\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function getDatabaseNameAttribute($value) {
        if($this->type == FieldType::BELONGS_TO) {
            return Str::endsWith($value, '_id') ? $value : $value . '_id';
        }

        return $value;
    }

    public function model()
    {
        return $this->belongsTo(ProjectModel::class, 'project_model_id');
    }

    public function validations()
    {
        return $this->hasMany(ModelFieldValidation::class);
    }

}
