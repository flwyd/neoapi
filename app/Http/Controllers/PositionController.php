<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Http\Controllers\ApiHouseController;

use Illuminate\Http\Request;

class PositionController extends ApiHouseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$this->authorize('view');
        return $this->jsonApi(Position::findAll(), null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store', Position::class);

        $position = new \App\Models\Position;
        $position->fromJsonApi(request(), null);

        if ($position->save()) {
            return $this->jsonApi($position);
        }

        return $this->errorJsonApi($position);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position)
    {
        return $this->jsonApi($position);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        $this->authorize('update', Position::class);
        $position->fromJsonApi($request);

        if ($position->save()) {
            return $this->success($position);
        }

        return $this->errorJsonApi($position);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        $this->authorize('delete', Position::class);
        $position->delete();
    }
}
