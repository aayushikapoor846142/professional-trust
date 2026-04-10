<style>
@media print {
  header,.cds-ty11-top-menu-container,.cds-t212-content-section-page-title,.cds-t27-support-section,.cds-t24-footer-note-section,.cds-t24-common-links-section,.cds-t24-footer-upper-section,.cds-t24-footer-section{
    display: none !important;
  }

}

/* Hide elements with .only-print by default on screen */

</style>
<div class="certificate-wrapper-outer only-print" id="certificate-wrapper-outer">
    <div class="certificate-wrapper">
        <div class="certificate-wrapper-inner">
            <div class="certificate">

                <div class="certificate-logo">
                    <img id="badgeImage" class="badge-image" src="{{ otherFileDirUrl($badge->badge_image,'m') }}"
                        alt="{{ $badge->badge_name }}">
                </div>

                <div class="certificate-title">Certificate of Recognition</div>
                <div class="certificate-subtitle">{{ $badge->badge_name }} Awarded In Honor of Outstanding Contribution</div>

                <div class="recipient-name">{{ $user->first_name }} {{ $user->last_name }}</div>

                <div class="certificate-body">
                    This certificate is proudly presented to <strong>{{ $user->first_name }}
                    {{ $user->last_name }}</strong> in recognition of their outstanding dedication and meaningful contribution in supporting our mission promoting ethical and trustworthy immigration practices.<Br>
                    <strong>Your efforts will have a lasting impact and continue to inspire change.</strong></p>
                </div>

                <div class="certificate-footer">

                    <div>
                        <div class="line">{{ date("d/m/Y") }}</div>
                        <div>Date</div>
                    </div>
                    <div>
                        <div class="line">
                          <img src="{{url('/')}}/assets/images/logo-c.png" width="170" />
                        </div>
                        <div>Authorized Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<button class="btn-print" onclick="window.print()">Download / Print Certificate</button>
<!-- <a class="btn btn-sm btn-primary" href="{{ url('download-badge/'.$user->unique_id) }}"><i class="fa fa-download"></i>
    Download</a> -->

