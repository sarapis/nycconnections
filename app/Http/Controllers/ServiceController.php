<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Functions\Airtable;
use App\Service;
use App\Servicelocation;
use App\Servicephone;
use App\Servicedetail;
use App\Serviceaddress;
use App\Location;
use App\Airtables;
use App\Taxonomy;
use App\Services\Stringtoint;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    

    public function airtable()
    {

        Service::truncate();
        Servicelocation::truncate();
        Serviceaddress::truncate();
        Servicephone::truncate();
        Servicedetail::truncate();

        $airtable = new Airtable(array(
            'api_key'   => 'keyIvQZcMYmjNbtUO',
            'base'      => 'appX6ZQc7Je8AIzMI',
        ));

        $request = $airtable->getContent( 'services' );
        $size = '';
        do {


            $response = $request->getResponse();

            $airtable_response = json_decode( $response, TRUE );

            foreach ( $airtable_response['records'] as $record ) {


                $service = new Service();
                $strtointclass = new Stringtoint();
                $service->service_recordid= $strtointclass->string_to_int($record[ 'id' ]);
                $service->service_name = isset($record['fields']['name'])?$record['fields']['name']:null;
                $service->service_organization = isset($record['fields']['organization'])? implode(",", $record['fields']['organization']):null;
                $service->service_organization = $strtointclass->string_to_int($service->service_organization);
                $service->service_alternate_name = isset($record['fields']['alternate_name'])?$record['fields']['alternate_name']:null;
                $service->service_description = isset($record['fields']['description'])?$record['fields']['description']:null;

                if(isset($record['fields']['locations'])){
                    $i = 0;
                    foreach ($record['fields']['locations']  as  $value) {
                        $service_location = new Servicelocation();
                        $service_location->service_recordid=$service->service_recordid;
                        $service_location->location_recordid=$strtointclass->string_to_int($value);
                        $service_location->save();
                        $servicelocation=$strtointclass->string_to_int($value);

                        if($i != 0)
                            $service->service_locations = $service->service_locations. ','. $servicelocation;
                        else
                            $service->service_locations = $servicelocation;
                        $i ++;
                    }
                }
                
                $service->service_url = isset($record['fields']['url'])?$record['fields']['url']:null;
                $service->service_email = isset($record['fields']['email'])?$record['fields']['email']:null;
                $service->service_status = isset($record['fields']['status'])?$record['fields']['status']:null;
                $service->service_taxonomy = isset($record['fields']['taxonomy'])? implode(",", $record['fields']['taxonomy']):null;

                $service->service_taxonomy= $strtointclass->string_to_int($service->service_taxonomy);

                $service->service_application_process = isset($record['fields']['application_process'])?$record['fields']['application_process']:null;
                $service->service_wait_time = isset($record['fields']['wait_time'])?$record['fields']['wait_time']:null;
                $service->service_fees = isset($record['fields']['fees'])?$record['fields']['fees']:null;
                $service->service_accreditations = isset($record['fields']['accreditations'])?$record['fields']['accreditations']:null;
                $service->service_licenses = isset($record['fields']['licenses'])?$record['fields']['licenses']:null;


                if(isset($record['fields']['phones'])){
                    $i = 0;
                    foreach ($record['fields']['phones']  as  $value) {
                        $service_phone = new Servicephone();
                        $service_phone->service_recordid=$service->service_recordid;
                        $service_phone->phone_recordid=$strtointclass->string_to_int($value);
                        $service_phone->save();
                        $servicephone=$strtointclass->string_to_int($value);

                        if($i != 0)
                            $service->service_phones = $service->service_phones. ','. $servicephone;
                        else
                            $service->service_phones = $servicephone;
                        $i ++;
                    }
                }


                $service->service_schedule = isset($record['fields']['schedule'])? implode(",", $record['fields']['schedule']):null;
                $service->service_contacts = isset($record['fields']['contacts'])? implode(",", $record['fields']['contacts']):null;

                if(isset($record['fields']['details'])){
                    $i = 0;
                    foreach ($record['fields']['details']  as  $value) {
                        $service_detail = new Servicedetail();
                        $service_detail->service_recordid=$service->service_recordid;
                        $service_detail->detail_recordid=$strtointclass->string_to_int($value);
                        $service_detail->save();
                        $servicedetail=$strtointclass->string_to_int($value);

                        if($i != 0)
                            $service->service_details = $service->service_details. ','. $servicedetail;
                        else
                            $service->service_details = $servicedetail;
                        $i ++;
                    }
                }

                if(isset($record['fields']['address'])){
                    $i = 0;
                    foreach ($record['fields']['address']  as  $value) {
                        $service_addresses = new Serviceaddress();
                        $service_addresses->service_recordid=$service->service_recordid;
                        $service_addresses->address_recordid=$strtointclass->string_to_int($value);
                        $service_addresses->save();
                        $serviceaddress=$strtointclass->string_to_int($value);

                        if($i != 0)
                            $service->service_address = $service->service_address. ','. $serviceaddress;
                        else
                            $service->service_address = $serviceaddress;
                        $i ++;
                    }
                }

                $service->service_metadata = isset($record['fields']['metadata'])? $record['fields']['metadata']:null;              
                
                $service ->save();

            }
           
        }
        while( $request = $response->next() );

        $date = date("Y/m/d H:i:s");
        $airtable = Airtables::where('name', '=', 'Services')->first();
        $airtable->records = Service::count();
        $airtable->syncdate = $date;
        $airtable->save();
    }


    public function index()
    {
        $services = Service::with('locations')->orderBy('service_name')->paginate(10);
        

        return view('backEnd.tables.tb_services', compact('services'));
    }


    public function services()
    {
        $services = Service::with('locations')->orderBy('service_name')->paginate(10);
        $locations = Location::with('services','organization')->get();

        return view('frontEnd.services', compact('services', 'locations'));
    }

    public function service($id)
    {
        $service = Service::where('service_recordid', '=', $id)->first();
        $location = Location::with('organization', 'address')->where('location_services', '=', $id)->get();

        return view('frontEnd.service', compact('service', 'location'));
    }

    public function taxonomy($id)
    {
        $chip_name = Taxonomy::find($id)->value('taxonomy_name');
        $chip_title = 'Category:';
        $services = Service::where('service_taxonomy', '=', $id)->orderBy('service_name')->paginate(10);
        $locations = Location::where('location_organization', '=', $id)->with('services','organization')->get();

        return view('frontEnd.chip', compact('services', 'locations', 'chip_title', 'chip_name'));
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
        $service = Service::find($id);
        return response()->json($service);
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
        $service = Service::find($id);
        // $project = Project::where('id', '=', $id)->first();
        $service->service_name = $request->service_name;
        $service->service_alternate_name = $request->service_alternate_name;
        $service->service_description = $request->service_description;
        $service->service_url = $request->service_url;
        $service->service_email = $request->service_email;
        $service->service_status = $request->service_status;
        $service->service_application_process = $request->service_application_process;
        $service->service_wait_time = $request->service_wait_time;
        $service->service_fees = $request->service_fees;
        $service->service_accreditations = $request->service_accreditations;
        $service->service_metadata = $request->service_metadata;

        $service->flag = 'modified';
        $service->save();
        // var_dump($project);
        // exit();
        return response()->json($service);
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
