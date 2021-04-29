<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projectModels()
    {
        return $this->hasMany(ProjectModel::class);
    }

    public function plugins()
    {
        return $this->belongsToMany(Plugin::class);
    }

    public function getSlugAttribute()
    {
        return $this->id . '-' . Str::slug($this->name);
    }

    public function getFolderAttribute()
    {
        return base_path('projects/' . $this->getSlugAttribute());
    }

}
