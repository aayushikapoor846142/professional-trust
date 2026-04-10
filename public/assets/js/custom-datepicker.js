(function (window) {
  const CustomCalendarWidget = {};
  
  CustomCalendarWidget.initialize = function (inputId, param = {}) {
    const multiDateSelect =  param.multiDateSelect !== undefined ? param.multiDateSelect:false;
    const onDateSelect = typeof param.onDateSelect === 'function' ? param.onDateSelect : () => false;
    const disabledDates = Array.isArray(param.disabledDates) ? param.disabledDates : []; // e.g., ['2025-05-09']
    const disabledDays = Array.isArray(param.disabledDays) ? param.disabledDays : [];
    const maxDate = param.maxDate ? param.maxDate : null;
    const minDate = param.minDate ? param.minDate : null;

    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0];
    let defaultDate =  param.defaultDate !== undefined ? param.defaultDate:formattedDate;
    const random_index = Math.floor(1000000000 + Math.random() * 9000000000);
    const containerEle = document.createElement('div');
    containerEle.className = 'CDSComponents-Calender-inline01-container CDSComponents-Calender-inline01-popup';
    containerEle.id = "datepicker-wrapper-"+random_index;
  //   const container = document.getElementById(elementId);
    if (!containerEle) return;
    const input = inputId ? document.getElementById(inputId) : null;
    if(input !== null && input.value != ''){
      defaultDate = input.value;
    }
    input.insertAdjacentElement('afterend', containerEle);
    const container =  document.getElementById("datepicker-wrapper-"+random_index);
    
    today.setHours(0, 0, 0, 0);

    // let currentMonth = today.getMonth();
    // let currentYear = today.getFullYear();
    let defaultDates = maxDate ? new Date(maxDate) : today;
    let defaultYear = defaultDates.getFullYear();
    let defaultMonth = defaultDates.getMonth();
    let currentMonth = (maxDate && new Date(maxDate) < today) ? new Date(maxDate).getMonth() : today.getMonth();
    let currentYear = defaultYear;
    let selectedDates = new Set([defaultDate]);
    let lastClickedDate = null;
    let isDragging = false;
    let dragStartDate = null;
    const monthSelect = document.createElement('select');
    monthSelect.className = "CDSDatepicker-monthSelecter CDSDatepicker-Select";
    const monthWrapper = document.createElement('div');
    monthWrapper.className = "CDSDatepicker-monthWrapper";
    monthWrapper.appendChild(monthSelect);

    // const currentYearVal = today.getFullYear();
    // const yearSelect = document.createElement('input');
    // yearSelect.type = "number";
    // yearSelect.className = "CDSDatepicker-yearInput CDSDatepicker-Input";
    // yearSelect.value = currentYearVal;


    const yearSelect = document.createElement('input');
    yearSelect.type = "number";
    yearSelect.className = "CDSDatepicker-yearInput CDSDatepicker-Input";
    yearSelect.value = defaultYear;



        yearSelect.max = (currentYear + 20);
    
    const yearWrapper = document.createElement('div');
    yearWrapper.className = "CDSDatepicker-yearWrapper";
    yearWrapper.appendChild(yearSelect);

    function formatDate(date) {
      return date.toISOString().split('T')[0];
    }

    function isDisabled(date,compareDate,action) {
      if(action == 'minDate'){
        return date < compareDate;
      }else if(action == 'maxDate'){
        return date > compareDate;
      }
      // return date < today || disabledDates.includes(formatDate(date));
    }

    function selectRange(from, to) {
      const temp = new Date(from);
      const range = [];
      while (temp <= to) {
        const f = formatDate(temp);
        if (!isDisabled(temp)) range.push(f);
        temp.setDate(temp.getDate() + 1);
      }
      return range;
    }

    function updateInput() {

      if (input) {
        input.value = [...selectedDates].sort().join(', ');
        onDateSelect(input.value);
      }
    }

    function updateVisualSelection() {
      const allCells = container.querySelectorAll('.CDSComponents-Calender-inline01-date');
      allCells.forEach(el => {
        const dateStr = el.dataset.date;
        el.classList.toggle('selected', selectedDates.has(dateStr));
      });
    }

    function buildCalendar(month, year) {
      container.innerHTML = '';

      const header = document.createElement('div');
      header.className = 'CDSComponents-Calender-inline01-header';

      const prevBtn = document.createElement('button');
      prevBtn.textContent = '<';
      prevBtn.onclick = () => {
        currentMonth--;
        if (currentMonth < 0) {
          currentMonth = 11;
          currentYear--;
          yearSelect.value = currentYear;
        }
        buildCalendar(currentMonth, currentYear);
      };

      const nextBtn = document.createElement('button');
      nextBtn.textContent = '>';
      nextBtn.onclick = () => {
        currentMonth++;
        if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
          yearSelect.value = currentYear;
        }
        
        buildCalendar(currentMonth, currentYear);
      };

     
      const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
           
      const monthNames = ["January", "February", "March", "April", "May", "June",
                          "July", "August", "September", "October", "November", "December"];

      monthNames.forEach((m, i) => {
        const opt = document.createElement('option');
        opt.value = i;
        opt.textContent = m;
        if (i === month) opt.selected = true;
        monthSelect.appendChild(opt);
      });

     
      // for (let y = 1970; y <= currentYearVal + 5; y++) {
      //   const opt = document.createElement('option');
      //   opt.value = y;
      //   opt.textContent = y;
      //   if (y === year) opt.selected = true;
      //   yearSelect.appendChild(opt);
      // }

      monthSelect.addEventListener('change', () => {
        currentMonth = parseInt(monthSelect.value);
        buildCalendar(currentMonth, currentYear);
      });

      yearSelect.addEventListener('change', () => {
        currentYear = parseInt(yearSelect.value);
        buildCalendar(currentMonth, currentYear);
      });

      header.appendChild(prevBtn);
      header.appendChild(monthWrapper);
      header.appendChild(yearWrapper);
      header.appendChild(nextBtn);

      container.appendChild(header);

      const dayRow = document.createElement('div');
      dayRow.className = 'CDSComponents-Calender-inline01-grid';
      ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
        const div = document.createElement('div');
        div.className = 'CDSComponents-Calender-inline01-day';
        div.textContent = d;
        dayRow.appendChild(div);
      });
      container.appendChild(dayRow);

      const grid = document.createElement('div');
      grid.className = 'CDSComponents-Calender-inline01-grid';

      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();

      for (let i = 0; i < firstDay; i++) {
        grid.appendChild(document.createElement('div'));
      }

      for (let d = 1; d <= daysInMonth; d++) {
        const cellDate = new Date(year, month, d);
        cellDate.setHours(0, 0, 0, 0);
        const dayNo = cellDate.getDay();
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

        const cell = document.createElement('div');
        cell.className = 'CDSComponents-Calender-inline01-date';
        cell.textContent = d;
        cell.dataset.date = dateStr;

        // disable by days

        if(disabledDays.length > 0){
          for(var i = 0;i < disabledDays.length;i++){
              if (disabledDays.includes(dayNames[dayNo])){
                cell.classList.add('disabled');
                grid.appendChild(cell);
              }
          }
        }

        // disable by dates

        if(disabledDates.length > 0){
          for(var i = 0;i < disabledDates.length;i++){
              if (disabledDates.includes(dateStr)){
                cell.classList.add('disabled');
                grid.appendChild(cell);
                continue;
              }
          }
        }

        // disable by max date

        if(maxDate !== null && isDisabled(new Date(dateStr),new Date(maxDate),'maxDate')){
            cell.classList.add('disabled');
            grid.appendChild(cell);
        }

         // disable by min date
        
        if(minDate !== null && isDisabled(new Date(dateStr),new Date(minDate),'minDate')){
            cell.classList.add('disabled');
            grid.appendChild(cell);
        }

        
        

        if (selectedDates.has(dateStr)) {
          cell.classList.add('selected');
        }
        cell.addEventListener('mousedown', (e) => {
          isDragging = true;
          dragStartDate = cellDate;

          if (multiDateSelect && e.shiftKey && lastClickedDate) {
            let from = new Date(lastClickedDate);
            let to = new Date(cellDate);
            if (from > to) [from, to] = [to, from];
            selectRange(from, to).forEach(date => selectedDates.add(date));
          } else if (multiDateSelect && (e.ctrlKey || e.metaKey)) {
            if (selectedDates.has(dateStr)) {
              selectedDates.delete(dateStr);
            } else {
              selectedDates.add(dateStr);
            }
            lastClickedDate = new Date(cellDate);
          } else {
            selectedDates = new Set([dateStr]);
            lastClickedDate = new Date(cellDate);
          }

          updateVisualSelection();
          updateInput();
          if (!multiDateSelect) {
    container.classList.remove('visible');
  }
        });
        cell.addEventListener('mouseenter', () => {
          if (multiDateSelect && isDragging && dragStartDate) {
            let from = new Date(dragStartDate);
            let to = new Date(cellDate);
            if (from > to) [from, to] = [to, from];
            selectedDates = new Set(selectRange(from, to));
            updateVisualSelection();
            updateInput();
          }
        });

        grid.appendChild(cell);
      }

      container.appendChild(grid);
    }

    document.addEventListener('mouseup', () => {
      isDragging = false;
      dragStartDate = null;
    });

    input.addEventListener('click', () => {
      container.classList.toggle('visible');
    });
    document.addEventListener('click', (e) => {
      if (!container.contains(e.target) && e.target !== input) {
        container.classList.remove('visible');
      }
    });
    container.addEventListener('click', e => e.stopPropagation());

    buildCalendar(currentMonth, currentYear);
    
  };
  window.CustomCalendarWidget = CustomCalendarWidget;
})(window);