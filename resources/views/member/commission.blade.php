<div class="tabbable">
    <ul class="nav nav-tabs nav-tabs-bottom nav-justified no-margin">
        @foreach ($commission as $key => $value)
            <li class="{{($key == 'mobile') ? 'active' : ''}}"><a href="#{{$key}}" data-toggle="tab" class="legitRipple" aria-expanded="true">{{ucfirst($key)}}</a></li>
        @endforeach
    </ul>

    <div class="tab-content">
        @if(isset($mydata['schememanager']) && $mydata['schememanager']->value == "admin")
            @foreach ($commission as $key => $value)
                <div class="tab-pane {{($key == 'mobile') ? 'active' : ''}}" id="{{$key}}">
                    <table class="table table-bordered" cellspacing="0" style="width:100%">
                        <thead>
                                <th>Provider</th>
                                <th>Type</th>
                                @if(Myhelper::hasRole(['admin']))
                                <th>Api User</th>
                                @endif
                                @if(Myhelper::hasRole(['admin','whitelable']))
                                <th>Whitelable</th>
                                @endif
                                @if(Myhelper::hasRole('admin','md'))
                                <th>Md</th>
                                @endif
                                @if(Myhelper::hasRole('admin','distributor'))
                                <th>Distributor</th>
                                @endif
                                @if(Myhelper::hasRole('admin','retailer'))
                                <th>Retailer</th>
                                @endif
                        </thead>

                        <tbody>
                            @foreach ($value as $comm)
                                <tr>
                                    <td>{{ucfirst($comm->provider->name)}}</td>
                                    <td>{{ucfirst($comm->type)}}</td>
                                    @if(Myhelper::hasRole(['admin']))
                                    <th>{{ucfirst($comm->apiuser)}}</th>
                                    @endif
                                    @if(Myhelper::hasRole('admin','whitelable'))
                                    <td>{{ucfirst($comm->whitelable)}}</td>
                                    @endif
                                    @if(Myhelper::hasRole('admin','md'))
                                    <td>{{ucfirst($comm->md)}}</td>
                                    @endif
                                    @if(Myhelper::hasRole('admin','distributor'))
                                    <td>{{ucfirst($comm->distributor)}}</td>
                                    @endif
                                    @if(Myhelper::hasRole('admin','retailer'))
                                    <td>{{ucfirst($comm->retailer)}}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            @foreach ($commission as $key => $value)
                <div class="tab-pane {{($key == 'mobile') ? 'active' : ''}}" id="{{$key}}">
                    <table class="table table-bordered" cellspacing="0" style="width:100%">
                        <thead>
                                <th>Provider</th>
                                <th>Type</th>
                                <th>Value</th>
                        </thead>

                        <tbody>
                            @foreach ($value as $comm)
                                <tr>
                                    <td>{{ucfirst($comm->provider->name)}}</td>
                                    <td>{{ucfirst($comm->type)}}</td>
                                    <td>{{$comm->value}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>
</div>