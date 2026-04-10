(function(window) {
            'use strict';

            class EnhancedCalendar {
                constructor(selector, options = {}) {
                    // Default options
                    this.options = {
                        events: {},
                        onDateSelect: null,
                        onEventClick: null,
                        onAddEvent: null,
                        theme: 'default',
                        startDate: new Date(),
                        locale: 'en-US',
                        ...options
                    };

                    // Initialize container
                    this.container = typeof selector === 'string' 
                        ? document.querySelector(selector) 
                        : selector;

                    if (!this.container) {
                        throw new Error('EnhancedCalendar: Container element not found');
                    }

                    // Calendar state
                    this.currentDate = new Date(this.options.startDate);
                    this.selectedDate = new Date(this.options.startDate);
                    this.monthNames = ["January", "February", "March", "April", "May", "June", 
                                      "July", "August", "September", "October", "November", "December"];
                    this.dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
this.options.disabledDays = options.disabledDays || []; // Array of day numbers (0=Sunday to 6=Saturday)
        this.options.disabledDates = options.disabledDates || []; // Array of date strings (YYYY-MM-DD)
                    // Initialize
                    this.init();
                }

                init() {
                    // Inject styles
                    this.injectStyles();
                    
                    // Create calendar structure
                    this.createCalendarStructure();
                    
                    // Bind events
                    this.bindEvents();
                    
                    // Generate initial calendar
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                    
                    // Auto-select current date
                    if (this.options.autoSelectToday) {
                        this.selectDate(this.selectedDate);
                    }
                }

                injectStyles() {
                    if (document.getElementById('enhanced-calendar-styles')) return;

                    const styles = ` `;

                    const styleSheet = document.createElement('style');
                    styleSheet.id = 'enhanced-calendar-styles';
                    styleSheet.textContent = styles;
                    document.head.appendChild(styleSheet);
                }

                createCalendarStructure() {
                    this.container.innerHTML = `
                        <div class="CDSDashboardAppointmentEnhancedCalender-app-container">
                            <!-- Header -->
                            <header class="CDSDashboardAppointmentEnhancedCalender-app-header">
                                <div class="CDSDashboardAppointmentEnhancedCalender-header-content">
                                    <h1 class="CDSDashboardAppointmentEnhancedCalender-app-title">${this.options.title || 'Calendar Pro'}</h1>
                                    <div class="CDSDashboardAppointmentEnhancedCalender-header-actions">
                                        <div class="CDSDashboardAppointmentEnhancedCalender-view-toggle">
                                            <button class="CDSDashboardAppointmentEnhancedCalender-view-btn CDSDashboardAppointmentEnhancedCalender-active" data-view="month">Month</button>
                                            <button class="CDSDashboardAppointmentEnhancedCalender-view-btn" data-view="week">Week</button>
                                            <button class="CDSDashboardAppointmentEnhancedCalender-view-btn" data-view="day">Day</button>
                                        </div>
                                        <button class="CDSDashboardAppointmentEnhancedCalender-today-btn">Today</button>
                                    </div>
                                </div>
                            </header>

                            <!-- Calendar -->
                            <div class="CDSDashboardAppointmentEnhancedCalender-calendar-container">
                                <div class="CDSDashboardAppointmentEnhancedCalender-calendar-wrapper">
                                    <div class="CDSDashboardAppointmentEnhancedCalender-calendar-header">
                                        <div class="CDSDashboardAppointmentEnhancedCalender-month-year">
                                            <span id="${this.getUniqueId('current-month')}">Loading...</span>
                                        </div>
                                        <div class="CDSDashboardAppointmentEnhancedCalender-month-nav">
                                            <button class="CDSDashboardAppointmentEnhancedCalender-nav-btn" id="${this.getUniqueId('prev-month')}">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                                                </svg>
                                            </button>
                                            <button class="CDSDashboardAppointmentEnhancedCalender-nav-btn" id="${this.getUniqueId('next-month')}">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="CDSDashboardAppointmentEnhancedCalender-calendar-body">
                                        <div class="CDSDashboardAppointmentEnhancedCalender-weekdays">
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Sun</div>
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Mon</div>
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Tue</div>
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Wed</div>
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Thu</div>
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Fri</div>
                                            <div class="CDSDashboardAppointmentEnhancedCalender-weekday">Sat</div>
                                        </div>
                                        <div class="CDSDashboardAppointmentEnhancedCalender-calendar-grid" id="${this.getUniqueId('calendar-grid')}">
                                            <!-- Calendar days will be generated by JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Overlay -->
                            <div class="CDSDashboardAppointmentEnhancedCalender-overlay" id="${this.getUniqueId('overlay')}"></div>

                            <!-- Floating Agenda Panel -->
                            <div class="CDSDashboardAppointmentEnhancedCalender-agenda-panel" id="${this.getUniqueId('agenda-panel')}">
                                <div class="CDSDashboardAppointmentEnhancedCalender-agenda-header">
                                    <div>
                                        <div class="CDSDashboardAppointmentEnhancedCalender-agenda-date" id="${this.getUniqueId('agenda-date')}">Select a date</div>
                                        <div class="CDSDashboardAppointmentEnhancedCalender-agenda-day" id="${this.getUniqueId('agenda-day')}"></div>
                                    </div>
                                    <button class="CDSDashboardAppointmentEnhancedCalender-close-btn" id="${this.getUniqueId('close-agenda')}">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <div class="CDSDashboardAppointmentEnhancedCalender-agenda-content" id="${this.getUniqueId('agenda-content')}">
                                    <!-- Agenda items will be generated by JavaScript -->
                                </div>
                                 <button class="CDSDashboardAppointmentEnhancedCalender-quick-add-btn d-none" title="Add Event">+</button>
                            </div>
                        </div>
                    `;

                    // Cache DOM elements
                    this.elements = {
                        calendarGrid: this.container.querySelector(`#${this.getUniqueId('calendar-grid')}`),
                        currentMonth: this.container.querySelector(`#${this.getUniqueId('current-month')}`),
                        overlay: this.container.querySelector(`#${this.getUniqueId('overlay')}`),
                        agendaPanel: this.container.querySelector(`#${this.getUniqueId('agenda-panel')}`),
                        agendaDate: this.container.querySelector(`#${this.getUniqueId('agenda-date')}`),
                        agendaDay: this.container.querySelector(`#${this.getUniqueId('agenda-day')}`),
                        agendaContent: this.container.querySelector(`#${this.getUniqueId('agenda-content')}`),
                        closeAgendaBtn: this.container.querySelector(`#${this.getUniqueId('close-agenda')}`),
                        prevMonthBtn: this.container.querySelector(`#${this.getUniqueId('prev-month')}`),
                        nextMonthBtn: this.container.querySelector(`#${this.getUniqueId('next-month')}`),
                        todayBtn: this.container.querySelector('.CDSDashboardAppointmentEnhancedCalender-today-btn'),
                        quickAddBtn: this.container.querySelector('.CDSDashboardAppointmentEnhancedCalender-quick-add-btn'),
                        viewBtns: this.container.querySelectorAll('.CDSDashboardAppointmentEnhancedCalender-view-btn')
                    };
                }

                getUniqueId(prefix) {
                    if (!this._uniqueId) {
                        this._uniqueId = 'calendar-' + Math.random().toString(36).substr(2, 9);
                    }
                    return `${this._uniqueId}-${prefix}`;
                }

                bindEvents() {
                    // Navigation
                    this.elements.prevMonthBtn.addEventListener('click', () => this.navigateMonth(-1));
                    this.elements.nextMonthBtn.addEventListener('click', () => this.navigateMonth(1));
                    this.elements.todayBtn.addEventListener('click', () => this.goToToday());

                    // Agenda panel
                    this.elements.closeAgendaBtn.addEventListener('click', () => this.hideAgenda());
                    this.elements.overlay.addEventListener('click', () => this.hideAgenda());
                    this.elements.agendaPanel.addEventListener('click', (e) => e.stopPropagation());

                    // Quick add
                    this.elements.quickAddBtn.addEventListener('click', () => {
                        if (this.options.onAddEvent) {
                            this.options.onAddEvent(this.selectedDate);
                        } else {
                            alert('Add event functionality would open a form here');
                        }
                    });

                    // View toggle
                    this.elements.viewBtns.forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            this.elements.viewBtns.forEach(b => b.classList.remove('CDSDashboardAppointmentEnhancedCalender-active'));
                            e.target.classList.add('CDSDashboardAppointmentEnhancedCalender-active');
                            if (this.options.onViewChange) {
                                this.options.onViewChange(e.target.dataset.view);
                            }
                        });
                    });

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.elements.agendaPanel.classList.contains('CDSDashboardAppointmentEnhancedCalender-active')) {
                            this.hideAgenda();
                        }
                    });

                    // Touch gestures
                    this.setupTouchGestures();
                }

                setupTouchGestures() {
                    let touchStartX = 0;
                    let touchEndX = 0;

                    this.elements.agendaPanel.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                    });

                    this.elements.agendaPanel.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        if (touchEndX > touchStartX + 50) {
                            this.hideAgenda();
                        }
                    });
                }

                generateCalendar(year, month) {
                    this.elements.calendarGrid.innerHTML = '';
                    
                    const firstDay = new Date(year, month, 1).getDay();
                    const daysInMonth = new Date(year, month + 1, 0).getDate();
                    const daysInPrevMonth = new Date(year, month, 0).getDate();
                    
                    // Previous month days
                    for (let i = firstDay - 1; i >= 0; i--) {
                        const day = daysInPrevMonth - i;
                        const dayEl = this.createDayElement(day, true, new Date(year, month - 1, day));
                        this.elements.calendarGrid.appendChild(dayEl);
                    }
                    
                    // Current month days
                    for (let day = 1; day <= daysInMonth; day++) {
                        const date = new Date(year, month, day);
                        const dayEl = this.createDayElement(day, false, date);
                        this.elements.calendarGrid.appendChild(dayEl);
                    }
                    
                    // Next month days
                    const remainingDays = 42 - (firstDay + daysInMonth);
                    for (let day = 1; day <= remainingDays; day++) {
                        const dayEl = this.createDayElement(day, true, new Date(year, month + 1, day));
                        this.elements.calendarGrid.appendChild(dayEl);
                    }
                    
                    this.elements.currentMonth.textContent = `${this.monthNames[month]} ${year}`;
                }
 isDateDisabled(date) {
        const dayOfWeek = date.getDay(); // 0 (Sunday) to 6 (Saturday)
        const dateStr = this.formatDateKey(date);
        
        // Check if this day of week is disabled
        if (this.options.disabledDays.includes(dayOfWeek)) {
            console.log(`Day ${dayOfWeek} (${this.dayNames[dayOfWeek]}) is disabled`);
            return true;
        }
        
        // Check if this specific date is disabled
        if (this.options.disabledDates.includes(dateStr)) {
            console.log(`Date ${dateStr} is disabled`);
            return true;
        }
        
        return false;
    }



                createDayElement(day, isOtherMonth, date) {
                    const dayEl = document.createElement('div');
                    dayEl.className = 'CDSDashboardAppointmentEnhancedCalender-calendar-day';
                    
                    if (isOtherMonth) {
                        dayEl.classList.add('CDSDashboardAppointmentEnhancedCalender-other-month');
                    }
           if (this.isDateDisabled(date)) {
            dayEl.classList.add('CDSDashboardAppointmentEnhancedCalender-disabled');
            dayEl.style.pointerEvents = 'none';
            dayEl.title = 'This day is not available';
        } else {
            // Only add click handler for enabled days
            dayEl.addEventListener('click', () => {
                if (!isOtherMonth) {
                    this.selectDate(date);
                }
            });
        }
        
                    // Check if today
                    const today = new Date();
                    if (date.toDateString() === today.toDateString()) {
                        dayEl.classList.add('CDSDashboardAppointmentEnhancedCalender-today');
                    }
                    
                    // Check if selected
                    if (date.toDateString() === this.selectedDate.toDateString()) {
                        dayEl.classList.add('CDSDashboardAppointmentEnhancedCalender-selected');
                    }
                    
                    // Check if has events
                    const dateKey = this.formatDateKey(date);
                   
                    if (this.options.events[dateKey]) {
               const eventCount = this.options.events[dateKey].length;

    dayEl.classList.remove('CDSDashboardAppointmentEnhancedCalender-has-events');
    dayEl.classList.add('CDSDashboardAppointmentEnhancedCalender-event-text');

    const label = `${eventCount} ${eventCount === 1 ? 'Appointment' : 'Appointments'}`;

    const badge = document.createElement('span');
    badge.className = 'CDSDashboardAppointmentEnhancedCalender-event-label';
    badge.textContent = label;
    badge.style.color = '#1E3A8A';          // dark brown
    badge.style.fontSize = '0.825rem';
    badge.style.position = 'absolute';
    badge.style.bottom = '8px';
    badge.style.left = '50%';
    badge.style.transform = 'translateX(-50%)';
    badge.style.whiteSpace = 'nowrap';
    dayEl.style.position = 'relative';      // ensure absolute positioning works
    dayEl.appendChild(badge);
                    }
                    
                    const dayNumber = document.createElement('div');
                    dayNumber.className = 'CDSDashboardAppointmentEnhancedCalender-day-number';
                    dayNumber.textContent = day;
                    dayEl.appendChild(dayNumber);
                    
                    // Add click handler
                    dayEl.addEventListener('click', () => {
                        if (!isOtherMonth) {
                            this.selectDate(date);
                        }
                    });
                    
                    return dayEl;
                }

                formatDateKey(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                selectDate(date) {
                    this.selectedDate = date;
                    
                    // Update calendar selection
                    this.container.querySelectorAll('.CDSDashboardAppointmentEnhancedCalender-calendar-day').forEach(day => {
                        day.classList.remove('CDSDashboardAppointmentEnhancedCalender-selected');
                    });
                    
                    // Regenerate calendar to update selection
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                    
                    // Trigger callback
                    if (this.options.onDateSelect) {
                        this.options.onDateSelect(date);
                    }
                    
                    // Show agenda
                    this.showAgenda(date);
                }

                showAgenda(date) {
                    // Update agenda header
                    this.elements.agendaDate.textContent = date.toLocaleDateString(this.options.locale, { 
                        month: 'long', 
                        day: 'numeric', 
                        year: 'numeric' 
                    });
                    this.elements.agendaDay.textContent = this.dayNames[date.getDay()];
                    
                    // Update agenda content
                    const dateKey = this.formatDateKey(date);
                    const dayEvents = this.options.events[dateKey] || [];
                    
                    this.elements.agendaContent.innerHTML = '';
                    
                    if (dayEvents.length === 0) {
                        this.elements.agendaContent.innerHTML = `
                            <div class="CDSDashboardAppointmentEnhancedCalender-agenda-empty">
                                <div class="CDSDashboardAppointmentEnhancedCalender-empty-icon">📅</div>
                                <p>No events scheduled for this day</p>
                            </div>
                        `;
                    } else {
                        dayEvents.forEach((event, index) => {
                            const eventEl = document.createElement('div');
                            eventEl.className = 'CDSDashboardAppointmentEnhancedCalender-time-slot';
                            eventEl.style.animationDelay = `${index * 0.1}s`;
                            
                            eventEl.innerHTML = `
                                <div class="CDSDashboardAppointmentEnhancedCalender-event-time">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M8 0a8 8 0 100 16A8 8 0 008 0zM8 14a6 6 0 110-12 6 6 0 010 12zm.5-9.5v3.793l2.854 2.853a.5.5 0 01-.708.708l-3-3A.5.5 0 017.5 8V4.5a.5.5 0 011 0z"/>
                                    </svg>
                                    ${event.time}
                                </div>
                                <div class="CDSDashboardAppointmentEnhancedCalender-event-title">${event.title}</div>
                                <div class="CDSDashboardAppointmentEnhancedCalender-event-description">${event.description}</div>
                                ${event.attendees ? `
                                    <div class="CDSDashboardAppointmentEnhancedCalender-event-attendees">
                                        ${event.attendees.slice(0, 3).map(a => 
                                            `<div class="CDSDashboardAppointmentEnhancedCalender-attendee">${a}</div>`
                                        ).join('')}
                                        ${event.attendees.length > 3 ? 
                                            `<div class="CDSDashboardAppointmentEnhancedCalender-attendee">+${event.attendees.length - 3}</div>` : ''
                                        }
                                    </div>
                                ` : ''}
                            `;
                            
                            // Add click handler for events
                            eventEl.addEventListener('click', () => {
                                if (this.options.onEventClick) {
                                    this.options.onEventClick(event, date);
                                }
                            });
                            
                            this.elements.agendaContent.appendChild(eventEl);
                        });
                    }
                    
                    // Show overlay and panel
                    this.elements.overlay.classList.add('CDSDashboardAppointmentEnhancedCalender-active');
                    this.elements.agendaPanel.classList.add('CDSDashboardAppointmentEnhancedCalender-active');
                    document.body.style.overflow = 'hidden';
                }

                hideAgenda() {
                    this.elements.overlay.classList.remove('CDSDashboardAppointmentEnhancedCalender-active');
                    this.elements.agendaPanel.classList.remove('CDSDashboardAppointmentEnhancedCalender-active');
                    document.body.style.overflow = '';
                }

                navigateMonth(direction) {
                    this.currentDate.setMonth(this.currentDate.getMonth() + direction);
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                }

                goToToday() {
                    this.currentDate = new Date();
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                    this.selectDate(new Date());
                }

                // Public methods
                setEvents(events) {
                    this.options.events = events;
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                }

                addEvent(date, event) {
                    const dateKey = this.formatDateKey(date);
                    if (!this.options.events[dateKey]) {
                        this.options.events[dateKey] = [];
                    }
                    this.options.events[dateKey].push(event);
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                }

                removeEvent(date, eventIndex) {
                    const dateKey = this.formatDateKey(date);
                    if (this.options.events[dateKey]) {
                        this.options.events[dateKey].splice(eventIndex, 1);
                        this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                    }
                }

                getSelectedDate() {
                    return this.selectedDate;
                }

                setSelectedDate(date) {
                    this.selectedDate = new Date(date);
                    this.currentDate = new Date(date);
                    this.generateCalendar(this.currentDate.getFullYear(), this.currentDate.getMonth());
                }
                getCurrentDate() {
                    return this.currentDate; 
                }

                destroy() {
                    // Remove event listeners
                    document.removeEventListener('keydown', this.handleKeydown);
                    
                    // Clear container
                    this.container.innerHTML = '';
                    
                    // Remove styles if no other instances exist
                    const otherInstances = document.querySelectorAll('.CDSDashboardAppointmentEnhancedCalender-app-container');
                    if (otherInstances.length === 0) {
                        const styleSheet = document.getElementById('enhanced-calendar-styles');
                        if (styleSheet) {
                            styleSheet.remove();
                        }
                    }
                }
            }

            // Export to global scope
            window.EnhancedCalendar = EnhancedCalendar;

        })(window);