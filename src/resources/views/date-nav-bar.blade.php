@section('table-menu.btn-filters')

    @if( ! is_null($date_filter))

        <?php $time_period  = !isset($date_filter) || is_null($date_filter->getValues()) ? null :
                head
        ($date_filter->getValues())->getTimeperiod(); ?>

        <div class="pull-right">
            <div class="btn-group">
                <button id="prev_date_filter" type="button" class="btn btn-{{ $size or 'xs' }} btn-white" {{is_null($time_period) ? 'disabled' : ''}}><i class="fa fa-chevron-left"></i></button>
                @if(in_array(\_20TRIES\DateRange::DAY, $time_periods))
                    <button id="add_day_filter" class="btn btn-{{ $size or 'xs' }} btn-white {{ $time_period === 'Day' ? 'active' : ''}}">Day</button>
                @endif
                @if(in_array(\_20TRIES\DateRange::WEEK, $time_periods))
                    <button id="add_week_filter" class="btn btn-{{ $size or 'xs' }} btn-white {{ $time_period === 'Week' ? 'active' : ''}}">Week</button>
                @endif
                @if(in_array(\_20TRIES\DateRange::MONTH, $time_periods))
                    <button id="add_month_filter" class="btn btn-{{ $size or 'xs' }} btn-white {{ $time_period === 'Month' ? 'active' : ''}}">Month</button>
                @endif
                @if(in_array(\_20TRIES\DateRange::YEAR, $time_periods))
                    <button id="add_year_filter" class="btn btn-{{ $size or 'xs' }} btn-white {{ $time_period === 'Year' ? 'active' : ''}}">Year</button>
                @endif
                <button id="next_date_filter" type="button" class="btn btn-{{ $size or 'xs' }} btn-white" {{is_null($time_period) ? 'disabled' : ''}}><i class="fa fa-chevron-right"></i> </button>
            </div>
        </div>

    @endif

@show