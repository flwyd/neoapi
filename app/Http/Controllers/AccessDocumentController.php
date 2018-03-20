<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessDocument;
use App\Http\Controllers\ApiHouseController;

class AccessDocumentController extends ApihouseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = request()->validate([
            'year'      => 'required|digits:4',
            'person_id' => 'sometimes|numeric'
        ]);

        //$this->authorize('index', [ AccessDocument::class, $query['person_id'] ]);

        return $this->jsonApi(AccessDocument::findForQuery($query), false);
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
     * @param  \App\AccessDocument  $accessDocument
     * @return \Illuminate\Http\Response
     */
    public function show(AccessDocument $accessDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AccessDocument  $accessDocument
     * @return \Illuminate\Http\Response
     */
    public function edit(AccessDocument $accessDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AccessDocument  $accessDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccessDocument $accessDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccessDocument  $accessDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccessDocument $accessDocument)
    {
        //
    }
}
