<?php

namespace App\Http\Controllers;

use App\Helpers\FieldType;
use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Jobs\DeployProjectJob;
use App\Models\ModelField;
use App\Models\Project;
use App\Models\ProjectModel;
use PhpZip\ZipFile;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ProjectCollection
     */
    public function index()
    {
        return new ProjectCollection(auth()->user()->projects()->latest('created_at')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProjectRequest $request
     * @return \App\Http\Resources\Project
     */
    public function store(CreateProjectRequest $request)
    {
        $data = $request->validated();

        $project = auth()->user()->projects()->create($data);

       // $this->createUsersTable($project);

        Project::executeProjectJob($project);

        return new \App\Http\Resources\Project($project);
    }

    /**
     * Display the specified resource.
     *
     * @param $project
     * @return \App\Http\Resources\Project
     */
    public function show(Project $project)
    {
        abort_if($project->user->id != auth()->user()->id, 503);
        return new \App\Http\Resources\Project($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Projects\CreateProjectRequest $request
     * @param \App\Models\Project $project
     * @return bool
     */
    public function update(CreateProjectRequest $request, Project $project)
    {
        abort_if($project->user->id != auth()->user()->id, 503);

        $data = $request->validated();

        $project->update($data);

        Project::executeProjectJob($project);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @return bool
     */
    public function destroy(Project $project)
    {
        abort_if($project->user->id != auth()->user()->id, 503);

        $project->delete();

        return true;
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function download(string $hash) {
        $project = Project::where('hash', $hash)->first();

        $zipName = base_path('zipfolders/' . $project->filename . '.zip');
        $zipFile = new ZipFile();

        try {
            $zipFile->addDirRecursive($project->folder)->saveAsFile($zipName)->close();
        } catch(\PhpZip\Exception\ZipException $e){
           return false;
        }
        finally{
            $zipFile->close();
        }

        return response()->download(base_path('zipfolders/' . $project->filename . '.zip'));
    }

    public function showMenu(Project $project ) {

    }

    public function deploy(Project $project) {

        if($project->deploy_status == 0 ) {
            $project->update(['deploy_status' => 0]);
            DeployProjectJob::dispatch($project);
        }

        return true;
    }

    public function showDeployStatus(Project $project){
        $url = count(Project::DEPLOY_STATUS) == ($project->deploy_status + 1) ? $project->deploy_url : null;
        $percentage = round(( $project->deploy_status + 1) / count(Project::DEPLOY_STATUS) * 100);
        return response()->json([
            'message' => Project::DEPLOY_STATUS[$project->deploy_status],
            'url' => $url,
            'percentage' => $percentage,
        ]);
    }

    private function createUsersTable(Project $project) {
        $projectModel = ProjectModel::create([
            'name' => 'User',
            'label' => 'Utilzadores',
            'soft_delete' => 0,
            'project_id' => $project->id,
            'order' => 0

        ]);

        ModelField::create([
            'can_edit' => false,
            'label' => 'Nome',
            'type' => FieldType::STRING,
            'database_name' => 'name',
            'in_view' => true,
            'project_model_id' => $projectModel->id,
            'in_edit' => true,
            'in_create' => true,
            'is_searchable' => true
        ]);

        ModelField::create([
            'can_edit' => false,
            'label' => 'Email',
            'type' => FieldType::EMAIL,
            'database_name' => 'email',
            'in_view' => true,
            'project_model_id' => $projectModel->id,
            'in_edit' => true,
            'in_create' => true,
            'is_searchable' => true
        ]);

        ModelField::create([
            'can_edit' => false,
            'label' => 'Password',
            'type' => FieldType::PASSWORD,
            'database_name' => 'password',
            'in_view' => true,
            'project_model_id' => $projectModel->id,
            'in_edit' => true,
            'in_create' => true,
            'is_searchable' => true
        ]);
    }

}
