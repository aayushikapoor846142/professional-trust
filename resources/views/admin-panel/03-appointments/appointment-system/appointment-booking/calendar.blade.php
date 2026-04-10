

<div class="container">
    <div class="cds-ty-dashboard-box">
        <div class="cds-register-address-list cds-markLeaves">
            <div class="p-3">
                <div class="cdsTYMainsite-login-form-container-header p-0">
                    <span>Filter By Appointment Location</span>
                </div>
                <div class="dropdown">
                    <a class="CdsTYButton-btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Filter Location</a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="js-form-message">
                                <div class="address-item appointment-location" data-mode="">
                                    <div class="address-header">
                                        <div class="map-thumbnail">
                                            <i class="fa-sharp fa-solid fa-location-dot" style="color: #000000;"></i>
                                        </div>
                                        <div class="address-details render-address">
                                            <!-- <span class="badge main-house">
                                    </span> -->
                                            <div class="address-name">
                                                <div class="company-name" data-value="All">All Locations</div>
                                            </div>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="location_id" onclick="window.setLocation(this.value)" value="" checked />
                                        </div>
                                    </div>
                                </div>
                                @foreach($companyLocations as $key => $record)
                                <!-- Address Item 1 -->
                                <div class="address-item appointment-location" data-mode="{{ $record->type }}" id="personal-address-div-{{$record->id}}">
                                    <div class="address-header">
                                        <div class="map-thumbnail">
                                            <i class="fa-sharp fa-solid fa-location-dot" style="color: #000000;"></i>
                                        </div>
                                        <div class="address-details render-address">
                                            <!-- <span class="badge main-house">
                        </span> -->
                                            <div class="address-name">
                                                <div class="company-name" data-value="{{$record->company->company_name ?? ''}}">{{$record->company->company_name ?? ''}}</div>
                                            </div>

                                            <div class="address-text">
                                                <div class="address-1" data-value="{{$record->address_1 ?? ''}}">{{$record->address_1 ?? ''}}</div>
                                                <div class="address-2" data-value="{{$record->address_2 ?? ''}}">{{$record->address_2 ?? ''}}</div>
                                            </div>
                                            <div class="address-text">
                                                <div class="state" data-value="{{$record->state ?? ''}}">{{$record->state ?? ''}}</div>
                                                <div class="city" data-value="{{$record->city ?? ''}}">{{$record->city ?? ''}}</div>

                                                <div class="pincode" data-value="{{$record->pincode ?? ''}}">{{$record->pincode ?? ''}}</div>
                                                <div class="country" data-value="{{$record->country ?? ''}}">{{$record->country ?? ''}}</div>
                                            </div>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="location_id" value="{{$record->id}}" onclick="window.setLocation(this.value)" />
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Advanced Features -->
    <div class="demo-section">
        <div id="advanced-calendar" class="calendar-wrapper"></div>
    </div>
</div>

<link href="{{ url('assets/css/28-cds-enhance-calendar.css') }}" rel="stylesheet" />
<script src="{{url('assets/js/cds-enhance-calendar.js')}}"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
let currentLocationId = '';
  window.setLocation = function(locationId = '') {
        currentLocationId = locationId;
        // Reload appointments when location changes
        const today = new Date();
        loadMonthAppointments(today.getMonth() + 1, today.getFullYear());
    }

    // Initialize calendar with empty events
    const calendarConfig = {
        events: {},
        startDate: new Date(),
        title: '{{$pageTitle}}',
        onDateSelect: (date) => {
           if (!this.dateSelectDebounce) {
        this.dateSelectDebounce = setTimeout(() => {
            loadDayAppointments(date);
            this.dateSelectDebounce = null;
        }, 100);
    }
        },
        onEventClick: (event, date) => {
            showAppointmentDetails(event);
        }
    };

    const appointmentCalendar = new EnhancedCalendar('#advanced-calendar', calendarConfig);
    // Initial load
    const today = new Date();
    loadMonthAppointments(today.getMonth() + 1, today.getFullYear());

    // Wait for calendar render, then bind navigation events
    setTimeout(() => {
        const prevBtn = document.querySelector('[id$="prev-month"]');
        const nextBtn = document.querySelector('[id$="next-month"]');

        [prevBtn, nextBtn].forEach(button => {
            if (button) {
                button.addEventListener('click', () => {
                    // Delay to allow calendar to update current month/year
                    setTimeout(() => {
                        const current = appointmentCalendar.getCurrentDate();
                        loadMonthAppointments(current.getMonth() + 1, current.getFullYear());
                    }, 10);
                });
            }
        });
    }, 100); // Allow time for calendar to render buttons

    // Refresh button handler
    document.getElementById('refresh-appointments').addEventListener('click', function () {
        const currentDate = appointmentCalendar.getCurrentDate();
        loadMonthAppointments(currentDate.getMonth() + 1, currentDate.getFullYear());
    });
    
    
    // Load initial appointments
    loadMonthAppointments(new Date().getMonth() + 1, new Date().getFullYear());

    // Refresh button handler
    document.getElementById('refresh-appointments').addEventListener('click', function() {
        const currentDate = appointmentCalendar.getCurrentDate();
        loadMonthAppointments(currentDate.getMonth() + 1, currentDate.getFullYear());
    });

    // Function to load appointments for a specific month
  function loadMonthAppointments(month, year) {
    // Show loading state
    const container = document.getElementById('appointment-list-container');
    if (container) {
        container.innerHTML = `
            <div class="CDSDashboardAppointmentEnhancedCalendar-loading">
                <div class="CDSDashboardAppointmentEnhancedCalendar-spinner"></div>
                <p>Loading appointments...</p>
            </div>
        `;
    }

    fetch("{{ baseUrl('appointments/appointment-booking/fetch-appointments') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            month: month,
            year: year,
            selectedDay: 0,
        location_id: currentLocationId, 
        professional_id:"{{ auth()->user()->id }}"
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        const formattedEvents = {};
        
        Object.entries(data.events).forEach(([monthIndex, monthEvents]) => {
            monthEvents.forEach(event => {
                const dateStr = `${year}-${String(Number(monthIndex) + 1).padStart(2, '0')}-${String(event.day).padStart(2, '0')}`;
                console.log('Adding event for', dateStr);
                
                if (!formattedEvents[dateStr]) {
                    formattedEvents[dateStr] = [];
                }
                
                formattedEvents[dateStr].push({
                    time: event.time,
                    title: event.title,
                    description: event.description,
                    rawData: event
                });
            });
        });
        
        appointmentCalendar.setEvents(formattedEvents);
        
        // Update the events list if HTML is returned
        if (data.html && container) {
            container.innerHTML = data.html;
        }
        
        showToast('Appointments loaded successfully', 'success');
    })
    .catch(error => {
        console.error('Error loading appointments:', error);
        showToast('Error loading appointments', 'error');
        
        if (container) {
            container.innerHTML = `
                <div class="CDSDashboardAppointmentEnhancedCalendar-agenda-empty">
                    <div class="CDSDashboardAppointmentEnhancedCalendar-empty-icon">⚠️</div>
                    <p>Failed to load appointments</p>
                    <button onclick="loadMonthAppointments(${month}, ${year})" 
                        class="CDSDashboardAppointmentEnhancedCalendar-retry-btn">
                        Retry
                    </button>
                </div>
            `;
        }
    });
}
    // Function to load appointments for a specific day
