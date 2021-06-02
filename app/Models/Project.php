<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{

    protected $fillable = [
        'name',
        'user_id',
        'hash'
    ];



    public static function boot() {
        parent::boot();

        static::creating(function (Project $project) {
            $project->hash = md5(uniqid($project->name . $project->id . date('Ymd'), true));
        });


    }

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

    public function menu() {
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

    public function getFilenameAttribute(): string
    {
        return Str::slug($this->name);
    }

    public function getFolderAttribute()
    {
        return base_path('projects/' . $this->getSlugAttribute());
    }

    public function getDownloadLinkAttribute() {
        return route('projects.download', $this->hash);
    }

}
