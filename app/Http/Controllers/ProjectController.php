<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Jobs\CreateEnvExampleJob;
use App\Jobs\CreateProjectJob;
use App\Jobs\DeleteProjectJob;
use App\Jobs\DeployProjectJob;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpZip\ZipFile;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

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

        DeleteProjectJob::dispatch($project);
        CreateProjectJob::dispatch($project);
        CreateEnvExampleJob::dispatch($project);

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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

}
