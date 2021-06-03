<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParentMenu\CreateParentMenuRequest;
use App\Http\Resources\ProjectModel;
use App\Jobs\CreateMenuJob;
use App\Models\Project;
use Illuminate\Http\Request;

class ParentMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Project $project
     * @return ProjectModel
     */
    public function index(Project $project): ProjectModel
    {
        return new ProjectModel($project->parentMenus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ParentMenu\CreateParentMenuRequest $request
     * @param \App\Models\Project $project
     * @return \App\Http\Resources\ProjectModel
     */
    public function store(CreateParentMenuRequest $request, Project $project): ProjectModel
    {
        $data = $request->validated();
        $data['is_parent'] = 1;

        // ORDER
        $index = $project->menu()->where('project_model_id', null)->orderBy('order', 'desc')->first();
        $index = $index->order + 1;

        $data['order'] = $index;

        $projectModel = \App\Models\ProjectModel::create($data);

        $project->update(['deploy_status' => 0]);
        CreateMenuJob::dispatch($project);

        return new ProjectModel($projectModel);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ProjectModel $projectModel
     * @return ProjectModel
     */
    public function show(\App\Models\ProjectModel $projectModel): ProjectModel
    {
        return new ProjectModel($projectModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ParentMenu\CreateParentMenuRequest $request
     * @param \App\Models\Project $project
     * @param \App\Models\ProjectModel $projectModel
     * @return bool
     */
    public function update(CreateParentMenuRequest $request, Project $project, \App\Models\ProjectModel $projectModel): bool
    {
        $data = $request->validated();

        $projectModel->update($data);

        $project->update(['deploy_status' => 0]);
        CreateMenuJob::dispatch($project);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ProjectModel $projectModel
     * @return int
     * @throws \Exception
     */
    public function destroy(\App\Models\ProjectModel $projectModel)
    {

        $projectModel->delete();

        return 1;
    }
}
