@extends('admin-panel.layouts.app')

@section('content')
<!-- Content -->
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
------------------

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cds-ty-dashboard-box" style="overflow:auto;">
                 <!-- Sidebar: Year/Months -->
                                    
                    <div class="calendar-container">
                        <!-- Sidebar: Year + Months -->
                        <div class="calendar-sidebar">
                        <div class="year-nav">
                            <button id="prev-year" class="year-btn">‹</button>
                            <span id="sidebar-year">2025</span>
                            <button id="next-year" class="year-btn">›</button>
                        </div>
                        <ul id="month-list" class="month-list">
                            <li data-month="0">January <span>0</span></li>
                            <li data-month="1">February <span>0</span></li>
                            <li data-month="2">March <span>0</span></li>
                            <li data-month="3">April <span>0</span></li>
                            <li data-month="4" class="active">May <span>7</span></li>
                            <li data-month="5">June <span>0</span></li>
                            <li data-month="6">July <span>0</span></li>
                            <li data-month="7">August <span>0</span></li>
                            <li data-month="8">September <span>0</span></li>
                            <li data-month="9">October <span>0</span></li>
                            <li data-month="10">November <span>0</span></li>
                            <li data-month="11">December <span>0</span></li>
                        </ul>
                        </div>

                        <!-- Main Calendar -->
                        <div class="main-calendar">
                        <div class="main-calendar-header">
                            <button id="prev-month" class="nav-btn">‹</button>
                            <h2 id="current-month-year">May 2025</h2>
                            <button id="next-month" class="nav-btn">›</button>
                            <img src="https://via.placeholder.com/42" alt="Avatar"/>
                        </div>
                        <div id="calendar-grid" class="calendar-grid">
                            <!-- days & dates injected by JS -->
                        </div>
                        </div>

                        <!-- Event List -->
                        <div class="event-sidebar">
                        <h3>Events</h3>
                        <div id="event-list">
                            <!-- events for selected date -->
                        </div>
                        </div>
                    </div>

                    <!-- Modal for Event Details -->
                    <div id="event-modal" class="cds-event-calendar-modal-overlay">
                        <div class="cds-event-calendar-modal">
                        <span id="cds-event-calendar-modal-close" class="cds-event-calendar-modal-close">&times;</span>
                        <h3 id="cds-event-calendar-modal-title"></h3>
                        <p id="cds-event-calendar-modal-time" style="font-weight: 500; margin: 12px 0;"></p>
                        <p id="cds-event-calendar-modal-description" style="line-height: 1.5;"></p>
                        </div>
                    </div>
            </div>
     
			</div>
	
	</div>
  </div>
</div>

<!-- End Content -->
@endsection

