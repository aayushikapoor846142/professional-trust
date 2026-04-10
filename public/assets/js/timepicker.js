class TimePicker {
    constructor(inputSelector, options = {}) {
        this.input = typeof inputSelector === 'string' ? document.querySelector(inputSelector) : inputSelector;
        this.options = {
            format: options.format || '24hr',
            stepMinutes: options.stepMinutes || 5,
            minTime: options.minTime || '00:00',
            maxTime: options.maxTime || '23:59',
            defaultTime: options.defaultTime || null,
            onSelect: options.onSelect || null,
        };
        this.dropdown = null;

        this.init();
    }

    init() {
        this.createDropdown();
        this.bindInputEvents();
        if (this.options.defaultTime) {
            this.input.value = this.formatTime(this.options.defaultTime);
        }
    }

    bindInputEvents() {
        this.input.addEventListener('focus', () => this.showDropdown());
        document.addEventListener('click', (e) => {
            if (!this.dropdown.contains(e.target) && e.target !== this.input) {
                this.hideDropdown();
            }
        });
    }

    showDropdown() {
        this.dropdown.innerHTML = '';
        this.generateTimeOptions();
        this.dropdown.style.display = 'block';
    }

    hideDropdown() {
        this.dropdown.style.display = 'none';
    }

    createDropdown() {
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'tp-dropdown';
        this.dropdown.style.display = 'none';
        this.input.parentNode.insertBefore(this.dropdown, this.input.nextSibling);
    }

    generateTimeOptions() {
        const times = this.getTimeSlots();
        times.forEach(time => {
            const option = document.createElement('div');
            option.className = 'tp-option';
            option.textContent = this.formatTime(time);
            option.dataset.value = time;

            option.addEventListener('click', () => {
                this.input.value = this.formatTime(time);
                this.hideDropdown();
                if (typeof this.options.onSelect === 'function') {
                    this.options.onSelect(time);
                }
            });

            this.dropdown.appendChild(option);
        });
    }

    getTimeSlots() {
        const result = [];
        const step = this.options.stepMinutes;
        const [minH, minM] = this.options.minTime.split(':').map(Number);
        const [maxH, maxM] = this.options.maxTime.split(':').map(Number);

        let minutes = minH * 60 + minM;
        const endMinutes = maxH * 60 + maxM;

        while (minutes <= endMinutes) {
            const h = Math.floor(minutes / 60).toString().padStart(2, '0');
            const m = (minutes % 60).toString().padStart(2, '0');
            result.push(`${h}:${m}`);
            minutes += step;
        }

        return result;
    }

    formatTime(timeStr) {
        if (this.options.format === '24hr') return timeStr;

        const [h, m] = timeStr.split(':').map(Number);
        const period = h >= 12 ? 'PM' : 'AM';
        const hour = h % 12 === 0 ? 12 : h % 12;
        return `${hour.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${period}`;
    }
}