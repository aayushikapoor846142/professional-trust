<link rel="stylesheet" href="{{url('assets/css/cds-custom-popup-modal.css')}}">
<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<div class="CdsDashboardCustomPopup-modal-container">
    <div class="CdsDashboardCustomPopup-modal-form-wrapper">
        <div class="CdsDashboardCustomPopup-modal-header">
            <h1 class="CdsDashboardCustomPopup-modal-title">{{$modalTitle??''}}</h1>
            <button class="CdsDashboardCustomPopup-modal-close-btn" aria-label="Close" onclick="closeCustomPopup()">×</button>
        </div>
        <div class="CdsDashboardCustomPopup-modal-content">
            @yield('custom-popup-content')
        </div>
        <div class="CdsDashboardCustomPopup-modal-submit-section">
            @yield('custom-popup-footer')
            <!-- <button type="button" class="CdsDashboardCustomPopup-modal-cancel-btn" onclick="closePopup()">Cancel</button>
            <button type="submit" class="CdsDashboardCustomPopup-modal-submit-btn">Create Group</button> -->
        </div>
    </div>
</div>