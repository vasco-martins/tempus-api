<?php

namespace App\Jobs;

use App\Fields\Field;
use App\Fields\FieldsController;
use App\Helpers\FieldType;
use App\Models\ModelField;
use App\Models\Project;
use App\Models\ProjectModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
                '#--LABEL-SINGULAR--#',
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
                Str::singular($projectModel->label),
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
            $str .= "\n\t\t\t'$field->database_name' => '" . $this->buildRulesArray($field) . "',";
        }

        return ltrim($str);
    }

    private function buildRulesArray(ModelField $field): string
    {
        $str = '';


        if($field->type == FieldType::EMAIL) {
            $str .= 'email|';
        }

        if($field->type == FieldType::TEXT || $field->type == FieldType::STRING || $field->type == FieldType::TEXTAREA) {
            $str .= 'string|';
        }

        foreach($field->validations as $validation) {

            switch ($validation->name) {
                case 'required':
                    if($validation->value == 1) {
                        if($field->type == FieldType::PASSWORD) {
                            $str .= '\' . $this->' . Str::lower($field->model->name) . ' == null ? \'required\' : \'nullable\' . \'' ;
                            break;
                        }
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
            if($field->type == FieldType::PASSWORD) {
                $str .= "\n\t\t\t" .'$this' . "->$field->database_name = '';";
                continue;
            }
            $str .= "\n\t\t\t" .'$this' . "->$field->database_name = $" . Str::lower($projectModel->name) . "->" . $field->database_name . ' ??';
            switch ($field->type) {
                case FieldType::STRING:
                case FieldType::TEXTAREA:
                case FieldType::EMAIL:
                case FieldType::TEXT:
                case FieldType::SELECT:
                     $str .= "''";
                    break;
                case FieldType::NUMBER:
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
        foreach ($projectModel->fields as $field) {
            $fieldController = new FieldsController($field);
             if($fieldController->getField()->isSearchable && $field->is_searchable) {
                $str .= "'$field->database_name',";
             }
        }
        return $str;
    }
}
