<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\ProjectModel;
use Illuminate\Http\Request;

class CheckIfModelNameExistsController extends Controller
{

    protected $rules = [
        'project_id' => 'required|exists:projects,id',
        'name' => 'required|string',
    ];

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate($this->rules);

        return ProjectModel::where($data)->exists() ? 'true' : 'false';
    }
}
