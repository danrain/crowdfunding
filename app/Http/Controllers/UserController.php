<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::query()->with(['roles'])->paginate();
        return response()->json($users);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* @var $user User */
        $user = new User($request->all());
        return $this->validateAndSaveUser($user, $request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    
    /**
     * 
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    protected function validateAndSaveUser(User $user, Request $request)
    {
        try {
            if (!$user->validate()) {
                return $this->returnValidationErrors($user->validator());
            }

            $user->save();
            // Attach roles for user
            if ( !$user->exists || ($user->exists && $request->get('roles')) ) {
                $roles = Role::getByCode($request->get('roles'));
                $user->roles()->sync($roles);
            }
        } catch (Exception $ex) {
            return $this->returnErrors();
        }
        
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /* @var $user User */
        $user = User::find($id);
        if ( ! $user ) {
            return $this->resourceNotFound();
        }
        $user->fill($request->all());
        return $this->validateAndSaveUser($user, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* @var $user User */
        $user = User::find($id);
        if ( ! $user ) {
            return $this->resourceNotFound();
        }
        
        try {
            $user->delete();
        } catch (Exception $ex) {
            return $this->returnErrors(null, 'An error occurred while deleting the user');
        }
        
        return response()->json([
            'success' => true
        ]);
    }
}
