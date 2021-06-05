<?php

namespace App\Http\Controllers;

use App\Helpers\FieldType;
use App\Http\Requests\ProjectModel\CreateProjectModel;
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
use App\Models\Project;
use App\Models\ProjectModel;
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

    private function executeProjectJob(Project $project) {
        $project->update(['deploy_status' => 0]);
        DeleteProjectJob::dispatch($project);
        CreateProjectJob::dispatch($project);
        CreateEnvExampleJob::dispatch($project);
        CreateRoutesJob::dispatch($project);
        CreateControllersJob::dispatch($project);
        CreateModelsJob::dispatch($project);
        CreateMenuJob::dispatch($project);
        CreateLivewireComponentLogicJob::dispatch($project);
        CreateIndexViewJob::dispatch($project);
        CreateLivewireComponentViewJob::dispatch($project);
        CreateMigrationsJob::dispatch($project);
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

        $this->executeProjectJob($project);

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
     * @param \App\Http\Requests\ProjectModel\CreateProjectModel $request
     * @param \App\Models\ProjectModel $projectModel
     * @return void
     */
    public function update(CreateProjectModel $request, ProjectModel $projectModel)
    {
        $data = $request->validated();
        $project = Project::find($data['project_id']);

        if($projectModel->project_model_id != $data['project_model_id']) {
            $index = $project->menu()->where('project_model_id', $data['project_model_id'])->orderBy('order', 'desc')->first();

            $data['order'] = $index ? $index->order + 1 : 0;

        }

        $projectModel->update($data);
        $projectModel->fields()->delete();

        foreach ($data['fields'] as $fieldData) {
            $field = $projectModel->fields()->create($fieldData);

            foreach($fieldData['validations'] as $key=>$validation) {
                if($validation['name'] == "values") {
                    $validation['value'] = json_encode($validation['value']);
                    $fieldData['validations'][$key] = $validation;
                }
            }

            $field->validations()->createMany($fieldData['validations']);
        }

        $this->executeProjectJob($project);

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

        $index = $project->menu()->where('project_model_id', null)->orderBy('order', 'desc')->first();

        if($index) {
            $index = $index->order;

            foreach($projectModel->projectModels as $child) {
                $child->order = $index;
                $index++;
            }
        }

        ModelField::where('type', FieldType::BELONGS_TO)->with(['validations' => function($query) use ($projectModel) {
            $query->where('crud', $projectModel->id);
        }])->delete();

        $projectModel->delete();

        $this->executeProjectJob($project);
        return true;
    }
}
