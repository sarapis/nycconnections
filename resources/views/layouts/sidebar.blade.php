<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<nav id="sidebar">
    <ul class="list-unstyled components pt-0 mb-0 sidebar-menu"> 
        <li class="option-side">
            <button class="btn btn-block waves-effect waves-classic" style="padding: 0;background: #A2E9FF;"><a href="/organizations" style="display: block;padding-left: 10px;">Organizations</a></button>
        </li>
        <li class="option-side">
            <button class="btn btn-block waves-effect waves-classic" style="padding: 0;background: #A2E9FF;"><a href="/about" style="display: block;padding-left: 10px;">About</a></button>
        </li>
    </ul>
    <div class="sidebar-header p-10">
        <div class="form-group" style="margin: 0;">
        <!--begin::Form-->
            <div class="mb-5">

                <form action="/find" method="POST" class="input-search">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <i class="input-search-icon md-search" aria-hidden="true"></i>
                    <input type="text" class="form-control search-form" name="find" placeholder="Search for Organizations" id="search_address">
                    
                </form>
                
            </div>
        </div>
    </div>

       <ul class="list-unstyled components pt-0"> 

            <li class="option-side">
                <!--begin::Form-->
                <form method="post" action="/search_address" class="mb-5" id="search_location">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="input-search">
                        <i class="input-search-icon md-search" aria-hidden="true"></i>
                        <input id="location" type="text" class="form-control search-form" name="search_address" placeholder="Search Address">
                    </div>
                </form>
            </li>

            <li class="option-side">
                <button class="btn btn-block waves-effect waves-classic" style="padding: 0;background: #A2E9FF;"><a href="/organizations_near_me" style="display: block;padding-left: 10px;">Near Me</a></button>
            </li> 
                    
            <li class="option-side">
                <a href="#projectcategory" class="text-side" data-toggle="collapse" aria-expanded="false">Category</a>
                <ul class="collapse list-unstyled option-ul" id="projectcategory">
                    @foreach($taxonomies as $taxonomy)
                        
                        <li class="option-li"><a href="category_{{$taxonomy->taxonomy_recordid}}" class="option-record">{{$taxonomy->taxonomy_name}}</a></li>
                       
                    @endforeach
                </ul>
            </li>
        </ul>

</nav>
@if(isset($location) == TRUE)
    <input type="hidden" name="location" id="location" value="{{$location}}">
@else
    <input type="hidden" name="location" id="location" value="">
@endif

<script>

$(function () {
    var getData = function (request, response) {
        $.getJSON(
            "https://geosearch.planninglabs.nyc/v1/autocomplete?text=" + request.term,
            function (data) {
                response(data.features);
                
                var label = new Object();
                for(i = 0; i < data.features.length; i++)
                    label[i] = data.features[i].properties.label;
                response(label);
            });
    };
 
    var selectItem = function (event, ui) {
        $("#location").val(ui.item.value);
        return false;
    }
 
    $("#location").autocomplete({
        source: getData,
        select: selectItem,
        minLength: 2,
        change: function() {
            console.log(selectItem);

        }
    });

    $('.ui-menu').click(function(){
        $('#search_location').submit();
    });

  
});
</script>
