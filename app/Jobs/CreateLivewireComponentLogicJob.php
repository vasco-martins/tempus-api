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
                '#--LIVEWIRE-IMPORT--#',
                '#--MODEL-NAME--#',
                '#--LOWERCASE-MODEL-NAME--#',
                '#--LOWERCASE-MODEL-NAME-PLURAL--#',
                '#--LABEL--#',
                '#--LABEL-SINGULAR--#',
                '#--RULES--#',
                '#--FIELDS-DECLARATION--#',
                '#--RESET-INPUTS--#',
                '#--RELATIONSHIPS--#',
                '#--SEARCHABLE-FIELDS--#'
            ], [
                'use App\\Models\\' . $projectModel->name . ';',
                $projectModel->controller_name,
                $projectModel->name,
                Str::lower(Str::singular($projectModel->name)),
                Str::lower(Str::plural($projectModel->name)),
                $projectModel->label,
                Str::singular($projectModel->label),
                $this->buildRules($projectModel),
                $this->buildFieldsDeclaration($projectModel),
                $this->buildResetInputs($projectModel),
                $this->buildRelationships($projectModel),
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

        if($field->type == FieldType::BELONGS_TO) {
            $str .= 'integer|';
        }

        if($field->type == FieldType::DATE) {
            $str .= 'date|';
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
                        $str .= 'unique:' . $field->model->database_name  . ',' . $field->database_name . '\'. $ignore .\'|';
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
            $str .=  "\n\t\t\t";

            if($field->type == FieldType::BELONGS_TO) {
                $str .= '$this' . "->$field->database_name = '';";
                continue;
            }

            if($field->type == FieldType::BELONGS_TO_MANY) {
                $relation = ProjectModel::find($this->getValidation($field, 'crud'));
                $str .= 'isset($' . Str::lower(Str::singular($projectModel->name)) .') ?
                $' . Str::lower(Str::singular($projectModel->name)) .'->' . Str::camel(Str::plural($relation->label)) . "->pluck('" . $field->database_name . "')->implode(', ') : [];";
                continue;

            }
            if($field->type == FieldType::PASSWORD) {
                $str .= '$this' . "->$field->database_name = '';";
                continue;
            }

            $str .= '$this' . "->$field->database_name = $" .  Str::lower(Str::singular($projectModel->name)) . "->" . $field->database_name . ' ??';
            switch ($field->type) {
                case FieldType::STRING:
                case FieldType::TEXTAREA:
                case FieldType::EMAIL:
                case FieldType::TEXT:
                case FieldType::SELECT:
                case FieldType::DATE:
                     $str .= "''";
                    break;
                case FieldType::NUMBER:
                case FieldType::BELONGS_TO:
                case FieldType::BOOLEAN:
                    $str .= "0";
                    break;
                default:
                    break;
            }
            $str .= ';';
        }

        //$hasSelect = false;
        $str .=  "\n\t\t" . 'if($' . Str::lower(Str::singular($projectModel->name)) . ') {' . "\n\t\t\t";

        foreach ($projectModel->fields as $field) {
            if($field->type == FieldType::SELECT || $field->type == FieldType::BELONGS_TO) {

                $value = '$this->emit(\'changeInput\', [\'id\' => \'' . $field->database_name .'\', \'value\' => $' . Str::lower(Str::singular($projectModel->name)) .'->' . $field->database_name .']);' . "\n\t\t\t";

                $str .= $value;
                //$hasSelect = true;

            }

            if($field->type == FieldType::BELONGS_TO_MANY) {
                $relation = ProjectModel::find($this->getValidation($field, 'crud'));
                $name = Str::endsWith($field->database_name, '_id') ? $field->database_name : $field->database_name . '_id';

                $value = '$this->emit(\'changeInput\', [\'id\' => \'' . $name .'\', \'value\' => $' . Str::lower(Str::singular($projectModel->name)) .'->' . Str::camel(Str::plural($relation->label)) .'->pluck(\'id\')]);' . "\n\t\t\t";

                $str .= $value;
            }

        }
        $str .= '}';


        return $str;
    }

    private function buildSearchableFields(ProjectModel $projectModel): string
    {
        $str = "'id',";
        foreach ($projectModel->fields as $field) {
            $fieldController = new FieldsController($field);
             if($fieldController->getField()->isSearchable) {
                $str .= "'$field->database_name',";
             }
        }
        return $str;
    }

    private function buildRelationships($projectModel)
    {
       $str = '';

        foreach ($projectModel->fields as $field) {
            if($field->type == FieldType::BELONGS_TO_MANY) {
                $relation = ProjectModel::find($this->getValidation($field, 'crud'));

                $str .= 'isset($this->' . Str::lower(Str::singular($projectModel->name)) .') ?
                $this->' . Str::lower(Str::singular($projectModel->name)) .'->' . Str::camel(Str::plural($relation->label)) . '()->sync($this->' . $field->database_name . ') :
                $' . Str::lower(Str::singular($projectModel->name)) . '->' . Str::camel(Str::plural($relation->label)) . '()->attach($this->' . $field->database_name . ');' . "\n\t\t\t";
            }
        }

        return $str;
    }

    protected function getValidation(ModelField $modelField, $validationName, $default = null) {
        foreach ($modelField->validations as $validation) {
            if($validation->name == $validationName) {
                return $validation->value;
            }
        }

        return $default;
    }

}