function loadDayAppointments(date) {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();
    
    // Show loading state
    const agendaContent = document.getElementById(
        appointmentCalendar.getUniqueId('agenda-content')
    );
    if (agendaContent) {
        agendaContent.innerHTML = `
            <div class="CDSDashboardAppointmentEnhancedCalendar-loading">
                <div class="CDSDashboardAppointmentEnhancedCalendar-spinner"></div>
                <p>Loading day's appointments...</p>
            </div>
        `;
    }
    
    // Format the date for display
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = date.toLocaleDateString('en-US', options);
    
    // Update the date display
    const agendaDate = document.getElementById(appointmentCalendar.getUniqueId('agenda-date'));
    const agendaDay = document.getElementById(appointmentCalendar.getUniqueId('agenda-day'));
    
    if (agendaDate) agendaDate.textContent = formattedDate.split(',')[0];
    if (agendaDay) agendaDay.textContent = formattedDate.split(',')[1].trim();
    
    fetch("{{ baseUrl('appointments/appointment-booking/fetch-appointments') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            month: month,
            year: year,
            selectedDay: day,
           location_id: currentLocationId, 
        professional_id:"{{ auth()->user()->id }}"
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (!agendaContent) return;
        
        if (data.html) {
            // Clear existing content
            agendaContent.innerHTML = '';
            
            // Create a container for the events
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'CDSDashboardAppointmentEnhancedCalendar-custom-events';
            
            // Add the server-rendered HTML
            eventsContainer.innerHTML = data.html;
            
            // Convert event-item elements to match calendar styling
            const eventItems = eventsContainer.querySelectorAll('.event-item');
            eventItems.forEach((item, index) => {
                item.classList.add('CDSDashboardAppointmentEnhancedCalendar-time-slot');
                item.style.animationDelay = `${index * 0.1}s`;
                
                // Add click handler if needed
                item.addEventListener('click', () => {
                    if (appointmentCalendar.options.onEventClick) {
                        const eventData = {
                            time: item.querySelector('strong')?.textContent,
                            title: item.textContent.replace(item.querySelector('strong')?.textContent, '').trim(),
                            rawData: data.events?.[month]?.[index]
                        };
                        appointmentCalendar.options.onEventClick(eventData, date);
                    }
                });
            });
            
            agendaContent.appendChild(eventsContainer);
            showToast('Day appointments loaded', 'success');
        } else {
            // Show empty state
            agendaContent.innerHTML = `
                <div class="CDSDashboardAppointmentEnhancedCalendar-agenda-empty">
                    <div class="CDSDashboardAppointmentEnhancedCalendar-empty-icon">📅</div>
                    <p>No events scheduled for this day</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading day appointments:', error);
        showToast('Error loading day appointments', 'error');
        
        if (agendaContent) {
            agendaContent.innerHTML = `
                <div class="CDSDashboardAppointmentEnhancedCalendar-agenda-empty">
                    <div class="CDSDashboardAppointmentEnhancedCalendar-empty-icon">⚠️</div>
                    <p>Error loading events</p>
                    <button onclick="loadDayAppointments(new Date(${date.getTime()}))" 
                        class="CDSDashboardAppointmentEnhancedCalendar-retry-btn">
                        Retry
                    </button>
                </div>
            `;
        }
    });
}

    // Function to show appointment details in a modal
    function showAppointmentDetails(appointment) {
        // You can implement a modal here or use your existing modal system
        console.log('Appointment details:', appointment.rawData);
        // Example: showModal(appointment.rawData);
    }

    // Helper function to show toast notifications
    function showToast(message, type = 'success') {
        // Implement your toast notification system here
        console.log(`${type}: ${message}`);
    }
});

</script>

