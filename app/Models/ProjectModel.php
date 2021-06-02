<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'label', 'soft_delete', 'project_id', 'order', 'is_parent', 'project_model_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $with=['projectModels'];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function fields()
    {
        return $this->hasMany(ModelField::class);
    }

    public function projectModels() {
        return $this->hasMany(ProjectModel::class);
    }

    public function parentMenu() {
        return $this->belongsTo(ProjectModel::class, 'project_model_id');
    }

    public function father()
    {
        return $this->belongsTo(ProjectModel::class);
    }

    public function getNameAttribute($value)
    {
        return (str_replace(' ', '', ucwords($value)));
    }

    public function getControllerNameAttribute(): string
    {
        return Str::ucfirst(Str::singular($this->name));
    }

    public function getDatabaseNameAttribute(): string {
        return Str::snake(Str::lower(Str::plural($this->name)));
        }

    public function getControllerAttribute(): string
    {
        return 'App\\Http\\Controllers\\' . $this->controller_name . 'Controller';
    }

    public function getResourceAttribute(): string
    {
        return Str::plural(Str::lower($this->name));
    }

}
