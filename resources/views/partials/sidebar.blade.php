{{-- <div class="col-lg-4">
    <div class="card">
        <div class="card-header bg-transparent">
            <span class="h6 m-0px">Categories</span>
        </div>
        <div class="list-group list-group-flush">
            @foreach(getCat() as $vcat)  
                @php
                    $cslug=str_slug($vcat->categories);
                    $rate=count(DB::select('select * from trending where cat_id=? and status=?', [$vcat->id,1]));
                @endphp 
            <a href="{{url('/')}}/cat/{{$vcat->id}}/{{$cslug}}" class="list-group-item list-group-item-action d-flex justify-content-between p15px-tb">
                <div>
                    <span>{{$vcat->categories}}</span>
                </div>
                <div>
                    <i class="ti-angle-right"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    <div class="card m-35px-t">
        <div class="card-header bg-transparent">
            <span class="h6 m-0px">Recent Posts</span>
        </div>
        <div class="list-group list-group-flush">
        @foreach(getBlog() as $vtrending)
                @php $vslug=str_slug($vtrending->title); @endphp
            <a href="{{url('/')}}/single/{{$vtrending->id}}/{{$vslug}}" class="list-group-item list-group-item-action d-flex p15px-tb">
                <div>
                    <div class="avatar-50 border-radius-5">
                        <img src="{{url('/')}}/asset/thumbnails/{{$vtrending->image}}" title="" alt="">
                    </div>
                </div>
                <div class="p-15px-l">
                    <p class="m-0px">{{$vtrending->title}}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div> --}}



<div class="col-lg-4 ps-xl-5 mt-5 mt-lg-0">
    <div class="blog-sidebar-widget ps-lg-2">

        <div class="widget-catagory mt-55">
            <h4 class="widget-title mb-20">Categories</h4>

            <ul>
                @foreach(getCat() as $vcat)  
                @php
                    $cslug=str_slug($vcat->categories);
                    $rate=count(DB::select('select * from trending where cat_id=? and status=?', [$vcat->id,1]));
                @endphp
                <li>
                    
                    <a href="{{url('/')}}/cat/{{$vcat->id}}/{{$cslug}}" class="">
                        <div>
                            <span>{{$vcat->categories}}</span>
                        </div>
                        {{-- <div>
                            <i class="ti-angle-right"></i>
                        </div> --}}
                    </a>  
                </li>
                @endforeach  
            </ul>
        </div>

        <div class="widget-news mt-50">
            <h4 class="widget-title">Reacent News</h4>

            <ul class="recent-post">
               

            @foreach(getBlog() as $vtrending)
                @php $vslug=str_slug($vtrending->title); @endphp
                <li>
                    <a href="{{url('/')}}/single/{{$vtrending->id}}/{{$vslug}}">
                        <img src="{url('/')}}/asset/thumbnails/{{$vtrending->image}}" alt="">
                        <div class="news-content">
                            <h6>{{$vtrending->title}}</h6>
                            {{-- <div class="post-date">
                                <img src="img/blog/calendar-outline.svg" alt="calender">
                                <span>March 18, 2021</span>
                            </div> --}}
                        </div>
                    </a>
                </li>
                @endforeach
               
            </ul>
        </div>

    </div>
</div>