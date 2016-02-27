@section('table-menu.filter-breadcrumbs')
    <div class="pull-right">
        @foreach($filters AS $filter => $data)
            <label class="btn btn-sm btn-white">
                <i class="fa fa-filter"></i> {{ ucwords(preg_replace('(_|-)',  ' ', $filter)) }}
            </label>
        @endforeach
    </div>
@show