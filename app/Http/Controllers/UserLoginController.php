<?php

namespace App\Http\Controllers;

use App\Models\user_login;
use Illuminate\Http\Request;

class UserLoginController extends Controller
{

    public function login(Request $request)
    {
        dd($request);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\user_login  $user_login
     * @return \Illuminate\Http\Response
     */
    public function show(user_login $user_login)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\user_login  $user_login
     * @return \Illuminate\Http\Response
     */
    public function edit(user_login $user_login)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\user_login  $user_login
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, user_login $user_login)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user_login  $user_login
     * @return \Illuminate\Http\Response
     */
    public function destroy(user_login $user_login)
    {
        //
    }
}
