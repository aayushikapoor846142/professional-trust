@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('cases') !!}
@endsection
@section('content')
<div class="content">
    <div class="cds-fs-render-form-overview-profile">
        <div class="cds-fs-render-form-overview-profile-header">
            <div class="cds-fs-render-form-overview-profile-header-left">
            </div>
            <div class="cds-fs-render-form-overview-profile-header-right">
                <div class="cds-fs-render-form-overview-profile-header-right-date">
                </div>
            </div>
        </div>

        <div class="cds-fs-render-form-overview-profile-body">
            @if(!empty($all_filled_forms))
                @foreach($all_filled_forms as $submission)
                    <div class="mb-6">
                        <h5 class="text-lg font-semibold mb-2">View Assessment</h5>
                        <div class="overflow-hidden rounded-lg shadow bg-white">
                            <table class="w-full table-auto border-collapse">
                                <tbody>
                                
                                    <!-- <tr class="border-b bg-gray-100 text-sm">
                                        <td class="p-3 font-medium w-1/4">Name</td>
                                        <td class="p-3">{{ $submission['form_reply']['first_name'] ?? '' }} {{ $submission['form_reply']['last_name'] ?? '' }}</td>
                                    </tr> -->
                                    <tr class="border-b bg-white text-sm">
                                        <td class="p-3 font-medium">Email</td>
                                        <td class="p-3">{{ $submission['email'] ?? '' }}</td>
                                    </tr>

                                    {{-- Dynamic Fields --}}
                                    @foreach($submission['filled_fields'] as $field)
                                        @php
                                            $label = $field['settings']['label'] ?? '';
                                            $value = '';

                                            if (isset($field['settings']['value'])) {
                                                $value = $field['settings']['value'];
                                            } elseif (isset($field['settings']['options'])) {
                                                $selected = collect($field['settings']['options'])
                                                    ->filter(fn($opt) => $opt['selected'] == 1)
                                                    ->pluck('value')
                                                    ->implode(', ');
                                                $value = $selected;
                                            }
                                        @endphp
                                        <tr class="border-b text-sm {{ $loop->even ? 'bg-gray-100' : 'bg-white' }}">
                                            <td class="p-3 font-medium">{{ $label }}</td>
                                           
                                            <td class="p-3">
                                            @if($field['fields'] == "fileUpload")
                                                @foreach(explode(',',$value) as $file)
                                              <a href="{{ url('download-media-file?dir=' . assesstmentFormDir($submission['uuid']) . '&file_name=' . $file) }}"  download>
                                                {{$file}}
                                            </a>

                                                @endforeach
                                            @else
                                                {{ $value }}
                                            @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <h5>No data available</h5>
            @endif
        </div>
    </div>
</div>                                                      
@endsection


@section('javascript')

@endsection
