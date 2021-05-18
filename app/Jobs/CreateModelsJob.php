<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ProjectModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateModelsJob implements ShouldQueue
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
        $stub = file_get_contents(base_path('stubs/coreui/app/model.stub'));

        foreach ($this->project->projectModels as $projectModel) {
            $replacement = str_replace([
                '#--PASSWORD-HASH-IMPORT--#',
                '#--MODEL-NAME--#',
                '#--FILLABLE--#'
            ], [
                $this->generatePasswordHashImport($projectModel),
                $projectModel->name,
                $this->generateFillable($projectModel)
            ], $stub);
        }

    }

    private function generateFillable(ProjectModel $projectModel) {
        $str = '';
        foreach($projectModel->fields as $field) {
            $str .= "'$field->database_name',";
        }
        return $str;
    }

    private function generatePasswordHashImport(ProjectModel $projectModel): string
    {
        foreach($projectModel->fields as $field) {
            if($field->type == "password") {
                return 'use Illuminate\Support\Facades\Hash;';
            }
        }
        return '';
    }
}
