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
        return $this->hasMany(ProjectModel::class)->where('is_parent', 0);
    }

    public function projectModelsWithoutParents() {
        return $this->hasMany(ProjectModel::class)->whereDoesntHave('parentMenu');
    }

    public function parentMenus() {
        return $this->hasMany(ProjectModel::class)->where('is_parent', 1);
    }

    public function plugins()
    {
        return $this->belongsToMany(Plugin::class);
    }

    public function getSlugAttribute()
    {
        return $this->id . '-' . Str::slug($this->name);
    }

    public function getFilenameAttribute(): string
    {
        return Str::slug($this->name);
    }

    public function getFolderAttribute()
    {
        return base_path('projects/' . $this->getSlugAttribute());
    }

}
