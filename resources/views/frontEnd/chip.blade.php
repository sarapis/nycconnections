@extends('layouts.app')
@section('title')
Organizations
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
    position: fixed !important;
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
            <div class="alert alert-alt alert-success alert-dismissible alert-chip" role="alert">
                  {{$chip_title}} <a class="alert-link">{{$chip_name}}</a>
            </div>
            <div class="col-md-12 p-0">
                <div class="col-md-8 pt-15 pr-0">
                    @foreach($organizations as $organization)

                    <div class="panel content-panel">
                        <div class="panel-body p-20">
                            
                        <a class="panel-link" href="/organization_{{$organization->organization_recordid}}">{{$organization->organization_name}}</a>
                        @if($organization->organization_x_chapter!=NULL)
                        <h4><span class="badge bg-red">Category:</span> <a class="panel-link" href="/category_{{$organization->taxonomy()->first()->taxonomy_recordid}}">{{$organization->taxonomy()->first()->taxonomy_name}}</a></h4>
                        @endif

                        <h4><span class="badge bg-red">Phone:</span> @foreach($organization->phone as $phone) {!! $phone->phone_number !!} @endforeach</h4>

                        <h4><span class="badge bg-blue">Description:</span> {!! str_limit($organization->organization_description, 200) !!}</h4>
                        </div>
                    </div>
                    @endforeach
                    <div class="pagination p-20">
                        {{ $organizations->appends(\Request::except('page'))->render() }}
                    </div>
                </div>
                
                <div class="col-md-4 p-0">
                    <div id="map" style="position: fixed !important;width: 28%;"></div>
                </div>
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
    // console.log(locations);

    var sumlat = 0.0;
    var sumlng = 0.0;
    var length = 0;
    // console.log(locations.length);
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
    // console.log(avglng);
    var mymap = new GMaps({
      el: '#map',
      lat: avglat,
      lng: avglng,
      zoom:12
    });


    $.each( locations, function(index, value ){
        // console.log(value);
        if(value.location_latitude){
            mymap.addMarker({

                lat: value.location_latitude,
                lng: value.location_longitude,
                       
                infoWindow: {
                    maxWidth: 250,
                    content: ('<a href="/organization_'+value.organization.organization_recordid+'" style="color:#428bca;font-weight:500;font-size:14px;">'+value.organization.organization_name+'</a>')
                }
            });
        }
   });
</script>
@endsection


