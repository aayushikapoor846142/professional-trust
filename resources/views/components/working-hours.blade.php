<form id="form" action="{{ baseUrl('working-hours/update') }}" method="post">
    @csrf
    <div class="cds-register-address-list">
        <div class="cdsTYMainsite-login-form-container-header ps-0">
            <span>Company Location</span>
        </div>

        @foreach($companyLocations->where('id',$getLocId) as $key => $record)
        <!-- Address Item 1 -->
        <div class="address-item" id="personal-address-div-{{$record->id}}">
            <div class="address-header">
                <div class="map-thumbnail">
                    <i class="fa-sharp fa-solid fa-location-dot" style="color:#000000;"></i>
                </div>
                <div class="address-details render-address">
                    <!-- <span class="badge main-house">
                                                    </span> -->
                    <div class="address-name">
                        <div class="company-name" data-value="{{$record->company->company_name ?? ''}}">
                            {{$record->company->company_name ?? ''}}</div>
                    </div>

                    <div class="address-text">
                        <div class="address-1" data-value="{{$record->address_1 ?? ''}}">
                            {{$record->address_1 ?? ''}}</div>
                        <div class="address-2" data-value="{{$record->address_2 ?? ''}}">
                            {{$record->address_2 ?? ''}}</div>
                    </div>
                    <div class="address-text">
                        <div class="state" data-value="{{$record->state ?? ''}}">
                            {{$record->state ?? ''}}
                        </div>
                        <div class="city" data-value="{{$record->city ?? ''}}">
                            {{$record->city ?? ''}}
                        </div>

                        <div class="pincode" data-value="{{$record->pincode ?? ''}}">
                            {{$record->pincode ?? ''}}</div>
                        <div class="country" data-value="{{$record->country ?? ''}}">
                            {{$record->country ?? ''}}</div>

                    </div>

                </div>
                <div class="radio">
                    <input type="radio" name="location_id" value="{{$record->id}}"
                        {{  $record->id == $getLocId ? 'checked' : '' }}
                        onclick="showWorkingHours('{{$record->unique_id}}')" />
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <span class="error-text text-danger" id="error-text-location"></span>
    <div class="js-form-message">
        <div class="cds-register-address-list">
            {!! FormHelper::formSelect([
            'name' => 'timezone',
            'id' => 'timezone',
            'label' => 'Select Timezone',
            'class' => 'select2-input ga-country',
            'options' => $timezones,
            'value_column' => 'label',
            'label_column' => 'value',
            'selected' => $getSelectedTimezone ?? null,
            'is_multiple' => false,
            'required' => true,
            ]) !!}

        </div>

    </div>
    <div class="datatable-custom cds-working-hours-table-bx table-responsive">

        <table id="tableList"
            class="card-table table table-align-middle table-bordered table-lg table-nowrap table-thead-bordered no-more-tables">
            <thead class="thead-light">
                <tr>
                    <th scope="col" class="table-column-pr-0">
                        <div class="text-md-center custom-checkbox custom-control">
                            <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
                            <label class="custom-control-label" for="datatableCheckAll"></label>
                        </div>
                    </th>
                    <th scope="col">Day</th>
                    <th scope="col" colspan="2" class="text-center">Working Hours</th>
                    <th scope="col">No Break</th>
                    <th scope="col" colspan="2" class="text-center">Non Consultation Time</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th></th>
                    <th scope="col">From-To</th>
                    
                   
                </tr>
            </thead>

            <tbody>
                @php
                $days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday",
                "sunday"];
                @endphp
                @for($d=0;$d < count($days);$d++) <tr>
                    <td class="table-column-pr-0">
                        <div class="custom-control custom-checkbox text-md-center">
                            <input type="checkbox" {{(isset($records[$days[$d]]))?'checked':''}}
                                class="custom-control-input row-checkbox" name="schedule[{{$d}}][day]"
                                value="{{ $days[$d] }}" id="row-{{$d}}">
                            <label class="custom-control-label" for="row-{{$d}}"></label>
                        </div>
                    </td>
                    <td data-title="Day">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <div>{{ ucfirst($days[$d]) }}</div>
                        </a>
                    </td>
                    <td data-title="From">
                        <input type="time" class="form-control" required
                            value="{{(isset($records[$days[$d]]))?$records[$days[$d]]['from']:''}}"
                            {{(isset($records[$days[$d]]))?'':'disabled'}} name="schedule[{{$d}}][from]">
                        <span class="error-text text-danger" id="error-from-{{$d}}"></span>
                    </td>
                    <td data-title="To">
                        <input type="time" class="form-control" required
                            value="{{(isset($records[$days[$d]]))?$records[$days[$d]]['to']:''}}"
                            {{(isset($records[$days[$d]]))?'':'disabled'}} name="schedule[{{$d}}][to]">
                        <span class="error-text text-danger" id="error-to-{{$d}}"></span>
                    </td>
                    <td data-title="No Break">
                        <input type="checkbox" class="no-break-checkbox" {{(!isset($records[$days[$d]]))?'disabled':''}} data-index="{{$d}}"
                            name="schedule[{{$d}}][no_break_time]"
                            {{ isset($records[$days[$d]]) && $records[$days[$d]]['no_break_time'] ? 'checked' : '' }}>
                        <label for="no-break-{{$d}}">No Break</label>
                    </td>
                    <td data-title="Break Times">
                        <div id="break-times-{{$d}}">
                            @php
                            $breaks = $records[$days[$d]]['breaks'] ?? [['start' => '', 'end' =>
                            '']];
                            @endphp

                            @foreach ($breaks as $index => $break)
                            <div class="break-row mb-1 d-block d-lg-flex align-items-center">
                                <input type="time" required name="schedule[{{$d}}][breaks][{{$index}}][start]" value="{{ $break['start'] }}" {{ $break['start'] != ''?'':'disabled' }} class="form-control break-start me-2">
                                <input type="time" required
                                name="schedule[{{$d}}][breaks][{{$index}}][end]" value="{{ $break['end'] }}" {{ $break['end'] != ''?'':'disabled' }} class="form-control break-end me-2 my-2 my-lg-0">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeBreakRow(this)">×</button>
                            </div>

                            <span class="text-danger" id="error-break-{{$d}}-{{$index}}-start"></span>
                            <span class="text-danger" id="error-break-{{$d}}-{{$index}}-end"></span>
                            @endforeach

                        </div>
                        <button type="button" {{ !empty($breaks) ?'':'disabled' }}
                            class="btn btn-sm btn-secondary mt-1 add-break" onclick="addBreakRow({{$d}})">+ Add
                            More</button>
                    </td>

                    <!-- <td data-title="From">
                                    <input type="time" class="form-control break-start-{{$d}}"  value="{{(isset($records[$days[$d]]) && isset($records[$days[$d]]['break_starttime']))?$records[$days[$d]]['break_starttime']:''}}" name="schedule[{{$d}}][breakstart]">
                                    <span class="error-text text-danger" id="error-breakstart-{{$d}}"></span>
                                </td>
                                <td data-title="To">
                                    <input type="time" class="form-control break-end-{{$d}}"  value="{{(isset($records[$days[$d]]) && isset($records[$days[$d]]['break_endtime']))?$records[$days[$d]]['break_endtime']:''}}" name="schedule[{{$d}}][breakend]">
                                    <span class="error-text text-danger" id="error-breakend-{{$d}}"></span>
                                </td> -->
                    </tr>
                    @endfor
            </tbody>

        </table>
    </div>
    <button type="submit" class="btn add-CdsTYButton-btn-primary m-2">Submit</button>
