<div class="cds-ty-uap-all-grid-view-home-card-list">
    @foreach($records as $value)
    <a href="{{ url('unauthorised-practitioners/'.$value['unique_id'].'/'.str_slug($value['name'])) }}"
        class="cds-ty-uap-all-grid-view-home-card">

        <div class="cds-ty-uap-all-grid-view-home-card-segment">
            <div class="cds-ty-uap-all-grid-view-home-card-segment-header">

            </div>
            <div class="cds-ty-uap-all-grid-view-home-card-segment-body">
                <div class="cds-ty-uap-all-grid-view-home-card-segment-body-image">
                    <img src="{{ $value['thumb_image_url'] }}" alt="image">
                </div>
                <div class="cds-ty-uap-all-grid-view-home-card-segment-info"><span>{{$value['owner_name']}}</span>
                    <h3>{{ $value['name']}}</h3>
                    <ul>
                        <li> {{$value['city']}} </li>
                        <li>
                            <span></span>
                        </li>
                        <li> {{$value['country']}} </li>
                    </ul>
                </div>
            </div>
            <div class="cds-ty-uap-all-grid-view-home-card-segment-footer">
                <div class="cds-t31-uap-profile-page-header-card-footer-alert">

                    @if(!empty($value['uap_level_tags']))
                        @for($i=0;$i < collect($value['uap_level_tags'])->max('level');$i++)
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        @endfor
                        @for($i=0;$i < 5 - collect($value['uap_level_tags'])->max('level');$i++)
                            <i class="fa-solid text-dark fa-circle-exclamation" aria-hidden="true"></i>
                        @endfor
                    @else
                    @for($i=0;$i < 5;$i++)
                            <i class="fa-solid text-dark fa-circle-exclamation" aria-hidden="true"></i>
                        @endfor
                    @endif
                </div>
                <ul class="about-tag ms-0">
                    @if(!empty($value['uap_level_tags']))
                    @foreach(collect($value['uap_level_tags'])->where('is_ping',1) as $row)
                    <li><span class="badge-list link">{{$row['tag_name']}}</span></li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </a>
    @endforeach
</div>