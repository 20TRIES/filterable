@if(isset($date_filter))
    @if(!isset($no_tags))<h5>@endif
        {!! $date_filter->getValues()[0]->forHumans(' <i class="fa fa-long-arrow-right"></i> ') !!}
    @if(!isset($noTags))</h5>@endif
@else
    <h5>All</h5>
@endif