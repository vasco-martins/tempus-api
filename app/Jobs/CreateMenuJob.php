<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMenuJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $project;

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
        $stub = file_get_contents(base_path('stubs/coreui/views/menu.stub'));

        $menuItems = '';

        foreach ($this->project->projectModels as $projectModel) {
            $menuItems .= $this->generateMenuItemHtml($projectModel->resource, $projectModel->label);
        }

        $stub = str_replace(['#--PROJECT-NAME--#', '#--MENU-FIELDS--#'], [$this->project->name, $menuItems], $stub);

        file_put_contents($this->project->folder . '/resources/views/components/navbar.blade.php', $stub);
    }

    private function generateMenuItemHtml(string $resource, string $name): string
    {
        return '<x-navitem title="' . $name . '" :route="route(\'' . $resource . '.index\')" title="' . $name . '" activeRoute="' . $resource . '/*" single />' . "\n";
    }
}
