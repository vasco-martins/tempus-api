<?php

namespace App\Jobs;

use App\Fields\FieldsController;
use App\Helpers\FieldType;
use App\Models\Project;
use App\Models\ProjectModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateMigrationsJob implements ShouldQueue
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
        $stub = file_get_contents(base_path('stubs/coreui/database/migration.stub'));

        foreach ($this->project->projectModels as $projectModel) {

            $replacement = str_replace([
                '#--MIGRATION-CLASS--#',
                '#--TABLE-NAME--#',
                '#--MIGRATIONS--#'
            ], [
                'Create' . Str::ucfirst(Str::plural(Str::camel($projectModel->name))) . 'Table',
                $projectModel->database_name,
                $this->generateMigrations($projectModel)
            ], $stub);

            file_put_contents(
                $this->project->folder . '/database/migrations/' . $projectModel->created_at->format('Y_m_d_His') . '_create_' . $projectModel->database_name . '_table.php',
                $replacement);
        }

    }

    private function generateMigrations(ProjectModel $projectModel): string {
        $str = '';

        foreach ($projectModel->fields as $field) {
            $fieldController = new FieldsController($field);
            $str .=  $fieldController->getField()->getMigration() . "\n\t\t\t";
        }

        return $str;
    }
}
