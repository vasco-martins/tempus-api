<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Project */
class Project extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'project_models' => $this->projectModels,
            'parent_menus' => $this->parentMenus,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'download_link' => $this->download_link,
            'hash' => $this->hash,
            'menu' => $this->menu,
        ];
    }
}
