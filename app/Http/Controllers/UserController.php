<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function update(UpdateUserRequest $request, User $user) {
        $data = $request->validated();

        $user->update($data);

        return $user;

    }


}
