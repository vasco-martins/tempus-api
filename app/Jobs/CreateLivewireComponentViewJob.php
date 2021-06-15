<?php

namespace App\Jobs;

use App\Fields\FieldsController;
use App\Models\Project;
use App\Models\ProjectModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateLivewireComponentViewJob implements ShouldQueue
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
        $stub = file_get_contents(base_path('stubs/coreui/views/livewire.stub'));

        foreach ($this->project->projectModels as $projectModel) {
            $livewireComponentNamePluralAndLowerCase = Str::lower(Str::plural($projectModel->name));
            $replacement = str_replace([
                '#--LABEL--#',
                '#--LABEL-SINGULAR--#',
                '#--TABLE-HEAD--#',
                '#--LOWERCASE-MODEL-NAME-PLURAL--#',
                '#--LOWERCASE-MODEL-NAME--#',
                '#--VARIABLE-NAME--#',
                '#--TABLE-ROW--#',
                '#--FORM--#',
                '#--SCRIPTS--#'
            ], [
                $projectModel->label,
                Str::singular($projectModel->label),
                $this->buildTableHead($projectModel),
                Str::lower(Str::plural($projectModel->name)),
                Str::lower(Str::singular($projectModel->name)) . 'Item',
                Str::lower(Str::singular($projectModel->name)),
                $this->buildTableRow($projectModel),
                $this->buildForm($projectModel),
                $this->buildScripts($projectModel)

            ], $stub);


            $basePath = $this->project->folder . '/resources/views/livewire/' . $livewireComponentNamePluralAndLowerCase;
            File::makeDirectory($basePath, 0777, true, true);
            file_put_contents($basePath . '/index.blade.php', $replacement);
        }

    }

    private function buildTableHead(ProjectModel $projectModel): string {
        $str = '';
        foreach($projectModel->fields as $field) {
            if($field->in_view) {
                $str .= "<th>$field->label</th>\n\t\t\t\t\t";
            }
        }
        return $str;
    }

    private function buildTableRow(ProjectModel $projectModel): string {
        $str = '';
        foreach($projectModel->fields as $field) {
            $fieldController = new FieldsController($field);
            if($field->in_view)     {
                $str .= '<td>' . $fieldController->getField()->getTable() . '</td>' . "\n\t\t\t\t\t\t";
            }
        }
        return $str;
    }

    private function buildForm(ProjectModel $projectModel): string {
        $str = '';
        foreach($projectModel->fields as $field) {
            $fieldController = new FieldsController($field);
            if($field->in_view)     {
                $str .= "\n\n" . $fieldController->getField()->getModalInput();
            }
        }
        return $str;
    }

    private function buildScripts(ProjectModel $projectModel): string {
        $str = '';
        foreach($projectModel->fields as $field) {
            $fieldController = new FieldsController($field);
            if($field->in_view)     {
                $str .= "\n\n" . $fieldController->getField()->getScripts();
            }
        }
        return $str;
    }
}
