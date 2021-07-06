<?php

namespace App\Http\Controllers;

use App\Helpers\FieldType;
use App\Http\Requests\ProjectModel\CreateProjectModel;
use App\Http\Requests\ProjectModel\UpdateProjectModel;
use App\Jobs\CreateControllersJob;
use App\Jobs\CreateEnvExampleJob;
use App\Jobs\CreateIndexViewJob;
use App\Jobs\CreateLivewireComponentLogicJob;
use App\Jobs\CreateLivewireComponentViewJob;
use App\Jobs\CreateMenuJob;
use App\Jobs\CreateMigrationsJob;
use App\Jobs\CreateModelsJob;
use App\Jobs\CreateProjectJob;
use App\Jobs\CreateRoutesJob;
use App\Jobs\DeleteProjectJob;
use App\Models\ModelField;
use App\Models\ModelFieldValidation;
use App\Models\Project;
use App\Models\ProjectModel;
use BeyondCode\ErdGenerator\Model;
use Illuminate\Http\Request;

class ProjectModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ProjectModel\CreateProjectModel $request
     * @return bool
     */
    public function store(CreateProjectModel $request)
    {
        $data = $request->validated();
        $project = Project::find($data['project_id']);
        $project->touch();

        $index = $project->menu()->where('project_model_id', $data['project_model_id'])->orderBy('order', 'desc')->first();

        $data['order'] = $index ? $index->order + 1 : 0;

        $model = ProjectModel::create($data);

        $model->load('fields', 'fields.validations');

        foreach ($data['fields'] as $fieldData) {
            $field = $model->fields()->create($fieldData);

            foreach($fieldData['validations'] as $key=>$validation) {
                if($validation['name'] == "values") {
                    $validation['value'] = json_encode($validation['value']);
                    $fieldData['validations'][$key] = $validation;
                }
            }

            $field->validations()->createMany($fieldData['validations']);
        }

        Project::executeProjectJob($project);

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectModel $projectModel
     * @return ProjectModel
     */
    public function show(ProjectModel $projectModel): ProjectModel
    {
        $projectModel->load(['fields', 'fields.validations']);

        return $projectModel;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ProjectModel\UpdateProjectModel $request
     * @param \App\Models\ProjectModel $projectModel
     * @return void
     */
    public function update(UpdateProjectModel $request, ProjectModel $projectModel)
    {
        $data = $request->validated();
        $project = Project::find($data['project_id']);

        if($projectModel->project_model_id != $data['project_model_id']) {
            $index = $project->menu()->where('project_model_id', $data['project_model_id'])->orderBy('order', 'desc')->first();

            $data['order'] = $index ? $index->order + 1 : 0;

        }

        $projectModel->update($data);
        $projectModel->touch();
        $projectModel->project->touch();

        $ids = [];
        foreach ($data['fields'] as $fieldData) {
            if(is_null($fieldData['id'])) {
                $field = $projectModel->fields()->create($fieldData);
            } else {
                $field = ModelField::find($fieldData['id']);
                $field->update($fieldData);
            }
            $field->validations()->delete();
            foreach($fieldData['validations'] as $key=>$validation) {
                if($validation['name'] == "values") {

                    if(is_array($validation['value'])) {
                        $validation['value'] = json_encode($validation['value']);
                    }
                    $fieldData['validations'][$key] = $validation;
                }
            }

            $field->validations()->createMany($fieldData['validations']);
            array_push($ids, $field->id);
        }

        $projectModel->fields()->whereNotIn('id', $ids)->delete();

        Project::executeProjectJob($project);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectModel $projectModel)
    {
        $project = $projectModel->project;
        $projectModel->project->touch();

        $index = $project->menu()->where('project_model_id', null)->orderBy('order', 'desc')->first();

        if($index) {
            $index = $index->order;

            foreach($projectModel->projectModels as $child) {
                $child->order = $index;
                $index++;
            }
        }

        $validations = ModelFieldValidation::where(['name' => 'crud', 'value' => $projectModel->id])->get();

        foreach($validations as $validation) {
            $validation->modelField->delete();
        }

        $projectModel->delete();

        Project::executeProjectJob($project);
        return true;
    }
}
