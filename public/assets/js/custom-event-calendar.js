(function (window) {
  const EventCalendarWidget = {};

  EventCalendarWidget.initialize = function (elementId, param = {} , formData = []) {
   
    // Parameters
    const eventAjaxUrl = param.eventAjaxUrl !== undefined ? param.eventAjaxUrl:null;
    const multiDateSelect =  param.multiDateSelect !== undefined ? param.multiDateSelect:true;
    const onDateSelect = typeof param.onDateSelect === 'function' ? param.onDateSelect : () => false;
    const disabledDates = (param.disabledDates !== undefined && Array.isArray(param.disabledDates)) ? param.disabledDates : []; // e.g., ['2025-05-09']
    const disabledDays = (param.disabledDays !== undefined && Array.isArray(param.disabledDays)) ? param.disabledDays : []; 
    
    const random_index = Math.floor(1000000000 + Math.random() * 9000000000);
    const containerEle = document.getElementById(elementId);
    if (!containerEle) return;

    let events = {};
    const monthNames = ["January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    let selectedDates = [];
    let htmleventcode = '';

    containerEle.innerHTML = generateHTML(random_index);

    const sidebarYearEl = document.getElementById('sidebar-year-' + random_index);
    const monthListEls = Array.from(document.querySelectorAll('#month-list-' + random_index + ' li'));
    const calendarGrid = document.getElementById('calendar-grid-' + random_index);
    const headerEl = document.getElementById('current-month-year-' + random_index);
    const eventListEl = document.getElementById('event-list-' + random_index);
    const modalOverlay = document.getElementById('event-modal-' + random_index);
    const modalClose = document.getElementById('cds-event-calendar-modal-close-' + random_index);
    const modalTitle = document.getElementById('cds-event-calendar-modal-title-' + random_index);
    const modalTime = document.getElementById('cds-event-calendar-modal-time-' + random_index);
    const modalDesc = document.getElementById('cds-event-calendar-modal-description-' + random_index);
    const prevYearBtn = document.getElementById('prev-year-' + random_index);
    const nextYearBtn = document.getElementById('next-year-' + random_index);
    const prevMonthBtn = document.getElementById('prev-month-' + random_index);
    const nextMonthBtn = document.getElementById('next-month-' + random_index);
    
    function generateHTML(index) {
      var eventSidebarSection = '';
      if(eventAjaxUrl !== null){
        eventSidebarSection = `<div class="event-sidebar">
            <div id="event-list-${index}"></div>
          </div>`;
      }
      return `<div class="cds-event-calendar">
        <div class="calendar-container">
          <div class="calendar-sidebar">
            <div class="year-nav">
              <button id="prev-year-${index}" class="year-btn">‹</button>
              <span id="sidebar-year-${index}">${currentYear}</span>
              <button id="next-year-${index}" class="year-btn">›</button>
            </div>
            <ul id="month-list-${index}" class="month-list">
              ${monthNames.map((month, i) => `<li data-month="${i}">${month} <span>0</span></li>`).join('')}
            </ul>
          </div>
          <div class="main-calendar">
            <div class="main-calendar-header">
              <button id="prev-month-${index}" class="nav-btn">‹</button>
              <h2 id="current-month-year-${index}">${monthNames[currentMonth]} ${currentYear}</h2>
              <button id="next-month-${index}" class="nav-btn">›</button>
            </div>
            <div id="calendar-grid-${index}" class="calendar-grid"></div>
          </div>
          ${eventSidebarSection}
        </div>
        <div id="event-modal-${index}" class="cds-event-calendar-modal-overlay">
          <div class="cds-event-calendar-modal">
            <span id="cds-event-calendar-modal-close-${index}" class="cds-event-calendar-modal-close">&times;</span>
            <h3 id="cds-event-calendar-modal-title-${index}"></h3>
            <p id="cds-event-calendar-modal-time-${index}" style="font-weight: 500; margin: 12px 0;"></p>
            <p id="cds-event-calendar-modal-description-${index}" style="line-height: 1.5;"></p>
          </div>
        </div>
      </div>`;
    }

    function fetchEvents(year, month, formData, selectedDay, callback) {
      if(eventAjaxUrl === null){
        bindEventClickListeners();
        callback();
        return false;
      }
      $.ajax({
        url: eventAjaxUrl,
        type: 'POST',
        data: {
          _token: csrf_token,
          formData: formData,
          year: year,
          month: month + 1,
          selectedDay: selectedDay
        },
        success: function (data) {
          events = data.events;
          htmleventcode = data.html;
          eventListEl.innerHTML = htmleventcode;
          bindEventClickListeners();
          callback();
        }
      });
    }

    function bindEventClickListeners() {
      document.querySelectorAll('.event-item').forEach(el => {
        el.addEventListener('click', () => {
          const ev = events[currentMonth][+el.dataset.index];
          modalTitle.textContent = ev.title;
          modalTime.textContent = ev.time;
          modalDesc.textContent = ev.description;
          modalOverlay.style.display = 'flex';
        });
      });
    }

    function updateSidebarCounts() {
      monthListEls.forEach(li => {
        const m = +li.dataset.month;
        li.querySelector('span').textContent = (events[m] || []).length;
      });
    }

    function setActiveMonthYear() {
      headerEl.textContent = `${monthNames[currentMonth]} ${currentYear}`;
      sidebarYearEl.textContent = currentYear;
      monthListEls.forEach(li => li.classList.remove('active'));
      const currentLi = monthListEls.find(li => +li.dataset.month === currentMonth);
      if (currentLi) currentLi.classList.add('active');
    }

    function generateCalendar(onload = false) {
      const firstDay = new Date(currentYear, currentMonth, 1).getDay();
      const totalDays = new Date(currentYear, currentMonth + 1, 0).getDate();
      let html = '';
      // Render day names
      html += dayNames.map(day => `<div class="day">${day}</div>`).join('');
    
      // Render empty cells before the first day
      for (let i = 0; i < firstDay; i++) {
        html += `<div class="empty"></div>`;
      }
      // Render actual days
      console.log(disabledDays,"disabledDays");
      for (let d = 1; d <= totalDays; d++) {
        const dateObj = new Date(currentYear, currentMonth, d);
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const weekday = dateObj.getDay();
        const dayName = dayNames[weekday];
        const isDisabledDate = disabledDates.includes(dateStr);
        const isDisabledDay = disabledDays.includes(dayName);

        const evts = (events[currentMonth] || []).filter(ev => ev.day === d);
        const count = evts.length;
        const isSelected = selectedDates.includes(dateStr);
    
        const classes = ['date'];
        if (count > 0) classes.push('has-event');
        if (isSelected){
          classes.push('selected');
        }
        console.log(isDisabledDay,dayName);
        if (isDisabledDate || isDisabledDay){
          classes.push('disabled');
        }
        html += `<div class="${classes.join(' ')}" data-day="${d}">
                   ${d}
                   ${count ? `<span class="event-count">${count}</span>` : ''}
                 </div>`;
      }
    
      calendarGrid.innerHTML = html;
      
    }

    function refreshUI() {
      setActiveMonthYear();
      generateCalendar(true);
      updateSidebarCounts();
    }

    function showEvents(selectedDay) {
        
      fetchEvents(currentYear, currentMonth, formData, selectedDay, () => {
        // refreshUI();
      });
    
     }
    function eventCloseModal() {
      modalOverlay.style.display = 'none';
    }

    // Navigation Events
    prevYearBtn.addEventListener('click', () => {
      currentYear--;
      fetchEvents(currentYear, currentMonth, formData, '', () => {
        refreshUI();
      });
    });

    nextYearBtn.addEventListener('click', () => {
      currentYear++;
      fetchEvents(currentYear, currentMonth, formData, '', () => {
        refreshUI();
      });
    });

    prevMonthBtn.addEventListener('click', () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      fetchEvents(currentYear, currentMonth, formData, '', () => {
        refreshUI();
      });
    });

    nextMonthBtn.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      fetchEvents(currentYear, currentMonth, formData, '', () => {
        refreshUI();
      });
    });

    calendarGrid.addEventListener('click', e => {
      if (e.target.classList.contains('date')) {
        if (e.target.classList.contains('disabled')) return;
        const clickedDay = +e.target.dataset.day;
        var dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(clickedDay).padStart(2, '0')}`;
        if(multiDateSelect){
          if (e.ctrlKey) {
            // Toggle the clicked day in selectedDates (multi-select)
            const index = selectedDates.indexOf(dateStr);
            if (index > -1) {
              selectedDates.splice(index, 1); // remove
            } else {
              selectedDates.push(dateStr); // add
            }
          } else {
            // Ctrl not pressed: select only this day (single-select)
            selectedDates.length = 0;
            selectedDates.push(dateStr);
          }
        }else{
            selectedDates.length = 0;
            selectedDates.push(dateStr);
        }
        generateCalendar(); 
        onDateSelect(selectedDates); // Callback events on select date
        showEvents(clickedDay);
      }
    });
    

    monthListEls.forEach(li => {
      li.addEventListener('click', () => {
        currentMonth = +li.dataset.month;
        refreshUI();
      });
    });

    modalClose.addEventListener('click', eventCloseModal);
    
    // Initial load
    fetchEvents(currentYear, currentMonth, formData, selectedDates, () => {
      refreshUI();
    });
  };

  window.EventCalendarWidget = EventCalendarWidget;
})(window);