@section('javascript')
<!-- <script src="{{url('assets/js/custom-event-calendar.js')}}"></script> -->
<link href="{{ url('assets/css/custom-event-calendar.css') }}" rel="stylesheet" />
<script>
document.addEventListener('DOMContentLoaded', function(){
var events = {}; // Global scope

const monthNames = ["January","February","March","April","May","June",
                    "July","August","September","October","November","December"];
const dayNames = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
const eventColors = ["#42a5f5","#ab47bc","#ffca28","#66bb6a","#ef5350","#26c6da"];

let currentYear = 2025;
let currentMonth = 4; // May (0-indexed)
let selectedDay = 1;

// Fetch events THEN initialize UI
fetchEvents(currentYear, currentMonth, function () {
  const mEv = events[currentMonth] || [];
  selectedDay = mEv.length ? mEv[0].day : 1;

  updateSidebarCounts();
  setActiveMonthYear();
  generateCalendar();
  showEvents();
});

function fetchEvents(year, month, callback) {
  $.ajax({
    url: "{{ baseUrl('appointments/appointment-booking/fetch-appointments') }}",
    type: 'GET',
    data: {
      year: year,
      month: month + 1, // 0-indexed in JS
    },
    success: function (data) {
      events = {}; // Reset

      data.forEach(event => {
        const evtMonth = event.month - 1; // To 0-indexed
        if (!events[evtMonth]) events[evtMonth] = [];
        events[evtMonth].push({
          day: event.day,
          time: event.time,
          title: event.title,
          description: event.description,
        });
      });

      console.log("Fetched Events:", events);
      if (typeof callback === 'function') callback();
    },
    error: function (xhr, status, error) {
      console.error("Error loading events:", error);
      if (typeof callback === 'function') callback(); // Still call to prevent hang
    }
  });
}




    const sidebarYearEl = document.getElementById('sidebar-year');
    const monthListEls  = Array.from(document.querySelectorAll('#month-list li'));
    const calendarGrid  = document.getElementById('calendar-grid');
    const headerEl      = document.getElementById('current-month-year');
    const eventListEl   = document.getElementById('event-list');
    const modalOverlay  = document.getElementById('event-modal');
    const modalClose    = document.getElementById('cds-event-calendar-modal-close');
    const modalTitle    = document.getElementById('cds-event-calendar-modal-title');
    const modalTime     = document.getElementById('cds-event-calendar-modal-time');
    const modalDesc     = document.getElementById('cds-event-calendar-modal-description');
    const prevYearBtn   = document.getElementById('prev-year');
    const nextYearBtn   = document.getElementById('next-year');
    const prevMonthBtn  = document.getElementById('prev-month');
    const nextMonthBtn  = document.getElementById('next-month');

    function updateSidebarCounts() {
      monthListEls.forEach(li => {
        const m = +li.dataset.month;
        li.querySelector('span').textContent = (events[m]||[]).length;
      });
    }

    function setActiveMonthYear() {
      monthListEls.forEach(li => li.classList.remove('active'));
      monthListEls[currentMonth].classList.add('active');
      headerEl.textContent      = `${monthNames[currentMonth]} ${currentYear}`;
      sidebarYearEl.textContent = currentYear;
    }

    function generateCalendar() {
      let html = dayNames.map(d => `<div class="day">${d}</div>`).join('');
      const firstDow = new Date(currentYear, currentMonth, 1).getDay();
      html += '<div class="empty"></div>'.repeat(firstDow);
      const dim = new Date(currentYear, currentMonth+1, 0).getDate();
      for (let d=1; d<=dim; d++) {
        const evts = (events[currentMonth]||[]).filter(ev => ev.day===d);
        const count = evts.length;
        const isAct = d===selectedDay;
        const cls = ['date']
          .concat(count ? ['has-event'] : [])
          .concat(isAct  ? ['active']    : [])
          .join(' ');
        html += `<div class="${cls}" data-day="${d}">
                   ${d}
                   ${count ? `<span class="event-count">${count}</span>` : ''}
                 </div>`;
      }
      calendarGrid.innerHTML = html;
    }

    function showEvents() {
      const todays = (events[currentMonth]||[]).filter(ev => ev.day===selectedDay);
      eventListEl.innerHTML = todays.length
        ? todays.map((ev,i) => `
            <div class="event-item" data-index="${i}">
              <span class="event-dot" style="background:${eventColors[i % eventColors.length]}"></span>
              <strong>${ev.time}</strong> – ${ev.title}
            </div>
          `).join('')
        : '<p>No events.</p>';
      document.querySelectorAll('.event-item').forEach(el => {
        el.addEventListener('click', () => {
          const ev = events[currentMonth][+el.dataset.index];
          modalTitle.textContent = ev.title;
          modalTime.textContent  = ev.time;
          modalDesc.textContent  = ev.description;
          modalOverlay.classList.add('active');
        });
      });
    }

    function eventCloseModal() {
      modalOverlay.classList.remove('active');
    }

    prevYearBtn.addEventListener('click', () => {
    currentYear--;
    fetchEvents(currentYear, currentMonth, () => {
        const mEv = events[currentMonth] || [];
        selectedDay = mEv.length ? mEv[0].day : 1;
        setActiveMonthYear();
        generateCalendar();
        showEvents();
        updateSidebarCounts();
    });
    });

    nextYearBtn.addEventListener('click', () => {
    currentYear++;
    fetchEvents(currentYear, currentMonth, () => {
        const mEv = events[currentMonth] || [];
        selectedDay = mEv.length ? mEv[0].day : 1;
        setActiveMonthYear();
        generateCalendar();
        showEvents();
        updateSidebarCounts();
    });
    });


    monthListEls.forEach(li => {
      li.addEventListener('click', () => {
        currentMonth = +li.dataset.month;
        const mEv = events[currentMonth] || [];
        selectedDay = mEv.length ? mEv[0].day : 1;
        setActiveMonthYear(); generateCalendar(); showEvents();
      });
    });

    calendarGrid.addEventListener('click', e => {
      if (e.target.classList.contains('date')) {
        selectedDay = +e.target.dataset.day;
        generateCalendar(); showEvents();
      }
    });

    prevMonthBtn.addEventListener('click', () => {
  currentMonth--; 
  if (currentMonth < 0) {
    currentMonth = 11; currentYear--;
  }
  fetchEvents(currentYear, currentMonth, () => {
        const mEv = events[currentMonth] || [];
        selectedDay = mEv.length ? mEv[0].day : 1;
        setActiveMonthYear();
        generateCalendar();
        showEvents();
        updateSidebarCounts();
    });
    });
    nextMonthBtn.addEventListener('click', () => {
    currentMonth++; 
    if (currentMonth > 11) {
        currentMonth = 0; currentYear++;
    }
    fetchEvents(currentYear, currentMonth, () => {
        const mEv = events[currentMonth] || [];
        selectedDay = mEv.length ? mEv[0].day : 1;
        setActiveMonthYear();
        generateCalendar();
        showEvents();
        updateSidebarCounts();
    });
    });


    modalClose.addEventListener('click', eventCloseModal);
    modalOverlay.addEventListener('click', e => {
      if (e.target === modalOverlay) eventCloseModal();
    });

    // initialize
    updateSidebarCounts();
    setActiveMonthYear();
    generateCalendar();
    showEvents();
  });
  </script>

@endsection