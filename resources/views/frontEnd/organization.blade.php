@extends('layouts.app')
@section('title')
Organization
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
    
                <div class="panel ml-15 mr-15 mb-15">
                    <div class="panel-body p-20">
                        <h2>{{$organization->organization_name}} @if($organization->organization_alternate_name!='')({{$organization->organization_alternate_name}})@endif</h2>

                        @if($organization->organization_x_chapter!=null)
                        <h4 class="panel-text"><span class="badge bg-red">Category:</span> <a class="panel-link" href="/category_{{$organization->taxonomy()->first()->taxonomy_recordid}}"> {{$organization->taxonomy()->first()->taxonomy_name}}</a></h4>
                        @endif

                        <h4 class="panel-text"><span class="badge bg-red">Description:</span> {{$organization->organization_description}}</h4>

                        <h4 class="panel-text"><span class="badge bg-red">Website</span> <a class="panel-link" href="http://{{$organization->organization_url}}" target="_blank">{{$organization->organization_url}}</a></h4>

                        @if($organization->organization_phones!='')
                        <h4 class="panel-text"><span class="badge bg-red">Main Phone:</span> @foreach($organization->phone as $phone)
                           {!! $phone->phone_number !!}, 
                        @endforeach</h4>
                        @endif

                    </div>
                  </div>
              
            </div>
            
            <div class="col-md-4 p-0 pr-15">
                <div id="map" style="width: 100%;"></div>
                
                @if($organization->organization_locations!='')
                  <hr>
                  @foreach($organization->location as $location)
                  <div class="panel m-0 mt-5">
                      <div class="panel-body p-20">

                          <h4><span class="badge bg-red">Location:</span> {{$location->location_name}}</h4>
                          <h4><span class="badge bg-red">Address:</span> @if($location->location_address!='')
                            @foreach($location->address as $address)
                              {{ $address->address_1 }} {{ $address->address_2 }} {{ $address->address_city }} {{ $address->address_state_province }} {{ $address->address_postal_code }}
                            @endforeach
                          @endif
                          </h4>
                          <h4><span class="badge bg-red">Phone:</span>
                             @foreach($location->phones as $phone)
                              {{$phone->phone_number}},
                             @endforeach
                          </h4>
                      </div>
                  </div>
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
    
    var locations = <?php print_r(json_encode($locations)) ?>;
    var organization = <?php print_r(json_encode($organization->organization_name)) ?>;


    var sumlat = 0.0;
    var sumlng = 0.0;
    var length = 0;

    for(var i = 0; i < locations.length; i ++)
    {
        if(locations[i].location_latitude)
        {
            sumlat += parseFloat(locations[i].location_latitude);
            sumlng += parseFloat(locations[i].location_longitude);
            length ++;
        }
    }
    if(length != 0){
        var avglat = sumlat/length;
        var avglng = sumlng/length;
    }
    else
    {
        avglat = 40.730981;
        avglng = -73.998107;
    }
 
    var mymap = new GMaps({
      el: '#map',
      lat: avglat,
      lng: avglng,
      zoom:15
    });


    $.each( locations, function(index, value ){
  
        if(value.location_latitude){
            mymap.addMarker({

                lat: value.location_latitude,
                lng: value.location_longitude,
                title: value.city,
                       
                infoWindow: {
                    maxWidth: 250,
                    content: ('<a href="" style="color:#424242;font-weight:500;font-size:14px;">'+organization+'</a>')
                }
            });
        }
   });
</script>
@endsection


