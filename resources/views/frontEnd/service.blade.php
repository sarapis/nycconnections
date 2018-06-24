@extends('layouts.app')
@section('title')
Service
@stop
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">


<style type="text/css">
.table a{
    text-decoration:none !important;
    color: rgba(40,53,147,.9);
    white-space: normal;
}
.footable.breakpoint > tbody > tr > td > span.footable-toggle{
    position: absolute;
    right: 25px;
    font-size: 25px;
    color: #000000;
}
.ui-menu .ui-menu-item .ui-state-active {
    padding-left: 0 !important;
}
ul#ui-id-1 {
    width: 260px !important;
}
#map{
    position: relative !important;
    z-index: 0 !important;
}
</style>

@section('content')
<div class="wrapper">
    @include('layouts.sidebar')
    <!-- Page Content Holder -->
    <div id="content" class="container">
        <!-- <div id="map" style="height: 30vh;"></div> -->
        <!-- Example Striped Rows -->
        <div class="row" style="margin-right: 0">
            <div class="col-md-8 pt-15 pr-0">
    
                <div class="panel ml-15 mr-15">
                    <div class="panel-body p-20">
                        <h2>{{$service->service_name}}</h2>
                        <h4 class="panel-text"><span class="badge bg-red">Alternate Name:</span> {{$service->service_alternate_name}}</h4>

                        @if($service->service_taxonomy!=0)
                        <h4><span class="badge bg-red">Category:</span> <a class="panel-link" href="/category_{{$service->taxonomy()->first()->taxonomy_recordid}}"> {{$service->taxonomy()->first()->taxonomy_name}}</a></h4>
                        @endif

                        @if($service->service_organization!=null)
                        <h4><span class="badge bg-red">Organization:</span><a class="panel-link" href="/organization_{{$service->organization()->first()->organization_recordid}}"> {{$service->organization()->first()->organization_name}}</a></h4>
                        @endif

                        <h4 class="panel-text"><span class="badge bg-blue">Description:</span> {!! $service->service_description !!}</h4>

                        <h4 class="panel-text"><span class="badge bg-red">Phone:</span> @foreach($service->phone as $phone) {!! $phone->phone_number !!}, @endforeach</h4>

                        <h4 class="panel-text" style="word-wrap: break-word;"><span class="badge bg-blue" >Url:</span> @if($service->service_url!=NULL) {!! $service->service_url !!} @endif</h4>

                        @if($service->service_email!=NULL) 
                        <h4 class="panel-text"><span class="badge bg-blue">Email:</span> {{$service->service_email}}</h4>
                        @endif

                        <hr>

                        <h3>Additional Info</h3>

                        <h4 class="panel-text"><span class="badge bg-blue">Application Process:</span> {!! $service->service_application_process !!}</h4>

                        <h4 class="panel-text"><span class="badge bg-blue">Wait Time:</span> {{$service->service_wait_time}}</h4>

                        <h4 class="panel-text"><span class="badge bg-blue">Fees:</span> {{$service->service_fees}}</h4>

                        <h4 class="panel-text"><span class="badge bg-blue">Accreditations:</span> {{$service->service_accreditations}}</h4>

                        <h4 class="panel-text"><span class="badge bg-blue">Licenses:</span> {{$service->service_licenses}}</h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 p-0">
                <div id="map" style="position: fixed !important;width: 28%;"></div>
                <hr>
                @if($service->service_address!=NULL)
                <h4><span class="badge bg-blue">Address:</span>
                    
                        @foreach($service->address as $address)
                           <br>{{ $address->address_1 }} {{ $address->address_city }} {{ $address->address_state_province }} {{ $address->address_postal_code }}
                        @endforeach
                    
                </h4>
                @endif
                @if($service->service_contacts!=0)
                <h4><span class="badge bg-red">Contact:</span>
                  
                    {{$service->contact()->first()->contact_name}}
                
                </h4>
                @endif

                <h3>Details</h3>
                @if($service->service_details!=NULL)
                
                  @foreach($service->detail as $detail)
                    <h4><span class="badge bg-red">{{ $detail->detail_type }}:</span> {!! $detail->detail_value !!}</h4>
                  @endforeach
 
                @endif
            </div>
        </div>
    </div>
</div>

<!-- <script>
    $(document).ready(function(){
        if(screen.width < 768){
          var text= $('.navbar-header').css('height');
          var height = text.slice(0, -2);
          $('.page').css('padding-top', height);
          $('#content').css('top', height);
        }
        else{
          var text= $('.navbar-header').css('height');
          var height = 0;
          $('.page').css('margin-top', height);
        }
    });
</script> -->
<script>
    var locations = <?php print_r(json_encode($location)) ?>;
    var show = 1;
    if(locations.length == 0){
      show = 0;
      locations[0] = {};
      locations[0].location_latitude = 40.730981;
      locations[0].location_longitude = -73.998107;
    }

    var sumlat = 0.0;
    var sumlng = 0.0;
    for(var i = 0; i < locations.length; i ++)
    {
        sumlat += parseFloat(locations[i].location_latitude);
        sumlng += parseFloat(locations[i].location_longitude);

    }
    var avglat = sumlat/locations.length;
    var avglng = sumlng/locations.length;
    var mymap = new GMaps({
      el: '#map',
      lat: avglat,
      lng: avglng,
      zoom:10
    });

    if(show == 1){
      $.each( locations, function(index, value ){
          mymap.addMarker({
              lat: value.location_latitude,
              lng: value.location_longitude,
              title: value.city,
                     
              
          });
     });
    }



</script>
@endsection


