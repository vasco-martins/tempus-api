<?php

namespace App\Jobs;

use App\Models\Log;
use App\Models\ModelField;
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
use Symfony\Component\ErrorHandler\Debug;

class CreateLivewireComponentLogicJob implements ShouldQueue
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
        $stub = file_get_contents(base_path('stubs/coreui/app/livewire.stub'));

        #--MODEL-NAME--#
        #--LOWERCASE-MODEL-NAME--#
        #--SEARCHABLE-FIELDS--#
        #--LOWERCASE-MODEL-NAME-PLURAL--#
        #--LABEL--#
        #--RESET-INPUTS--#
        #--RULES--#

        foreach ($this->project->projectModels as $projectModel) {
            $replacement = str_replace([
                '#--MODEL-IMPORT--#',
                '#--MODEL-NAME--#',
                '#--LOWERCASE-MODEL-NAME--#',
                '#--LOWERCASE-MODEL-NAME-PLURAL--#',
                '#--LABEL--#',
                '#--RULES--#',
                '#--FIELDS-DECLARATION--#',
                '#--RESET-INPUTS--#',
                '#--SEARCHABLE-FIELDS--#'
            ], [
                'use App\\Models\\' . $projectModel->name . ';',
                $projectModel->name,
                Str::lower($projectModel->name),
                Str::lower(Str::plural($projectModel->name)),
                $projectModel->label,
                $this->buildRules($projectModel),
                $this->buildFieldsDeclaration($projectModel),
                $this->buildResetInputs($projectModel),
                $this->buildSearchableFields($projectModel)

            ], $stub);

            \Log::info($this->buildSearchableFields($projectModel));

            $basePath = $this->project->folder . '/app/Http/Livewire/' . $projectModel->controller_name;
            File::makeDirectory($basePath, 0777, true, true);
            file_put_contents($basePath . '/Index.php', $replacement);

        }
    }

    private function buildFieldsDeclaration(ProjectModel $projectModel): string {
        $str = '';

        foreach($projectModel->fields as $field) {
            $str .= "\n\t public $" . $field->database_name . ';';
        }

        return $str;
    }

    private function buildRules(ProjectModel $projectModel): string {
        $str = '';
        foreach($projectModel->fields as $field) {
            $str .= "\n\t\t'$field->database_name' => '" . $this->buildRulesArray($field) . "',";
        }

        return ltrim($str);
    }

    private function buildRulesArray(ModelField $field): string
    {
        $str = '';

        if($field->type == 'email') {
            $str .= 'email|';
        }

        if($field->type == 'text' || $field->type == 'string' || $field->type == 'textarea') {
            $str .= 'string|';
        }

        foreach($field->validations as $validation) {

            switch ($validation->name) {
                case 'required':
                    if($validation->value == 1) {
                        $str .= 'required|';
                    }
                    break;
                case 'max':
                case 'min':
                    $str .= $validation->name . ':' . $validation->value . '|';
                    break;
                case 'unique':
                    if($validation->value == 1) {
                        $str .= 'unique:' . $field->model->name  . ',' . $field->database_name . '|';
                    }
                    break;
                default:
                    break;

            }
        }

        return rtrim($str, '|');
    }

    private function buildResetInputs(ProjectModel $projectModel): string
    {
        $str = '';

        foreach ($projectModel->fields as $field) {
            $str .= "\n\t\t\t" .'$this' . "->$field->database_name = $" . Str::lower($projectModel->name) . "->" . $field->database_name . ' ??';
            switch ($field->type) {
                case 'string':
                case 'textarea':
                case 'email':
                case 'text':
                case 'password':
                case 'select':
                     $str .= "''";
                    break;
                case 'number':
                    $str .= "0";
                    break;
                default:
                    break;
            }
            $str .= ';';
        }

        return $str;
    }

    private function buildSearchableFields(ProjectModel $projectModel): string
    {
        $str = '';
        $fieldTypes = ['string', 'text', 'textarea', 'email'];

        foreach ($projectModel->fields as $field) {
         //   if(in_array(Str::lower($field->type), $fieldTypes)) {
                $str .= "'$field->database_name',";
   //         }
        }
        return $str;
    }
}
