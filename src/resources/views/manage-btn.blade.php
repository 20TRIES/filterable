@if(isset($filterable_settings['btn-type']) && $filterable_settings['btn-type'] != 'default')

    @if($filterable_settings['btn-type'] == 'icon')

        <button id="manage-filters" class="btn btn-outline btn-primary" data-toggle="modal" data-target="#add-filter-modal"><i class="fa fa-filter"></i></button>

    @endif

@else

    <button id="manage-filters" class="btn btn-outline btn-primary" data-toggle="modal" data-target="#add-filter-modal" style="width: 75%;">Manage Filters</button>

@endif

<div id="add-filter-modal" class="modal inmodal fade in" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 15px;">

    <div class="modal-dialog modal-lg">

        <div class="modal-content animated slideInDown">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Filters</h4>
            </div>

            <div class="modal-body">

                <h3>Add Filter</h3>
                <hr>
                <select id="new-filter-select" class="form-control" id="sel1">
                    <option>Please Select...</option>
                    <?php
                        $grouped_filters = $inactive_filters->groupBy(function($item)
                        {
                            return $item->getGroup();
                        }
                        , true)->except('Other')->toArray();

                        ksort($grouped_filters);
                    ?>
                    @foreach($grouped_filters AS $group_name => $group)
                        <optgroup label="{{$group_name}}">
                            @foreach($group AS $filter_name => $data)
                                <option value="{{$filter_name}}">{{ucwords(str_replace('_', ' ', $filter_name))}}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                    @if(array_key_exists('Other', $grouped_filters))
                        <optgroup label="Other">
                            @foreach($grouped_filters->get('Other') AS $filter => $data)
                                <option value="{{$filter}}">{{ucwords(str_replace('_', ' ', $filter))}}</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>

                <br>

                <div id="add-filter-form-container" class="form-group">

                </div>

                <br>

                <div id="add-filter-btn-container" style="display: none;">

                    <button id="add-filter-btn" class="btn btn-primary btn-outline pull-right">Add</button>

                </div>

                <br>

                <form method="get">

                    <div id="added-filters-section" style="display: none;">
                        <h3>Added Filters</h3>
                        <hr>

                        <div class="row">
                            <ul class="list-group col-lg-12">

                            </ul>
                        </div>
                    </div>

                    <br>
                    <br>

                    <div id="active-filters-section">
                        <h3>Active Filters</h3>
                        <hr>

                        <div class="row">
                            <ul class="list-group col-lg-12">

                            </ul>
                        </div>
                    </div>

                    <br>
                    <br>

                    <hr>

                    <div id="manage-filters-modal-footer" {!! empty($filters) ? 'style="display: none;"' : '' !!}>
                        <button type="submit" class="btn btn-primary btn-outline pull-right">Update</button>
                    </div>

                    <br>
                    <br>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="components" style="display: none;">
    <li id="filter-sm" class="form-inline list-group-item col-lg-6"  data-filter-name="" data-filter-type="" data-filter-group="" data-filter-collection="">

        <button id="rmv-filter-sm-btn" class="btn btn-xs btn-link form-group pull-right"><i class="glyphicon glyphicon-remove"></i></button>

        <h5 id="filter_name"></h5>

        <small id="filter-value-label"></small>

        <input class="filter-value-input" name="" value="" style="display: none;"/>

        <br>

        <label id="filter-collection-label" class="label label-warning pull-right">Custom</label>

        <input id="filter-collection-input" type="hidden" name="collections[]" value=""/>
    </li>

    <div id="filter-form" class="form-inline"  data-filter-name="">
        <div class="form-group">
            {{-- This is where any form input fields should be placed --}}
        </div>
        <button id="rmv-filter-form-btn" class="btn btn-primary btn-sm btn-outline pull-right"><i class="glyphicon glyphicon-remove"></i></button>
    </div>
</div>

<style>
    .datepicker-dropdown {
        z-index: 2400;
    }
