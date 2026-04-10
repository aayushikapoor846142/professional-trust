@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')
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
            <form id="form" method="post">
                @csrf
                <input type="hidden" name="case_id" value="{{$case_id}}" />
                @if($record->form_reply != '')
                    <div class="step-content cs-step-active" data-step="3">
                        <div class="mb-4  justify-content-center">
                            @if($record->form_json != '')
                                @php
                                    $field_reply = json_decode($record->form_reply,true);
                                    $fg_field_json = json_decode($record->form_json,true);
                                @endphp
                                <div class="cds-fs-form-assessment-report-panel-body-report-render-header">
                                            <h3 class="cds-heading">View Assesment</h3>
                                        </div>
                                <div class="cds-fs-form-assessment-report-panel-body">
                                    <div class="cds-fs-form-assessment-report-panel-body-report-render">
                                        <div class="cds-fs-form-assessment-report-panel-body-report-render-body">
                                            @php
                                                $isFg = 0;
                                                $totalFg = 0;
                                                $fgIndex = 0;
                                            @endphp
                                            @foreach($fg_field_json as $key => $fg_field)
                                                @if($isFg == 0 || $fgIndex == 0)
                                                    
                                                @endif
                                                @if($fg_field['fields'] == 'fieldGroups')
                                                    @php
                                                        $isFg = 1;
                                                        $totalFg = count($fg_field['groupFields']);
                                                    @endphp
                                                    <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-header">
                                                        <h4>{{$fg_field['settings']['label']}}</h4>
                                                    </div>
                                                @else
                                                        <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-title">
                                                            <label>
                                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-label">{{$fg_field['settings']['label']}}</div>
                                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-values">
                                                            @if(isset($field_reply[$fg_field['settings']['name']]))
                                                                @if(!is_array($field_reply[$fg_field['settings']['name']]))
                                                                    {!!
                                                                        isset($field_reply[$fg_field['settings']['name']])?$field_reply[$fg_field['settings']['name']]:''
                                                                    !!}
                                                                @else
                                                                    {!! implode(",",$field_reply[$fg_field['settings']['name']]) !!}
                                                                @endif
                                                            @endif
                                                        </div>
                                                            </label>
                                                        </div>
                                                @endif
                                                @if($isFg == 0 || $fgIndex > $totalFg)
                                                    @php
                                                        $fgIndex = 0;
                                                    @endphp
                                                   
                                                @else
                                                    @if($isFg == 1)
                                                        @php $fgIndex++; @endphp
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="cds-fs-form-assessment-report-panel-footer"></div>
                            @else
                               
                            @endif
                        </div>
                    </div>
                @else
                    <b>Not Submited</b>
                @endif

            </form>
            
        </div>
    </div>
</div>     
@endsection                                                 
