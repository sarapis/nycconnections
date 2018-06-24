<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Functions\Airtable;
use App\Taxonomy;
use App\Airtables;
use App\Services\Stringtoint;

class TaxonomyController extends Controller
{

    public function airtable()
    {

        Taxonomy::truncate();
        $airtable = new Airtable(array(
            'api_key'   => 'keyIvQZcMYmjNbtUO',
            'base'      => 'appX6ZQc7Je8AIzMI',
        ));

        $request = $airtable->getContent( 'taxonomy' );

        do {


            $response = $request->getResponse();

            $airtable_response = json_decode( $response, TRUE );

            foreach ( $airtable_response['records'] as $record ) {

                $taxonomy = new Taxonomy();
                $strtointclass = new Stringtoint();

                $taxonomy->taxonomy_recordid = $strtointclass->string_to_int($record[ 'id' ]);
                 // $taxonomy->taxonomy_recordid = $record[ 'id' ];
                $taxonomy->taxonomy_name = isset($record['fields']['Name'])?$record['fields']['Name']:null;
                $taxonomy->taxonomy_parent_name = isset($record['fields']['parent_name'])? implode(",", $record['fields']['parent_name']):null;
                if($taxonomy->taxonomy_parent_name!=null){
                    $taxonomy->taxonomy_parent_name = $strtointclass->string_to_int($taxonomy->taxonomy_parent_name);
                }

                if(isset($record['fields']['Organizations'])){
                    $i = 0;
                    foreach ($record['fields']['Organizations']  as  $value) {

                        $taxonomyorganization=$strtointclass->string_to_int($value);

                        if($i != 0)
                            $taxonomy->taxonomy_organizations = $taxonomy->taxonomy_organizations. ','. $taxonomyorganization;
                        else
                            $taxonomy->taxonomy_organizations = $taxonomyorganization;
                        $i ++;
                    }
                }

                $taxonomy->taxonomy_attachments = isset($record['fields']['Attachments'])?$record['fields']['Attachments']:null; 

                $taxonomy ->save();

            }
            
        }
        while( $request = $response->next() );

        $date = date("Y/m/d H:i:s");
        $airtable = Airtables::where('name', '=', 'Taxonomy')->first();
        $airtable->records = Taxonomy::count();
        $airtable->syncdate = $date;
        $airtable->save();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxonomies = Taxonomy::orderBy('taxonomy_name')->get();

        return view('backEnd.tables.tb_taxonomy', compact('taxonomies'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $taxonomy= Taxonomy::find($id);
        return response()->json($taxonomy);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $taxonomy = Taxonomy::find($id);
        $taxonomy->taxonomy_name = $request->taxonomy_name;
        $taxonomy->taxonomy_vocabulary = $request->taxonomy_vocabulary;
        $taxonomy->taxonomy_x_description = $request->taxonomy_x_description;
        $taxonomy->taxonomy_x_notes = $request->taxonomy_x_notes;
        $taxonomy->flag = 'modified';
        $taxonomy->save();

        return response()->json($taxonomy);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