</style>

@section('js')

    <script>

                <?php
                    $url_params = ['filters' => [], 'collections' => []];

                    foreach($filters AS $filter => $data)
                    {
                        if(!is_null($data->getCollection()) && !isset($url_params['filters'][$data->getCollection()]))
                        {
                            $url_params['collections'][] = $data->getCollection();
                        }

                        if($data->getGroup() === 'Dates')
                        {
                            continue;
                        }

                        $url_params['filters'][$filter] = isset($data->getValues()[0]) ? $data->getValues()[0] : null;
                    }
                ?>

                var available_filters = {!! $available_filters->toJson() !!};

        var active_filters = {!! $filters->toJson() !!};

        var url_params = {!! json_encode($url_params) !!};

        <?php $time_period = !isset($date_filter) || is_null($date_filter->getValues()) ? null : head($date_filter->getValues())->getTimeperiod(); ?>

        @if( ! is_null($time_period))
            @if($time_period == 'Day')
                var next_date_range = "{{ head($date_filter->getValues())->offset(1, \_20TRIES\DateRange::DAY, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
                var prev_date_range = "{{ head($date_filter->getValues())->offset(-1, \_20TRIES\DateRange::DAY, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
            @elseif($time_period == 'Week')
                var next_date_range = "{{ head($date_filter->getValues())->offset(1, \_20TRIES\DateRange::WEEK, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
                var prev_date_range = "{{ head($date_filter->getValues())->offset(-1, \_20TRIES\DateRange::WEEK, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
            @elseif($time_period == 'Month')
                var next_date_range = "{{ head($date_filter->getValues())->offset(1, \_20TRIES\DateRange::MONTH, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
                var prev_date_range = "{{ head($date_filter->getValues())->offset(-1, \_20TRIES\DateRange::MONTH, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
            @elseif($time_period == 'Year')
                var next_date_range = "{{ head($date_filter->getValues())->offset(1, \_20TRIES\DateRange::YEAR, false, true)->toInclusiveString('d/m/Y', ' - ') }}";
                var prev_date_range = "{{ head($date_filter->getValues())->offset(-1, \_20TRIES\DateRange::YEAR, false, true)->toInclusiveString('d/m/Y', ' - ') }}"
            @endif
        @endif

        /**
         * Generates a form for a filter, that can be used to capture the data that is required to apply
         * it to a query.
         *
         * @param string name The name of the filter
         * @return [not sure what the type is] Form content for the filter.
         */
        function generateFilterForm(name)
        {
            if(typeof $(available_filters).prop(name) == 'undefined')
            {
                console.log('Unable to make "' + name + '" filter available!');

                return;
            }

            var filter =  $(available_filters).prop(name);

            if(filter.type == 'select')
            {
                return generateSelectFilter(name, filter);
            }
            else if(filter.type == 'text')
            {
                return generateTextFilter(name, filter);
            }
            else if(filter.type == 'flag')
            {
                return generateFlagFilter(name, filter);
            }
            else if(filter.type == 'date_range_picker')
            {
                return generateDateRangePickerFilter(name, filter);
            }
            else if(filter.type == 'month_picker')
            {
                return generateMonthPickerFilter(name, filter);
            }
        }

        /**
         * Generates a form for a filter with the type of "text".
         *
         * @param string name The name of the filter
         * @param object date The filter object.
         * @return [not sure what the type is Form content for the filter.
         */
        function generateTextFilter(name, data)
        {
            var form = generateFilter(name, data);

            var filter_value = (typeof data.values[0] == 'undefined' ? '' : data.values[0]);

            var form_group = $(form).find('.form-group');

            var input_field = '<input'
                    + ' type="text"'
                    + ' name="' + name + '"'
                    + (data.readonly == true ? ' readonly' : '')
                    + ' class="form-control" name="' + name + '"'
                    + ' style="width: 470px;"'
                    + ' value="' + filter_value + '"'
                    + '>';

            $(form_group).html(input_field);

            return form;
        }

        /**
         * Generates a form for a filter with the type of "flag".
         *
         * @param string name The name of the filter
         * @param object date The filter object.
         * @return [not sure what the type is Form content for the filter.
         */
        function generateFlagFilter(name, data)
        {
            var form = generateFilter(name, data);

            var form_group = $(form).find('.form-group');

            var input_field = '<input'
                    + ' type="hidden"'
                    + ' name="' + name + '"'
                    + (data.readonly == true ? ' readonly' : '')
                    + ' class="form-control" name="' + name + '"'
                    + ' style="width: 470px;"'
                    + ' value=""'
                    + '>';

            $(form_group).html(input_field);

            return form;
        }

        /**
         * Generates a form for a filter with the type of "select".
         *
         * @param string name The name of the filter
         * @param object date The filter object.
         * @return [not sure what the type is Form content for the filter.
         */
        function generateSelectFilter(name, data)
        {
            var form = generateFilter(name, data);

            var form_group = $(form).find('.form-group');

            var select_boxes = [];

            for(var i = 0; i < data.options.length; ++i)
            {
                var selects = data.options[i];

                for (var select_name in selects)
                {
                    if (selects.hasOwnProperty(select_name))
                    {
                        var options = $(selects).prop(select_name);

                        var select_box = '<select' + (data.readonly == true ? ' readonly' : '') + ' class="form-control" name="' + name + '">';

                        select_box += '<option disabled selected>' + ucwords(select_name.replace(/_/g, ' ')) + '</option>';

                        for (var option_name in options)
                        {
                            if (options.hasOwnProperty(option_name))
                            {
                                var option_value = $(options).prop(option_name);

                                select_box += '<option name="' + option_name + '" value="' + option_value + '">' + option_name + '</option>'
                            }
                        }

                        select_box += '</select>';

                        select_boxes[select_boxes.length] = select_box;
                    }
                }
            }

            $(form_group).html(select_boxes.join(' '));

            return form;
        }

        /**
         * Generates a form for a filter with the type of "date_range_picker".
         *
         * @param string name The name of the filter
         * @param object date The filter object.
         * @return [not sure what the type is Form content for the filter.
         */
        function generateDateRangePickerFilter(name, data)
        {
            var form = generateFilter(name, data);

            var form_group = $(form).find('.form-group');

            var input_field = '<input'
                    + ' type="text"'
                    + ' name="' + name + '"'
                    + (data.readonly == true ? ' readonly' : '')
                    + ' class="form-control date_range_picker" name="' + name + '"'
                    + ' style="width: 470px;"'
                    + ' value=""'
                    + '>';

            $(form_group).html(input_field);

            return form;
        }

        /**
         * Generates a form for a filter with the type of "date_range_picker".
         *
         * @param string name The name of the filter
         * @param object date The filter object.
         * @return [not sure what the type is] Form content for the filter.
         */
        function generateMonthPickerFilter(name, data)
        {
            var form = generateFilter(name, data);

            var form_group = $(form).find('.form-group');

            var input_field = ''
                + '<input'
                + ' type="text"'
                + ' name="' + name + '"'
                +  (data.readonly == true ? ' readonly' : '')
                + ' class="form-control month_picker" name="' + name + '"'
                + ' style="width: 470px;"'
                + ' value=""'
                + '>';

            $(form_group).html(input_field);

            return form;
        }

        /**
         * Generates a "bare-bones" form for a filter which can then be customised according to the
         * type of filter being targetted.
         *
         * @param string name The name of the filter
         * @param object date The filter object.
         * @return [not sure what the type is] Form content for the filter.
         */
        function generateFilter(name, data)
        {
            var form = $('#filter-form').clone();

            $(form).attr('id', name + "_filter");

            $(form).attr('data-filter-name', name);

            return form;
        }

        /**
         * Generates an element that can be used to display a filter; the element does not support
         * editing of the filter value, however does contain the necessary elements to enable the
         * element to be submitted as part of a form.
         *
         * Filter Value: The filter filter value; if different from the value contained within
         * the "available_filters" collection. This is useful if the value has been input by a user.
         *
         * Filter Value Alias: An alias that should be used in place of the filter value when
         * displaying the value to a user; a good example of this is a user id as the filter value but their
         * name as the alias.
         *
         * @param {string} name The name of the filter
         * @param {Array} filter_values [Filter Value, Filter Value Alias, ...]
         * @return [not sure what the type is] The element described above.
         */
        function generateSmallFilter(name, filter_values)
        {
            if(typeof $(available_filters).prop(name) == 'undefined')
            {
                console.log('Unable to make small filter:"' + name + '"!');

                return;
            }

            var filter = $(available_filters).prop(name);

            // Get filter
            var sm_filter = $("#filter-sm").clone();

            // Set attributes
            $(sm_filter).attr('id', name + "_filter");

            $(sm_filter).attr('data-filter-name', name);

            $(sm_filter).attr('data-filter-type', filter.type);

            $(sm_filter).attr('data-filter-group', filter.group);

            $(sm_filter).attr('data-filter-collection', filter.collection);

            // Set contents
            $(sm_filter).find('#filter_name').html(ucwords(name.replace(/_/g, ' ')));

            /*
             * Now we will need to find the value(s) of the filter and set these in the small filter.
             */
            var filter_value_alias = "";

            var default_input = $(sm_filter).find('.filter-value-input');

            var last_input = null;

            for(var i = 0; i < filter_values.length; ++i)
            {
                var filter_value = (typeof filter_values[i][0] == 'undefined' ? '' : filter_values[i][0]);

                filter_value_alias += (((typeof filter_values[i][1] == 'undefined' ? filter_value : filter_values[i][1])) + ' ');

                var input = $(default_input).clone();

                $(input).attr('name', 'filters[' + name + '][]');

                $(input).attr('value', filter_value);

                if(last_input != null)
                {
                    $(input).insertAfter(last_input);
                }
                else
                {
                    last_input = input;

                    $(input).insertAfter(default_input);
                }
            }

            $(default_input).remove();

            $(sm_filter).find('#filter-value-label').html(filter_value_alias);

            /*
             * Now we will need to find the collection for the filter and set these in the small filter.
             */
            if(filter.collection !== null)
            {
                var label = $(sm_filter).find('#filter-collection-label');

                label.removeClass('label-warning');

                label.addClass('label-info');

                $(label).html(ucwords(filter.collection.replace(/_/g,' ')));

                $(sm_filter).find('#filter-collection-input').attr('value', filter.collection);
            }

            // Return filter html
            return $(sm_filter).prop('outerHTML');
        }

        /**
         * Disconnects a filter collection from all active and added filters.
         *
         * @param string name The name of the filter
         */
        function disconnectCollection(name)
        {
            var collections = $('#filter-collection-input[value= ' + name + ']');

            for(var i = 0; i < collections.length; ++i)
            {
                $(collections[i]).attr('value', '');
                $(collections[i]).prev('#filter-collection-label').html('Other');
                $(collections[i]).prev('#filter-collection-label').removeClass('label-info');
                $(collections[i]).prev('#filter-collection-label').addClass('label-warning');
            }
        }

        /**
         * Makes a filter available to add by inserting the filter as an option in the select box in the
         * "Add Filter" section.
         */
        function makeFilterAvailable(name)
        {
            if(typeof $(available_filters).prop(name) == 'undefined')
            {
                console.log('Unable to make "' + name + '" filter available!');

                return;
            }

            var filter = $(available_filters).prop(name);

            var optgroup = $('#new-filter-select optgroup[label="' + filter.group + '"]');

            if(optgroup.length === 0)
            {
                // If an option group does not exist, we will create one and set the optgroup
                // variable to that.
                $('#new-filter-select').append('<optgroup label="' + filter.group + '"></optgroup>');

                optgroup = $('#new-filter-select optgroup[label="' + filter.group + '"]');
            }

            $(optgroup).append('<option value="' +  name + '">' + ucwords(name.replace(/_/g, ' ')) + '</option>');
        }

        /**
         * Determines the value of a filter.
         *
         * This method will check for any filter forms that are active to get the most up to date value;
         * if no forms are currently active for the filter, then the value in the "available_filters"
         * attribute will be returned.
         *
         * @param string name The name of the filter
         * @return mixed The filter value
         */
        function getFilterValues(name)
        {
            var filter = $(available_filters).prop(name);

            var form = $('#' + name + '_filter');

            /*
             * First we will look for an active filter form from which we can get a value.
             */
            if(typeof form != 'undefined')
            {
                if(filter.type == 'text')
                {
                    var input_fields = $(form).find('input');

                    if(input_fields.length != 0)
                    {
                        return [[$(input_fields[0]).val()]];
                    }
                }
                else if(filter.type == 'select')
                {
                    var select_boxes = $(form).find('select');

                    if(select_boxes.length != 0)
                    {
                        var values = [];

                        for(var i = 0; i < select_boxes.length; ++i)
                        {
                            var selected = $(select_boxes[i]).find(':selected');

                            values[i] = [$(selected[0]).attr('value'), $(selected[0]).attr('name')];
                        }
                        return values;
                    }
                }
                else if(filter.type == 'date_range_picker')
                {
                    var input_fields = $(form).find('input');

                    if(input_fields.length != 0)
                    {
                        var start_date = $(input_fields[0]).data('daterangepicker').startDate.format("DD/MM/YYYY");
                        var end_date = $(input_fields[0]).data('daterangepicker').endDate.format("DD/MM/YYYY");

                        return [[start_date + ' - ' + end_date]];
                    }
                }
                else if(filter.type == 'month_picker')
                {
                    var picker = $(form).find('.month_picker')[0];

                    var value = $(picker).datepicker('getUTCDate');

                    var start_date = moment(value).startOf('month').format('DD/MM/YYYY');

                    var end_date = moment(value).endOf('month').format('DD/MM/YYYY');

                    return [[start_date + ' - ' + end_date]];
                }
                else
                {
                    return [['','']];
                }
            }

            /*
             * If no active filter form was found we will instead return the default value.
             */
            return [filter.values[0], ""];
        }

        /**
         * Makes a filter unavailable to users when adding a new filter.
         *
         * @param string name The name of the filter
         */
        function makeFilterUnavailable(name)
        {
            $('#new-filter-select option[value="' + name + '"]').remove();
        }

        /**
         * Removes a small filter element.
         *
         * @param string name The small filter element
         */
        function removeSmFilter(sm_filter)
        {
            var collections = $(sm_filter).find('#filter-collection-input[value!=""]');

            for (var i = 0; i < collections.length; ++i)
            {
                disconnectCollection($(collections[i]).attr('value'));
            }

            var filter_name = $(sm_filter).attr('data-filter-name');

            makeFilterAvailable(filter_name);

            $(sm_filter).remove();

            /*
             * Now, if the "Added Filters" or "Active Filters" sections are empty, we will hide them.
             */
            var added_filters_section = $('#added-filters-section');

            if($(added_filters_section).find('li').length == 0)
            {
                $(added_filters_section).hide();
            }

            var active_filters_section = $('#active-filters-section');

            if($(active_filters_section).find('li').length == 0)
            {
                $(active_filters_section).hide();
            }
        }

        /**
         * Capitalizes the first letter of every word within a string.
         *
         * @param string str The string to format.
         * @return string The formatted string.
         */
        function ucwords (str)
        {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1)
            {
                return $1.toUpperCase();
            });
        }

        /**
         * Gets the first property from an object (order of object properties is not guaranteed).
         *
         * @param object
         * @returns {*}
         */
        function getFirstObjectProp(object)
        {
            for (var item in object)
            {
                if (object.hasOwnProperty(item))
                {
                    return $(object).prop(item);
                }
            }

            return null;
        }

        /**
         * Gets the current date filter or null if a date filter is not set.
         *
         * @returns {array|null}
         */
        function getCurrentDateFilter()
        {
            for(var filter_name in active_filters)
            {
                if (active_filters.hasOwnProperty(filter_name))
                {
                    var filter = $(active_filters).prop(filter_name);

                    if(filter.group == 'Dates')
                    {
                        return [filter_name, filter];
                    }
                }
            }

            return null;
        }

        function redirect(url)
        {
            $(location).attr('href', url);
        }

        jQuery(document).ready(function($) {

            var currently_adding_filter = 'undefined';

            $(document).on('change', '#new-filter-select', function()
            {
                if($(this).val() != 'undefined' && $(this).val() != '')
                {
                    /*
                     * When a filter is selected in the "Add Filter" section we will show the associated form
                     * for adding that filter.
                     */
                    var form = generateFilterForm($(this).val());

                    currently_adding_filter = $(this).val();

                    $(form).appendTo('#add-filter-form-container');

                    $(this).prop('disabled', true);

                    $('#add-filter-btn-container').show();

                    /*
                     * Now we will initialise any elements with the date_range_picker class that do not have
                     * the attribute "readonly" set.
                     */
                    var date_range_pickers = $('.date_range_picker');

                    for(var i = 0; i < date_range_pickers.length; ++i)
                    {
                        if(typeof $(date_range_pickers[i]).attr('readonly') === 'undefined')
                        {
                            $(date_range_pickers[i]).daterangepicker({
                                format: "DD/MM/YYYY"
                            });
                        }
                    }

                    /*
                     * Now we will initialise any elements with the month_picker class that do not have
                     * the attribute "readonly" set.
                     */
                    var month_pickers = $('.month_picker');

                    for(var i = 0; i < month_pickers.length; ++i)
                    {
                        if(typeof $(month_pickers[i]).attr('readonly') === 'undefined')
                        {
                            $(month_pickers[i]).datepicker({
                                format: "yyyy-mm-dd",
                                startView: "year",
                                autoclose: true,
                            });
                        }
                    }
                }
            });

            /*
             * Event listener that alters the behavior of the datepicker plugin to allow the
             * selection of a month without having to specify the day; instead the day defaults
             * to the first day of any given month.
             */
            $(document).on('click', '.month', function(e, value)
            {
                var days_container = $('.datepicker-days');

                var days = $(days_container).find('.new.day');

                var picker = $('.month_picker');

                /*
                 * When a month is clicked, we will automatically trigger a click on the first day
                 * of that month.
                 */
                $(days[0]).trigger('click');

                /*
                 * Now we will de-select the input field.
                 */
                $(picker).trigger('blur');

                /*
                 * Now we will correct the month value which would previously have been incremented
                 * to the month after the one selected (datepicker bug).
                 */
                var date_string = $(picker).val();

                var date = moment(date_string);

                date.month(date.month() - 1);

                $(picker).datepicker('update', date.format("YYYY-MM-DD"));
            });


            $(document).on('click', '#add-filter-btn', function()
            {
                /*
                 * First we will make sure that a filter has been selected; if it hasn't then we will
                 * take no further action.
                 */
                if(currently_adding_filter == 'undefined')
                {
                    return;
                }

                var filter = $(available_filters).prop(currently_adding_filter);

                /*
                 * If the filter to be added is a date filter, then we will first check to make sure that
                 * no other date filters have been added; if they have, we will show an error message and
                 * no proceed any further.
                 *
                 * Support for multiple dates could be added in the future; just is not currently
                 * implemented.
                 */
                if(filter.group == 'Dates')
                {
                    date_filters = $('li[data-filter-group="Dates"]');

                    if(date_filters.length != 0)
                    {
                        flashError('Adding multiple date filters is not currently supported.')
                        return;
                    }
                }

                /*
                 * Now we will check that, if the filter type is date and there is already a date filter
                 * provided, then we will show an error and stop multiple date filters being added; this
                 * is to simplify the underlying code but support could be added for multiple date filters
                 * at a later date.
                 */
                var added_filters_section  = $('#added-filters-section');

                var active_filters_section = $('#active-filters-section');

                if($(filter).group == 'Dates' && ($(added_filters_section).find("[data-filter-group='Dates']").length > 0 || $(active_filters_section).find("[data-filter-group='Dates']").length > 0))
                {
                    flashError('Adding multiple date filters is currently not supported!');

                    return;
                }

                /*
                 * Now we will make sure that the "Added Filters" section and update button are
                 * currently visible.
                 */
                var footer = $('#manage-filters-modal-footer');

                added_filters_section.show();

                footer.show();

                /*
                 * Now we will hide the "Add" filter button container.
                 */
                $(this).parent().hide();

                /*
                 * Now we will need to move the newly added filter to the "Added Filters" section and
                 * remove the filter from the list of filters in the "Add Filter" section. Once this is
                 * done we can enable the "Add Filter" select box.
                 */
                makeFilterUnavailable(currently_adding_filter);

                var filter_values = getFilterValues(currently_adding_filter);

                $('#' + currently_adding_filter + '_filter').remove();

                $(added_filters_section).find('.row ul').append(generateSmallFilter(currently_adding_filter, filter_values));

                $('#new-filter-select').prop('disabled', false);

                /*
                 * Now we will set the currently_adding_filter so that any future code will know that we
                 * are no longer adding a new filter.
                 */
                currently_adding_filter = 'undefined';
            });

            $(document).on('click', '#rmv-filter-form-btn', function()
            {
                var this_container = $(this).parent();

                $(this_container).remove();

                var add_filter_select = $('#new-filter-select');

                $(add_filter_select).find('option:selected').prop('selected', false);

                $(add_filter_select).prop('disabled', false);

                $('#add-filter-btn-container').hide();

                /*
                 * Now, if the "Added Filters" or "Active Filters" sections are empty, we will hide them.
                 */
                var added_filters_section = $('#added-filters-section');

                if($(added_filters_section).find('li').length == 0)
                {
                    $(added_filters_section).hide();
                }

                var active_filters_section = $('#active-filters-section');

                if($(active_filters_section).find('li').length == 0)
                {
                    $(active_filters_section).hide();
                }
            });

            $(document).on('click', '#rmv-filter-sm-btn', function(e)
            {
                e.preventDefault();

                var this_container = $(this).parent();

                var collections = $(this_container).find('#filter-collection-input[value!=""]');

                if(collections.length != 0)
                {
                    swal({
                                title: "Are you sure?",
                                text: "Removing a filter that is part of a collection will also remove the collection. This may effect the columns that are shown.",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes, delete it.",
                                confirmButtonColor: "#DD6B55",
                                cancelButtonText: "No, cancel.",
                                closeOnConfirm: false
                            },
                            function()
                            {
                                removeSmFilter(this_container);

                                swal({
                                    title: "Deleted!",
                                    text: "The filter was successfully removed.",
                                    type: "success",
                                    confirmButtonText: "Ok",
                                    confirmButtonColor: "#1a7bb9",
                                    closeOnConfirm: false
                                });
                            });
                }
                else
                {
                    removeSmFilter(this_container);
                }

            });

            $(document).on('click', '#next_date_filter', function(e)
            {
                if(typeof $(this).prop('disabled') != 'undefined')
                {
                    var current_page = window.location.href.split('?')[0];

                    var filter = getCurrentDateFilter();

                    redirect(current_page + '?' + $.param(url_params) + "&filters[" + filter[0] + "][]=in&filters[" + filter[0] + "][]=" + encodeURIComponent(next_date_range));
                }
            });

            $(document).on('click', '#prev_date_filter', function(e)
            {
                if(typeof $(this).prop('disabled') != 'undefined')
                {
                    var current_page = window.location.href.split('?')[0];

                    var filter = getCurrentDateFilter();

                    redirect(current_page + '?' + $.param(url_params) + "&filters[" + filter[0] + "][]=in&filters[" + filter[0] + "][]=" + encodeURIComponent(prev_date_range));
                }
            });

            $(document).on('click', '#add_day_filter', function(e)
            {
                var current_page = window.location.href.split('?')[0];

                var start = "{{\Carbon\Carbon::now('GB')->startOfDay()->format('d/m/Y')}}";
                var end   = "{{\Carbon\Carbon::now('GB')->endOfDay()->format('d/m/Y')}}";

                var filter = getCurrentDateFilter();

                redirect(current_page + '?' + $.param(url_params) + "&filters[" + filter[0] + "][]=in&filters[" + filter[0] + "][]=" + start + ' - ' + end);
            });

            $(document).on('click', '#add_week_filter', function(e)
            {
                var current_page = window.location.href.split('?')[0];

                var start = "{{\Carbon\Carbon::now('GB')->startOfWeek()->format('d/m/Y')}}";
                var end   = "{{\Carbon\Carbon::now('GB')->endOfWeek()->format('d/m/Y')}}";

                var filter = getCurrentDateFilter();

                redirect(current_page + '?' + $.param(url_params) + "&filters[" + filter[0] + "][]=in&filters[" + filter[0] + "][]=" + start + ' - ' + end);
            });

            $(document).on('click', '#add_month_filter', function(e)
            {
                var current_page = window.location.href.split('?')[0];

                var start = "{{\Carbon\Carbon::now('GB')->startOfMonth()->format('d/m/Y')}}";
                var end   = "{{\Carbon\Carbon::now('GB')->endOfMonth()->format('d/m/Y')}}";

                var filter = getCurrentDateFilter();

                redirect(current_page + '?' + $.param(url_params) + "&filters[" + filter[0] + "][]=in&filters[" + filter[0] + "][]=" + start + ' - ' + end);
            });

            $(document).on('click', '#add_year_filter', function(e)
            {
                var current_page = window.location.href.split('?')[0];

                var start = "{{\Carbon\Carbon::now('GB')->startOfYear()->format('d/m/Y')}}";
                var end   = "{{\Carbon\Carbon::now('GB')->endOfYear()->format('d/m/Y')}}";

                var filter = getCurrentDateFilter();

                redirect(current_page + '?' + $.param(url_params) + "&filters[" + filter[0] + "][]=in&filters[" + filter[0] + "][]=" + start + ' - ' + end);
            });

            var active_filters_section = $('#active-filters-section div ul');

            $(active_filters_section).html('');

            for (var name in active_filters) {
                if (active_filters.hasOwnProperty(name)) {

                    var filter = $(active_filters).prop(name);

                    var values = [];

                    for(var i = 0; i < filter.values.length; ++i)
                    {
                        var value = filter.values[i];

                        var value_alias = filter.values[i];

                        if(typeof filter.options != 'undefined')
                        {
                            var options = getFirstObjectProp(filter.options[i]);

                            for (var option in options)
                            {
                                if (options.hasOwnProperty(option) && $(options).prop(option) == value)
                                {
                                    value_alias = option;
                                }
                            }
                        }

                        values[values.length] = [value, value_alias];
                    }

                    $(active_filters_section).html($(active_filters_section).html() + generateSmallFilter(name, values));
                }

                /*
                 * Now, if the "Active Filters" section is empty, we will hide them.
                 */
                if($(active_filters_section).find('li').length == 0)
                {
                    $(active_filters_section).hide();
                }

            }
        });

    </script>

    @parent

@stop