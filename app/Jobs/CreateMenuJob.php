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

        $stub = str_replace(['#--PROJECT-NAME--#', '#--MENU-FIELDS--#'], [$this->project->name, $this->generateMenu($this->project->projectModelsWithoutParents()->orderBy('order')->get() )], $stub);

        file_put_contents($this->project->folder . '/resources/views/components/navbar.blade.php', $stub);
    }

    private function generateMenu($projectModels): string {
        $str = '';

        foreach ($projectModels as $projectModel) {
            if($projectModel->is_parent) {
                $str .= '<x-navitem title="' . $projectModel->label . '" title="' . $projectModel->label . '">' . "\n\t";
                $str .= $this->generateMenu($projectModel->projectModels()->orderBy('order')->get());
                $str .= '</x-navitem>';
                continue;
            }
            $str .= '<x-navitem title="' . $projectModel->label . '" :route="route(\'' . $projectModel->resource . '.index\')" title="' . $projectModel->label . '" activeRoute="' . $projectModel->resource . '/*" single />' . "\n\t";
        }
        return $str;
    }

    private function generateMenuItemHtml(string $resource, string $name, bool $isSingle): string
    {
        return '<x-navitem title="' . $name . '" :route="route(\'' . $resource . '.index\')" title="' . $name . '" activeRoute="' . $resource . '/*" ' . $isSingle ? 'single' : '' . ' />' . "\n";
    }
}
