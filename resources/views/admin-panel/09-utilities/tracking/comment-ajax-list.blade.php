@if(isset($records) && !empty($records))
    @foreach($records as $key => $value)
        <div class="cds-ty-33-review-card " id="share-{{$value['unique_id']}}{{$key}}">
            <div class="cds-ty-33-review-card-body">
                <div class="d-flex flex-column align-items-start">
                    <p class="cds-ty-33-review-card-content feedback-comment">{{$value['comment']}}</p>
                    <div class="d-flex mt-3">
                        <div>
                            <h6 class="cds-ty-33-review-author">{{$value['added_by_name']}}</h6>
                            <p class="cds-ty-33-review-date">{{date('M Y', strtotime($value['created_at']))}}</p>
                        </div>
                    </div>
                </div>
            </div>
     
        </div>
    @endforeach
    
@endif
