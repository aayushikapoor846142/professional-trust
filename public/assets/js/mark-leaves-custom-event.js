function createCalendar(containerId, isPopup = false, inputId = null) {
    const container = document.getElementById(containerId);
    const input = inputId ? document.getElementById(inputId) : null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    let selectedDates = new Set();
    let lastClickedDate = null;
    let isDragging = false;
    let dragStartDate = null;
    const leaveDatesString = document.getElementById('markedLeaves').value;
    const disabledDates = leaveDatesString.split(','); // Now an array
    console.log(disabledDates);

  //[
  //   '2025-05-10',
  //   '2025-05-15',
  //   '2025-05-20'
  // ];// example

    function formatDate(date) {
      return date.toISOString().split('T')[0];
    }

    function isDisabled(date) {
      return date < today || disabledDates.includes(formatDate(date));
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
        }
        buildCalendar(currentMonth, currentYear);
      };

      const monthSelect = document.createElement('select');
      const yearSelect = document.createElement('select');

      const monthNames = ["January", "February", "March", "April", "May", "June",
                          "July", "August", "September", "October", "November", "December"];

      monthNames.forEach((m, i) => {
        const opt = document.createElement('option');
        opt.value = i;
        opt.textContent = m;
        if (i === month) opt.selected = true;
        monthSelect.appendChild(opt);
      });

      const currentYearVal = today.getFullYear();
      for (let y = currentYearVal - 1; y <= currentYearVal + 5; y++) {
        const opt = document.createElement('option');
        opt.value = y;
        opt.textContent = y;
        if (y === year) opt.selected = true;
        yearSelect.appendChild(opt);
      }

      monthSelect.addEventListener('change', () => {
        currentMonth = parseInt(monthSelect.value);
        buildCalendar(currentMonth, currentYear);
      });

      yearSelect.addEventListener('change', () => {
        currentYear = parseInt(yearSelect.value);
        buildCalendar(currentMonth, currentYear);
      });

      header.appendChild(prevBtn);
      header.appendChild(monthSelect);
      header.appendChild(yearSelect);
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
        const dateStr = formatDate(cellDate);

        const cell = document.createElement('div');
        cell.className = 'CDSComponents-Calender-inline01-date';
        cell.textContent = d;
        cell.dataset.date = dateStr;

        const disabled = isDisabled(cellDate);
        if (disabled) {
          cell.classList.add('disabled');
          grid.appendChild(cell);
          continue;
        }

        if (selectedDates.has(dateStr)) {
          cell.classList.add('selected');
        }

        cell.addEventListener('mousedown', (e) => {
          isDragging = true;
          dragStartDate = cellDate;

          if (e.shiftKey && lastClickedDate) {
            let from = new Date(lastClickedDate);
            let to = new Date(cellDate);
            if (from > to) [from, to] = [to, from];
            selectRange(from, to).forEach(date => selectedDates.add(date));
          } else if (e.ctrlKey || e.metaKey) {
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
        });

        cell.addEventListener('mouseenter', () => {
          if (isDragging && dragStartDate) {
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

    if (isPopup && input) {
      input.addEventListener('click', () => {
        container.classList.toggle('visible');
      });
      document.addEventListener('click', (e) => {
        if (!container.contains(e.target) && e.target !== input) {
          container.classList.remove('visible');
        }
      });
      container.addEventListener('click', e => e.stopPropagation());
    }

    buildCalendar(currentMonth, currentYear);
  }

  createCalendar('popupCalendar', true, 'calendarInput');
  createCalendar('inlineCalendar', false);