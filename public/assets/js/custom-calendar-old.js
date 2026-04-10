(function(window) {
    const CalendarWidget = {};

    CalendarWidget.initialize = function(elementId,selected_date='',days = []) {
        const random_index = Math.floor(1000000000 + Math.random() * 9000000000);
        const containerEle = document.getElementById(elementId);
        if (!containerEle) return;

        // HTML injection
        containerEle.innerHTML = generateHTML(random_index);

        const calendarHeader = document.getElementById('calendarHeader-' + random_index);
        const calendarGrid = document.getElementById('calendarGrid-' + random_index);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();

        function generateHTML(index) {
            return `
            <div class="CDSComponents-Calender-inline01-wrapper">
                <div class="CDSComponents-Calender-inline01-calendar">
                    <div class="CDSComponents-Calender-inline01-header" id="calendarHeader-${index}"></div>
                    <div class="CDSComponents-Calender-inline01-grid" id="calendarGrid-${index}"></div>
                </div>

                <div class="CDSComponents-Calender-inline01-slots-container">
                    <h3 class="font18" id="desktopSlotHeading-${index}">`+((selected_date != '')?'Available Times for '+selected_date:'')+`</h3>
                    <div class="CDSComponents-Calender-inline01-slots-scroll cds-bookSlots" id="desktopSlots-${index}"></div>
                    <div class="CDSComponents-Calender-inline01-show-more" id="desktopShowMore-${index}"></div>
                    <div class="CDSComponents-Calender-inline01-expand-btn">
                        <button type="button" class="btn btn-primary" onclick="CalendarWidget.openModal(${index})"><i class="fa-light fa-up-right-and-down-left-from-center pe-1"></i> Expand</button>
                    </div>
                </div>
            </div>

            <div class="CDSComponents-Calender-inline01-modal-overlay" id="modal-${index}">
                <div class="CDSComponents-Calender-inline01-modal">
                    <button class="close-btn btn" type="button" onclick="CalendarWidget.closeModal(${index})">X</button>
                    <h3 id="modalSlotHeading-${index}">Time Slots</h3>
                    <div class="CDSComponents-Calender-inline01-slots-scroll cds-bookSlots" id="modalSlots-${index}"></div>
                    <div class="CDSComponents-Calender-inline01-show-more" id="modalShowMore-${index}"></div>
                </div>
            </div>

            <div class="CDSComponents-Calender-inline01-mobile-slide" id="mobileSlide-${index}">
                <button class="close-btn" onclick="CalendarWidget.closeSlide(${index})">Close</button>
                <h3 id="mobileSlotHeading-${index}">Time Slots</h3>
                <div class="CDSComponents-Calender-inline01-slots-scroll cds-bookSlots" id="mobileSlots-${index}"></div>
                <div class="CDSComponents-Calender-inline01-show-more" id="mobileShowMore-${index}"></div>
            </div>`;
        }

        async function renderTimeSlots(dateStr, container, heading, moreIndicatorId) {
            container.innerHTML = "";
            heading.textContent = `Available Times for ${dateStr}`;
            await fetchAvailableSlots(dateStr);
            // for (let time in slotPrices) {
            //     const row = document.createElement('div');
            //     row.className = 'CDSComponents-Calender-inline01-slot-row';

            //     const label = `${time} - ${String(Number(time.split(':')[0]) + 1).padStart(2, '0')}:00`;
            //     const price = slotPrices[time];

            //     row.innerHTML = `<div>${label} — $${price} CAD</div>
            //                      <button class="CDSComponents-Calender-inline01-book-btn">Book</button>`;

            //     container.appendChild(row);
            // }
            const moreIndicator = moreIndicatorId ? document.getElementById(moreIndicatorId) : null;
            if (!moreIndicator) return;

            moreIndicator.classList.remove("faded");

            requestAnimationFrame(() => {
                const hiddenCount = container.scrollHeight > container.clientHeight
                    ? Math.floor((container.scrollHeight - container.clientHeight) / 52)
                    : 0;
                if (hiddenCount > 0) {
                    moreIndicator.textContent = `+${hiddenCount} more`;
                    moreIndicator.style.display = 'block';
                } else {
                    moreIndicator.style.display = 'none';
                }

                container.onscroll = () => {
                    const atBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 1;
                    moreIndicator.classList.toggle("faded", atBottom);
                };
            });
        }

        function isMobile() {
            return window.innerWidth < 768;
        }

        function handleDateClick(dateStr, cell) {
            
            document.querySelectorAll('.CDSComponents-Calender-inline01-date').forEach(el => el.classList.remove('selected'));
            cell.classList.add('selected');

            if (isMobile()) {
                renderTimeSlots(dateStr,
                    document.getElementById('mobileSlots-' + random_index),
                    document.getElementById('mobileSlotHeading-' + random_index),
                    'mobileShowMore-' + random_index
                );
                document.getElementById('mobileSlide-' + random_index).classList.add('show');
            } else {
                renderTimeSlots(dateStr,
                    document.getElementById('desktopSlots-' + random_index),
                    document.getElementById('desktopSlotHeading-' + random_index),
                    'desktopShowMore-' + random_index
                );
            }

            renderTimeSlots(dateStr,
                document.getElementById('modalSlots-' + random_index),
                document.getElementById('modalSlotHeading-' + random_index),
                'modalShowMore-' + random_index
            );
        }

        function buildCalendar(month, year) {
            calendarHeader.innerHTML = "";

            const prev = document.createElement('button');
            prev.textContent = "<";
            prev.onclick = () => {
                if (--currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                buildCalendar(currentMonth, currentYear);
            };

            const next = document.createElement('button');
            next.textContent = ">";
            next.onclick = () => {
                if (++currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                buildCalendar(currentMonth, currentYear);
            };

            const monthSelect = document.createElement('select');
            const months = [...Array(12).keys()].map(i => new Date(0, i).toLocaleString('default', { month: 'long' }));
            months.forEach((m, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = m;
                if (i === month) opt.selected = true;
                monthSelect.appendChild(opt);
            });
            monthSelect.onchange = () => {
                currentMonth = parseInt(monthSelect.value);
                buildCalendar(currentMonth, currentYear);
            };

            const yearSelect = document.createElement('select');
            for (let y = today.getFullYear() - 1; y <= today.getFullYear() + 2; y++) {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                if (y === currentYear) opt.selected = true;
                yearSelect.appendChild(opt);
            }
            yearSelect.onchange = () => {
                currentYear = parseInt(yearSelect.value);
                buildCalendar(currentMonth, currentYear);
            };

            calendarHeader.appendChild(prev);
            calendarHeader.appendChild(monthSelect);
            calendarHeader.appendChild(yearSelect);
            calendarHeader.appendChild(next);

            calendarGrid.innerHTML = "";
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(d => {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'CDSComponents-Calender-inline01-day';
                dayDiv.textContent = d;
                calendarGrid.appendChild(dayDiv);
            });

            const firstDay = new Date(year, month, 1).getDay();
            const numDays = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                calendarGrid.appendChild(document.createElement('div'));
            }
            const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
           
            for (let d = 1; d <= numDays; d++) {
                const cell = document.createElement('div');
                const date = new Date(year, month, d);
                const day = date.getDay();
                const dayName = dayNames[day];
                // const dateStr = date.toISOString().split('T')[0];
                const dateStr = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
                cell.className = 'CDSComponents-Calender-inline01-date';
                cell.textContent = d;
             
                if(selected_date == dateStr){
                    cell.classList.add('selected');
                }
                if (date < today) {
                    cell.classList.add('disabled');
                }else if(!days.includes(dayName.toLowerCase())){
                    cell.classList.add('disabled');
                } else {
                    cell.onclick = () => handleDateClick(dateStr, cell);
                }
                
                calendarGrid.appendChild(cell);
            }
        }

        buildCalendar(currentMonth, currentYear);
    };

    CalendarWidget.openModal = function(index) {
        document.getElementById('modal-' + index).style.display = 'flex';
    };

    CalendarWidget.closeModal = function(index) {
        document.getElementById('modal-' + index).style.display = 'none';
    };

    CalendarWidget.closeSlide = function(index) {
        document.getElementById('mobileSlide-' + index).classList.remove('show');
    };

    window.CalendarWidget = CalendarWidget;
})(window);
