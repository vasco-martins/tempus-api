<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateIndexViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Project $project;

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
        $stub = file_get_contents(base_path('stubs/coreui/views/index.stub'));

        foreach ($this->project->projectModels as $projectModel) {
            $livewireComponentNameSingular = Str::lower(Str::singular($projectModel->name));
            $livewireComponentNamePlural = Str::lower(Str::plural($projectModel->name));
            $replacement = str_replace([
                '#--LABEL--#',
                '#--LIVEWIRE-COMPONENT--NAME--#',
            ], [
                $projectModel->label,
                $livewireComponentNameSingular . '.index',
            ], $stub);

            $basePath = $this->project->folder . '/resources/views/' . $livewireComponentNamePlural;
            File::makeDirectory($basePath, 0777, true, true);
            file_put_contents($basePath . '/index.blade.php', $replacement);
        }
    }
}
