<?php

namespace App\Jobs;

use App\Fields\FieldsController;
use App\Helpers\FieldType;
use App\Models\ModelField;
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
        $editStub = file_get_contents(base_path('stubs/coreui/database/add_migration.stub'));
       // $userStub = file_get_contents(base_path('stubs/coreui/database/usersTable.stub'));


        foreach ($this->project->projectModels as $projectModel) {

            $replacement = str_replace([
                '#--MIGRATION-CLASS--#',
                '#--TABLE-NAME--#',
                '#--MIGRATIONS--#'
            ], [
                'Create' . Str::ucfirst(Str::plural(Str::camel($projectModel->name))) . 'Table',
                $projectModel->database_name,
                $this->generateMigrations($projectModel)
            ],  $stub);

            file_put_contents(
                $this->project->folder . '/database/migrations/' . $projectModel->created_at->format('Y_m_d_His') . '_create_' . $projectModel->database_name . '_table.php',
                $replacement);
        }

        foreach($this->project->projectModels as $projectModel) {
            $fields = $projectModel->fields()->where('type', FieldType::BELONGS_TO)->get();

            if($fields->isEmpty()) {
                continue;
            }

            $replacement = str_replace([
                '#--MIGRATION-CLASS--#',
                '#--TABLE-NAME--#',
                '#--MIGRATIONS--#'
            ], [
                'AddRelationsTo' . Str::ucfirst(Str::plural(Str::camel($projectModel->name))) . 'Table',
                $projectModel->database_name,
                $this->generateRelations($fields)
            ], $editStub);

            file_put_contents(
                $this->project->folder . '/database/migrations/' . now()->addSeconds(50)->format('Y_m_d_His') . '_add_relations_to_' . $projectModel->database_name . '_table.php',
                $replacement);

        }


       foreach($this->project->projectModels as $projectModel) {
            $fields = $projectModel->fields()->where('type', FieldType::BELONGS_TO_MANY)->get();

            foreach ($fields as $field) {
                $crudValue = null;

                foreach ($field->validations as $validation) {
                    if($validation->name == 'crud') {
                        $crudValue = $validation->value;
                    }
                }

                $crud = ProjectModel::find($crudValue);

                $names = [ucfirst(Str::singular($projectModel->database_name)), ucfirst(Str::singular($crud->database_name))];
                sort($names);
                $namesImploded = implode('', $names);


                if($fields->isEmpty()) {
                    continue;
                }

                $replacement = str_replace([
                    '#--MIGRATION-CLASS--#',
                    '#--TABLE-NAME--#',
                    '#--MIGRATIONS--#'
                ], [
                    'Create' . $namesImploded . 'Table',
                    Str::snake($namesImploded),
                    $this->generateManyToManyRelations($fields)
                ], $stub);

                file_put_contents(
                    $this->project->folder . '/database/migrations/' . now()->addSeconds(50)->format('Y_m_d_His') . '_create_' . Str::snake($namesImploded) . '_table.php',
                    $replacement);

            }

        }

    }


    private function generateManyToManyRelations( $fields): string
    {
        $str = '';

        foreach($fields as  $field) {
            $fieldController = new FieldsController($field);
            $str .=  $fieldController->getField()->getMigration() . "\n\t\t\t";
        }

        return $str;
    }

    private function generateRelations( $fields): string
    {
        $str = '';

        foreach($fields as  $field) {
            $fieldController = new FieldsController($field);
            $str .=  $fieldController->getField()->getMigration() . "\n\t\t\t";
        }

        return $str;
    }

    private function generateMigrations(ProjectModel $projectModel): string {
        $str = '';

        foreach ($projectModel->fields as $field) {
            if($field->type == FieldType::BELONGS_TO) continue;
            $fieldController = new FieldsController($field);
            $str .=  $fieldController->getField()->getMigration() . "\n\t\t\t";
        }

        return $str;
    }
}
