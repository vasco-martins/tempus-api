<?php

namespace App\Models;

use App\Jobs\CreateControllersJob;
use App\Jobs\CreateEnvExampleJob;
use App\Jobs\CreateIndexViewJob;
use App\Jobs\CreateLivewireComponentLogicJob;
use App\Jobs\CreateLivewireComponentViewJob;
use App\Jobs\CreateMenuJob;
use App\Jobs\CreateMigrationsJob;
use App\Jobs\CreateModelsJob;
use App\Jobs\CreateProjectJob;
use App\Jobs\CreateRoutesJob;
use App\Jobs\DeleteProjectJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{

    protected $fillable = [
        'name',
        'user_id',
        'hash',
        'deploy_status',
        'updated_at',
    ];

    public const DEPLOY_STATUS = [
         'A iniciar o processo',
         'A preparar o ambiente',
         'A criar a base de dados',
         'A criar a pasta do projeto',
         'A gerar os ficheiros de configuração',
         'A instalar os ficheiros de configuração',
         'A realizar as migrações',
         'Pronto para testar!'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function (Project $project) {
            $project->hash = md5(uniqid($project->name . $project->id . date('Ymd'), true));
        });


    }

    public static function executeProjectJob(Project $project) {
        $project->update(['deploy_status' => 0]);
         DeleteProjectJob::dispatch($project);
        CreateProjectJob::dispatch($project);
        CreateEnvExampleJob::dispatch($project);
        CreateRoutesJob::dispatch($project);
        CreateControllersJob::dispatch($project);
        CreateModelsJob::dispatch($project);
        CreateMenuJob::dispatch($project);
        CreateLivewireComponentLogicJob::dispatch($project);
        CreateIndexViewJob::dispatch($project);
        CreateLivewireComponentViewJob::dispatch($project);
        CreateMigrationsJob::dispatch($project);
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
        return $this->hasMany(ProjectModel::class)->orderBy('order');
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

    public function getDatabaseAttribute(): string {
        return Str::slug($this->name);
    }

    public function getFolderAttribute()
    {
        return base_path('projects/' . $this->getSlugAttribute());
    }

    public function getDownloadLinkAttribute() {
        return route('projects.download', $this->hash);
    }

    public function getDeployUrlAttribute() {
        if(config('app.env') == 'local') {
            return 'http://' . $this->slug . '.test';
        }

        return 'http://' . $this->slug .'.avogg.pt/';
     }

}
