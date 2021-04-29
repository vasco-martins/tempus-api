<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Jobs\CreateProjectJob;
use App\Models\Project;
use Illuminate\Http\Request;

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

        CreateProjectJob::dispatch($project);

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
}
