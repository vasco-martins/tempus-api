<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateEnvExampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private Project $project;

    /**
     * Create a new job instance.
     *
     * @return void
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
        $stub = file_get_contents(base_path('stubs/coreui/.env.example.stub'));

        $stub = str_replace([
            '#--PROJECT-NAME--#',
            '#--SANITIZED-PROJECT-NAME-LOWERCASE--#',
            ],
            [
                $this->project->name,
                Str::slug($this->project->name),
            ], $stub);

        file_put_contents(
            $this->project->folder . '/.env.example',
            $stub);
    }
}
