<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateControllersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $project;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $stub = file_get_contents(base_path('stubs/coreui/app/controller.stub'));

        foreach ($this->project->projectModels as $projectModel) {
            $replacement = str_replace([
                '#--MODEL-IMPORT--#',
                '#--CONTROLLER_NAME--#',
                '#--MODEL-NAME--#',
                '#--LOWERCASE-MODEL-NAME--#',
                '#--LOWERCASE-NAME-PLURAL--#',
            ], [
                'use App\\Models\\' . $projectModel->name . ';',
                $projectModel->controller_name,
                $projectModel->name,
                Str::lower($projectModel->name),
                Str::lower(Str::plural($projectModel->name))
            ], $stub);
            file_put_contents($this->project->folder . '/app/Http/Controllers/' . $projectModel->controller_name . 'Controller.php', $replacement);
        }
    }
}
