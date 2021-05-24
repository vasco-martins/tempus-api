<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParentMenu\CreateParentMenuRequest;
use App\Http\Resources\ParentMenu;
use App\Jobs\CreateMenuJob;
use App\Models\Project;
use Illuminate\Http\Request;

class ParentMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ParentMenu
     */
    public function index(Project $project): ParentMenu
    {
        return new ParentMenu($project->parentMenus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ParentMenu\CreateParentMenuRequest $request
     * @return \App\Http\Resources\ParentMenu
     */
    public function store(CreateParentMenuRequest $request, Project $project): ParentMenu
    {
        $data = $request->validated();

        $parentMenu = \App\Models\ParentMenu::create($data);

        CreateMenuJob::dispatch($project);

        return new ParentMenu($parentMenu);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ParentMenu $parentMenu
     * @return \App\Http\Resources\ParentMenu
     */
    public function show(\App\Models\ParentMenu $parentMenu): ParentMenu
    {
        return new ParentMenu($parentMenu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ParentMenu\CreateParentMenuRequest $request
     * @param \App\Models\ParentMenu $parentMenu
     * @return \App\Http\Resources\ParentMenu
     */
    public function update(CreateParentMenuRequest $request, \App\Models\ParentMenu $parentMenu): ParentMenu
    {
        $data = $request->validated();

        $parentMenu->update($data);

        return new ParentMenu($parentMenu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ParentMenu $parentMenu
     * @return int
     * @throws \Exception
     */
    public function destroy(\App\Models\ParentMenu $parentMenu)
    {
        $parentMenu->delete();

        return 1;
    }
}
