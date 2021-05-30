<?php

namespace App\Jobs;

use App\Helpers\FieldType;
use App\Models\Project;
use App\Models\ProjectModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Psy\Util\Json;

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
                '#--FILLABLE--#',
                '#--HASH_FUNCTIONS--#',
                '#--SELECT-CONSTANTS--#'
            ], [
                $this->generatePasswordHashImport($projectModel),
                $projectModel->name,
                $this->generateFillable($projectModel),
                $this->generateHashFunctions($projectModel),
                $this->generateSelectConstants($projectModel)
            ], $stub);

            file_put_contents($this->project->folder. '/app/Models/' . $projectModel->name . '.php', $replacement);

        }

    }

    private function generateFillable(ProjectModel $projectModel): string
    {
        $str = '';
        foreach($projectModel->fields as $field) {
            $str .= "'$field->database_name',\n\t\t";
        }
        return $str;
    }

    private function generatePasswordHashImport(ProjectModel $projectModel): string
    {
        foreach($projectModel->fields as $field) {
            if($field->type == FieldType::PASSWORD) {
                return 'use Illuminate\Support\Facades\Hash;';
            }
        }
        return '';
    }

    private function generateHashFunctions(ProjectModel $projectModel): string
    {
        $str = '';
        foreach($projectModel->fields as $field) {
            if($field->type == FieldType::PASSWORD) {
                $camelCaseWithUCFirst = ucfirst(Str::camel($field->database_name));
                $str .= 'public function set' . $camelCaseWithUCFirst . 'Attribute($value) {
        if(!empty($value)) {
            $this->attributes[\'' . $field->database_name .'\'] = Hash::make($value);
        }
    }' . "\n\n\t";
            }
        }
        return $str;
    }

    private function generateSelectConstants(ProjectModel $projectModel): string
    {
        $str = '';
        $selectFields = $projectModel->fields()->where('type', FieldType::SELECT)->get();

        foreach ($selectFields as $field) {
            $valuesField = $field->validations()->where('name', 'values')->first();
            $valuesField = (array) json_decode($valuesField->value);
            $str .= 'public const ' . Str::upper($field->database_name) . '_SELECT = [' . "\n\t\t";
            foreach($valuesField as $valueField) {
                $str .= '"' . $valueField->name . '" => "' . $valueField->label . '"' . "\n\t\t";
            }
            $str .= "];\n\n\t";
        }

        return $str;
    }
}
