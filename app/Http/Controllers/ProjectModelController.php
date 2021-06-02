<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectModel\CreateProjectModel;
use App\Jobs\CreateControllersJob;
use App\Jobs\CreateIndexViewJob;
use App\Jobs\CreateLivewireComponentLogicJob;
use App\Jobs\CreateLivewireComponentViewJob;
use App\Jobs\CreateMenuJob;
use App\Jobs\CreateMigrationsJob;
use App\Jobs\CreateModelsJob;
use App\Jobs\CreateRoutesJob;
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

        CreateRoutesJob::dispatch($project);
        CreateControllersJob::dispatch($project);
        CreateModelsJob::dispatch($project);
        CreateMenuJob::dispatch($project);
        CreateLivewireComponentLogicJob::dispatch($project);
        CreateIndexViewJob::dispatch($project);
        CreateLivewireComponentViewJob::dispatch($project);
        CreateMigrationsJob::dispatch($project);

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
