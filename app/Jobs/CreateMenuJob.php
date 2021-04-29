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

        $stub = str_replace('#--MENU-FIELDS--#', $menuItems, $stub);

        file_put_contents($this->project->folder . '/resources/views/dashboard/shared/nav-builder.blade.php', $stub);
    }

    private function generateMenuItemHtml(string $resource, string $name): string
    {
        return '        <x-nav-item activeRoute="' . $resource . '/*" :route="route(\'' . $resource . '.index\')" title="' . $name . '" />' . "\n";
    }
}
