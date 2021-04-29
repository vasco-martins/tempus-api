<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateRoutesJob implements ShouldQueue
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
        $content = '';

        foreach ($this->project->projectModels as $projectModel) {
            $content .= "\t\t'$projectModel->resource' => $projectModel->controller::class,\n";
        }

        $content = rtrim($content);
        $stub = file_get_contents(base_path('stubs/coreui/routes/web.stub'));
        $stub = str_replace('#--RESOURCES--#', $content, $stub);

        file_put_contents($this->project->folder . '/routes/web.php', $stub);
    }
}
