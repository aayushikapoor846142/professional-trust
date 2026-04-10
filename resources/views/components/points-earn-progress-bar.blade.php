@php
  $userPoints = pointEarns($bg_user->id); // Or use actual user ID logic
  $badgePoints = supportBadgePoints(); // Contains max_points
  $badges = supportBadges(); // Your badge structure
  $currentBadgeName = supportBadge($userPoints);

  $currentPosition = ($userPoints / $badgePoints['max_points']) * 100;
  $showPointsText = ($userPoints >= 200);

  $badge_html = '';
  $upcoming_milestone = '';

  foreach ($badges as $index => $badge) {
    if ($index < count($badges) - 1) {
      $position = ($badge["max"] / $badgePoints['max_points']) * 100; // Show progress marker
      if ($userPoints >= $badge["max"]) {
        $badge_html .= '<div class="separator cds-badge-cross" style="left: ' . $position . '%;"></div>';
      } else {
        $badge_html .= '<div class="separator" style="left: ' . $position . '%;"></div>';
      }

      // Determine upcoming milestone if current badge matches
      if ($badge['name'] == $currentBadgeName) {
        $nextBadge = $badges[$index + 1];
        $diff = $nextBadge['max'] - $userPoints;

        if ($diff > 0) {
          $upcoming_milestone = "{$nextBadge['name']} badge is {$diff} pts away";
        } else {
          $upcoming_milestone = '';
        }
      }
    }
  }
@endphp

    <div class="cdsTYSupportDashboard-main-panel-section-points-wrapper">
        <div class="cdsTYSupportDashboard-main-panel-section-header-points-progress-title cds-point-earn-total-bx">
            <div class="cds-points-achieved-value">
                Total Points:<span> <b>{{ $userPoints }} Points</b></span>
            </div>
            <div class="cds-points-achieved-value">Total Support: <span> ${{ totalSupportAmounts($bg_user->id) }}</span></div>
            <div class="cds-points-achieved-value">Badge Type:<span> {{ supportBadge($userPoints) }} </span></div>
            <div class="cds-points-achieved-value-badge">
                <div class="cdsTYSupportDashboard-main-panel-section-header-points-earn">
                    <img src="{{ supportBadge($userPoints,'image') }}" />
                </div>
            </div>
        </div>
        <div class="cdsTYSupportDashboard-main-panel-section-header-points-progress mx-4">
            <div class="cds-upcoming-milestone">
                {{ $upcoming_milestone }}
            </div>
            <div class="cds-horizontal-progressbar">
                <div class="cdsTYSupportDashboard-main-panel-section-header-points-progress-wrapper poins-progress-wrapper">
                    <div class="cds-progress-wrapper">
                        <div class="cds-progress-bar" id="progressBar">
                            <div class="cds-progress-fill" id="progressFill"></div>
                            <div class="current-points-text" id="tooltip">0 Points</div>
                        </div>
                    </div>
                </div>
            </div>
            @php $verticle_badges = array_reverse($badges); @endphp @if(supportBadge($userPoints) != '')

            <span class="download-pdf">
                <a class="btn btn-sm btn-primary" href="{{ baseUrl('earnings/points-earn-history/download-badge/'.$bg_user->unique_id) }}"><i class="fa fa-download"></i> Download</a>
                <a class="btn btn-sm btn-primary" href="{{ url('badges/'.encrypt($bg_user->unique_id)) }}"><i class="fa fa-eye"></i> View</a>
            </span>
            @endif
        </div>
    </div>

    @push("scripts")
    <script>
        const userPoints = {{ $userPoints }};
        const badges = {!!  json_encode($badges)  !!};

        const totalWeight = badges.reduce((sum, b) => sum + b.weight, 0);
        const bar = document.getElementById('progressBar');
        const fill = document.getElementById('progressFill');
        const tooltip = document.getElementById('tooltip');
        const wrapper = document.querySelector('.cds-progress-wrapper');

        document.querySelectorAll('.milestone-separator, .milestone-label').forEach(el => el.remove());

        let totalProgress = 0;
        for (let i = 0; i < badges.length; i++) {
            const b = badges[i];
            if (userPoints >= b.min && userPoints <= b.max) {
                const range = b.max - b.min;
                const progressInRange = userPoints - b.min;
                const innerProgress = (progressInRange / range) * b.weight;
                totalProgress += innerProgress;
                break;
            } else {
                totalProgress += b.weight;
            }
        }

        const progressPercent = (totalProgress / totalWeight) * 100;

        if ($(window).width() <= 767) {
            fill.style.height = `${Math.min(progressPercent, 100).toFixed(2)}%`;
        } else {
            fill.style.width = `${Math.min(progressPercent, 100).toFixed(2)}%`;
        }

        tooltip.innerText = `${userPoints.toLocaleString()} Points`;
        tooltip.style.left = `${progressPercent.toFixed(2)}%`;

        let accumulatedWeight = 0;
        const outerDiv = document.createElement('div');
        outerDiv.classList.add('milestone-label-wrapper');
        wrapper.appendChild(outerDiv);

        badges.forEach((badge, index) => {
            // Shift milestones to show after previous one
            accumulatedWeight += badge.weight;
            const leftPercent = (accumulatedWeight / totalWeight) * 100;

            // Separator
            const separator = document.createElement('div');
            separator.classList.add('milestone-separator');
            if (userPoints >= badge.max) {
                separator.classList.add('cds-badge-cross');
            }
            if ($(window).width() <= 767) {
                separator.style.height = `${leftPercent}%`;
            } else {
                separator.style.left = `${leftPercent}%`;
            }
            bar.appendChild(separator);

            // Label
            const label = document.createElement('div');
            label.classList.add('milestone-label');
            label.style.left = `${leftPercent}%`;
            label.innerHTML = `${badge.name}<small>${badge.min} - ${badge.max}</small>`;
            label.innerHTML = `${badge.name}<span>${badge.min} - <strong>${badge.max - 1}</strong></span>`;
            outerDiv.appendChild(label);
        });

        const $separators = $('.milestone-separator');
        const $lastCross = $separators.filter('.cds-badge-cross').last();
        const $nextSeparator = $lastCross.nextAll('.milestone-separator').first();
        $nextSeparator.addClass('cds-badge-cross');

    </script>
    @endpush