</form>
@if($load_type == 'page')
@push("scripts")
@endif
<script type="text/javascript">
    function addBreakRow(dayIndex) {
        const container = document.getElementById(`break-times-${dayIndex}`);
        const breakCount = container.querySelectorAll('.break-row').length;

        const row = document.createElement('div');
        row.className = 'break-row mb-1 d-flex align-items-center';

        const startInput = document.createElement('input');
        startInput.type = 'time';
        startInput.name = `schedule[${dayIndex}][breaks][${breakCount}][start]`;
        startInput.className = 'form-control me-2 break-start';
        startInput.attribute = 'required';

        const endInput = document.createElement('input');
        endInput.type = 'time';
        endInput.name = `schedule[${dayIndex}][breaks][${breakCount}][end]`;
        endInput.className = 'form-control me-2 break-end';
        endInput.attribute = 'required';
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-sm btn-danger';
        removeButton.textContent = '×';
        removeButton.onclick = function () {
            container.removeChild(row);
        };

        row.appendChild(startInput);
        row.appendChild(endInput);
        row.appendChild(removeButton);

        container.appendChild(row);
    }

    function removeBreakRow(button) {
        const row = button.closest('.break-row');
        row.parentNode.removeChild(row);
    }

    function showWorkingHours(id) {
        window.location.href = "{{baseUrl('working-hours?location=')}}" + id;
    }
    $(document).ready(function () {
        $("#form").submit(function (e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            if ($(".row-checkbox:checked").length == 0) {
                errorMessage("Select atleast one day for working hours");
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                    $(".error-text").text(""); // Clear previous errors

                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        window.location.reload();
                    } else {

                        validation(response.errors);
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (fieldName, errorMsg) {
                         
                            const match = fieldName.match(/^schedule\.(\d+)(?:\.breaks\.(\d+))?\.(\w+)$/);

                            if (match) {
                                const dayIndex = match[1];
                                const breakIndex = match[2]; // could be undefined
                                const fieldType = match[3];

                                let errorElementId = '';

                                if (breakIndex !== undefined) {
                                    // For nested break fields
                                    errorElementId = `#error-break-${dayIndex}-${breakIndex-1}-${fieldType}`;
                                    console.log(errorElementId);
                                } else {
                                    // For schedule-level fields
                                    errorElementId = `#error-${fieldType}-${dayIndex}`;
                                }

                                $(errorElementId).text(errorMsg[0]);
                            } else if (fieldName === "location_id") {
                                $("#error-text-location").text(errorMsg[0]);
                            } else if (fieldName === "timezone") {
                                $("#error-text-timezone").text(errorMsg[0]);
                            }

                                                    
                        });

                    } else {
                        internalError();
                    }
                }
            });
        });
        $("#datatableCheckAll").change(function () {
            const isChecked = $(this).is(":checked");
            $(".row-checkbox").prop("checked", isChecked);
            $('input[type="time"]').prop("disabled", !isChecked);
            $('.no-break-checkbox').prop("disabled",  !isChecked?true:false);

        });
        $(".row-checkbox").change(function () {
            const isChecked = $(this).is(":checked");
            $(this).parents("tr").find('input[type="time"]').prop("disabled", !isChecked);
            $(this).parents("tr").find('.add-break').prop("disabled", !isChecked);
            $(this).parents("tr").find('.no-break-checkbox').prop("disabled",  !isChecked?true:false);
        });
        $(".no-break-checkbox").change(function () {
            const isChecked = $(this).is(":checked");
            $(this).parents("tr").find('.break-start,.break-end').prop("disabled", isChecked?true:false);
            $(this).parents("tr").find('.add-break').prop("disabled",  isChecked?true:false);
            
        });
    });

</script>
@if($load_type == 'page')
@endpush
@endif