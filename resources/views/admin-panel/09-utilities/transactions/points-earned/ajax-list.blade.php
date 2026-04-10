@if(count($records) > 0)
    @foreach($records as $key => $record)
        @if(!empty($record->earnBadge))
            <div class="text-center badges-row"><img width="30" src="{{ otherFileDirUrl($record->earnBadge->badge->badge_image,'m') }}" /> {{ $record->earnBadge->badge->badge_name }}</div>
        @endif
        <div class="cdsTYDashboard-table-row">
            <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at) }}</div>
            <div class="cdsTYDashboard-table-cell" data-label="Actual Points">{{$record->points ?? ''}}</div>
            <div class="cdsTYDashboard-table-cell" data-label="Bonus Points">{{$record->bonus_points ?? ''}}</div>
            <div class="cdsTYDashboard-table-cell" data-label="Total Points"><b>{{$record->total_points ?? ''}}</b></div>
        </div>
       

        @endforeach
    @else
   
@endif
