<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\ProjectModel;
use Illuminate\Http\Request;

class ShowParentMenusController extends Controller
{
    protected $rules = [
        'project_id' => 'required|exists:projects,id',
    ];

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate($this->rules);
        $data['is_parent'] = 1;

        return ProjectModel::where($data)->pluck(
             'label', 'id'
        );
    }
}
