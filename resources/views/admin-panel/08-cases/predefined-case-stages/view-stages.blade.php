<div class="row">
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Name:</label>
        <div>{{ $record->name ?? 'N/A' }}</div>
    </div>
  
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Fees:</label>
        <div> {{$record->fees}}</div>
    </div>
     <div class="col-md-6 mb-3">
        <label class="fw-bold">Sort Order:</label>
        <div> {{$record->sort_order}}</div>
    </div>
    <div class="col-md-12 mb-3">
        <label class="fw-bold">Short Description:</label>
        <div>{{ $record->short_description ?? 'N/A' }}</div>
    </div>
    
</div>