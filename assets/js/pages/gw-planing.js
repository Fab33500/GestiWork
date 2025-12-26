(function () {
    if (typeof FullCalendar === 'undefined') {
        return;
    }

    const calendarEl = document.getElementById('gw_planing_calendar');
    if (!calendarEl) {
        return;
    }

    const viewButtons = document.querySelectorAll('.gw-planing-view-button');
    const labelEl = document.getElementById('gw_planing_label');
    const monthPickerMonth = document.getElementById('gw_planing_month_picker_month');
    const monthPickerYear = document.getElementById('gw_planing_month_picker_year');
    const navButtons = document.querySelectorAll('[data-gw-planing-nav]');
    const yearNavButtons = document.querySelectorAll('[data-gw-planing-only-year="1"]');
    const yearViewEl = document.getElementById('gw_planing_year_view');
    const calendarWrapper = document.getElementById('gw_planing_calendar');
    const monthPickerWrapper = document.getElementById('gw_planing_month_picker_wrapper');
    const weekNavWrapper = document.getElementById('gw_planing_week_nav');
    const weekInfoEl = document.getElementById('gw_planing_week_info');
    const weekDayEl = document.getElementById('gw_planing_week_day');
    const weekNavButtons = document.querySelectorAll('[data-gw-planing-week-nav]');
    const semesterNav = document.getElementById('gw_planing_semester_nav');
    let activeView = 'year';
    let selectedDate = new Date();
    let yearViewStartDate = getSemesterStart(selectedDate);
    const mobileWeekMediaQuery = window.matchMedia('(max-width: 1024px)');

    const sampleEvents = [
        {
            title: 'Management - Session inter',
            start: '2026-01-15',
            end: '2026-01-17',
            extendedProps: { label: 'INTER' },
            classNames: ['gw-planing-event', 'is-inter'],
        },
        {
            title: 'Qualiopi - Session intra',
            start: '2026-02-04',
            extendedProps: { label: 'INTRA' },
            classNames: ['gw-planing-event', 'is-intra'],
        },
        {
            title: 'Formation RGPD (distanciel)',
            start: '2026-01-29',
            extendedProps: { label: 'DIST' },
            classNames: ['gw-planing-event', 'is-dist'],
        },
        {
            title: 'CPF - Bureautique',
            start: '2026-03-11',
            end: '2026-03-12',
            extendedProps: { label: 'CPF' },
            classNames: ['gw-planing-event', 'is-cpf'],
        },
        {
            title: 'Session à confirmer',
            start: '2026-03-25',
            extendedProps: { label: 'N/A' },
            classNames: ['gw-planing-event', 'is-na'],
        },
    ];

    const holidayCache = {};

    const planningYearMin = Number(monthPickerYear?.dataset?.gwPlaningYearMin ?? '2025');
    const planningYearMax = Number(monthPickerYear?.dataset?.gwPlaningYearMax ?? '2050');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        views: {
            dayGridMonth: {},
            dayGridWeek: {},
            dayGridDay: {},
        },
        validRange: {
            start: `${planningYearMin}-01-01`,
            end: `${planningYearMax}-12-31`,
        },
        height: 'auto',
        locale: 'fr',
        firstDay: 1,
        headerToolbar: {
            left: '',
            center: '',
            right: '',
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
        },
        events: sampleEvents,
        datesSet: function (info) {
            applyMobileWeekLayout(info.view.type);
        },
        eventDidMount: function (info) {
            if (!info.el || !info.event.extendedProps.label) {
                return;
            }

            const badge = document.createElement('span');
            badge.className = 'gw-planing-event-badge';
            badge.textContent = info.event.extendedProps.label;
            info.el.querySelector('.fc-event-title').prepend(badge);
        },
        dayCellDidMount: function (info) {
            const date = info.date instanceof Date ? info.date : null;
            if (!date) {
                return;
            }
            if (isWeekend(date)) {
                info.el.classList.add('gw-planing-cell--weekend');
            }
            if (isPublicHoliday(date)) {
                info.el.classList.add('gw-planing-cell--holiday');
            }
        },
        dayHeaderDidMount: function (info) {
            const date = info.date instanceof Date ? info.date : null;
            if (!date) {
                return;
            }
            if (isWeekend(date)) {
                info.el.classList.add('gw-planing-header--weekend');
            }
            if (isPublicHoliday(date)) {
                info.el.classList.add('gw-planing-header--holiday');
            }
        },
    });

    function updateLabel(view) {
        if (!labelEl) {
            return;
        }

        const currentDate = calendar.getDate();
        const formatter = new Intl.DateTimeFormat('fr-FR', {
            month: 'long',
            year: 'numeric',
        });

        if (view === 'year') {
            labelEl.textContent = formatMonthLabel(selectedDate);
            syncMonthPicker(selectedDate);
        } else if (view === 'dayGridWeek') {
            selectedDate = new Date(currentDate);
            labelEl.textContent = formatWeekInfo(currentDate);
        } else if (view === 'dayGridDay') {
            selectedDate = new Date(currentDate);
            labelEl.textContent = currentDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        } else {
            selectedDate = new Date(currentDate);
            labelEl.textContent = formatter.format(currentDate);
        }

        if (monthPickerWrapper) {
            if (view === 'dayGridMonth') {
                monthPickerWrapper.classList.remove('is-hidden');
            } else {
                monthPickerWrapper.classList.add('is-hidden');
            }
            if (view !== 'year') {
                syncMonthPicker(currentDate);
            }
        }

        updateWeekControls();
    }

    calendar.render();
    if (activeView === 'year') {
        calendarWrapper?.setAttribute('hidden', 'hidden');
        yearViewEl?.removeAttribute('hidden');
        renderYearView(yearViewStartDate);
        updateLabel('year');
    } else {
        updateLabel(calendar.view.type);
    }
    updateNavButtonsVisibility(activeView);
    applyMobileWeekLayout(calendar.view.type);
    mobileWeekMediaQuery.addEventListener('change', function () {
        applyMobileWeekLayout(calendar.view.type);
    });

    viewButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const viewName = button.dataset.gwPlaningView;
            if (!viewName) {
                return;
            }

            activeView = viewName;
            viewButtons.forEach((btn) => btn.classList.remove('gw-planing-view-button--active'));
            button.classList.add('gw-planing-view-button--active');

            if (viewName === 'year') {
                calendarWrapper?.setAttribute('hidden', 'hidden');
                yearViewEl?.removeAttribute('hidden');
                selectedDate = new Date(calendar.getDate());
                yearViewStartDate = getSemesterStart(selectedDate);
                renderYearView(yearViewStartDate, selectedDate);
                updateLabel('year');
                updateNavButtonsVisibility('year');
                return;
            }

            yearViewEl?.setAttribute('hidden', 'hidden');
            calendarWrapper?.removeAttribute('hidden');


            calendar.changeView(viewName);
            updateLabel(viewName);
            updateNavButtonsVisibility(viewName);
            updateWeekControls();
        });
    });

    navButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const currentView = activeView === 'year' ? 'year' : calendar.view.type;
            const action = button.dataset.gwPlaningNav;

            if (currentView === 'year') {
                shiftSemester(action === 'prev' ? -1 : 1);
                renderYearView(yearViewStartDate, selectedDate);
                updateLabel('year');
                return;
            }
        });
    });

    weekNavButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const currentType = calendar.view.type;
            if (currentType !== 'dayGridWeek' && currentType !== 'dayGridDay') {
                return;
            }
            const direction = button.dataset.gwPlaningWeekNav === 'prev' ? -1 : 1;
            const targetDate = new Date(calendar.getDate());
            targetDate.setDate(targetDate.getDate() + direction);
            calendar.gotoDate(targetDate);
            updateLabel(currentType);
            updateWeekControls();
            applyMobileWeekLayout(currentType);
        });
    });

    function syncMonthPicker(date) {
        if (!monthPickerMonth || !monthPickerYear) {
            return;
        }
        monthPickerMonth.value = String(date.getMonth());
        monthPickerYear.value = String(date.getFullYear());
    }

    if (monthPickerMonth) {
        monthPickerMonth.addEventListener('change', handleMonthPickerChange);
    }

    if (monthPickerYear) {
        monthPickerYear.addEventListener('change', handleMonthPickerChange);
    }

    function handleMonthPickerChange() {
        if (!monthPickerMonth || !monthPickerYear) {
            return;
        }
        const monthValue = Number(monthPickerMonth.value);
        const yearValue = Number(monthPickerYear.value);
        if (Number.isNaN(monthValue) || Number.isNaN(yearValue)) {
            return;
        }

        const clampedYear = Math.min(Math.max(yearValue, planningYearMin), planningYearMax);
        const targetDate = new Date(Date.UTC(clampedYear, monthValue, 1));

        if (activeView === 'year') {
            selectedDate = new Date(targetDate);
            yearViewStartDate = getSemesterStart(selectedDate);
            renderYearView(yearViewStartDate, selectedDate);
            updateLabel('year');
            return;
        }

        calendar.gotoDate(targetDate);
        updateLabel(calendar.view.type);
        applyMobileWeekLayout(calendar.view.type);
    }

    function renderYearView(startDate, highlightDate) {
        if (!yearViewEl) {
            return;
        }

        yearViewEl.innerHTML = '';
        const months = [];

        for (let i = 0; i < 6; i += 1) {
            const monthDate = new Date(startDate);
            monthDate.setMonth(startDate.getMonth() + i);
            months.push(monthDate);
        }

        const highlight = highlightDate ? new Date(highlightDate) : new Date();
        const highlightYear = highlight.getFullYear();
        const highlightMonth = highlight.getMonth();

        months.forEach((monthDate) => {
            const column = document.createElement('div');
            column.className = 'gw-planing-year-column';
            if (monthDate.getFullYear() === highlightYear && monthDate.getMonth() === highlightMonth) {
                column.classList.add('is-current-month');
            }

            const header = document.createElement('div');
            header.className = 'gw-planing-year-column__header';
            header.textContent = `${monthDate.toLocaleDateString('fr-FR', { month: 'long' }).toUpperCase()} ${monthDate.getFullYear()}`;
            column.appendChild(header);

            const daysContainer = document.createElement('div');
            daysContainer.className = 'gw-planing-year-days';

            const days = getDaysOfMonth(monthDate);
            days.forEach((dayDate) => {
                const dayRow = document.createElement('div');
                dayRow.className = 'gw-planing-year-day';
                if (isWeekend(dayDate)) {
                    dayRow.classList.add('is-weekend');
                }

                const label = document.createElement('span');
                label.className = 'gw-planing-year-day__label';
                label.innerHTML = `<strong>${dayDate.toLocaleDateString('fr-FR', { weekday: 'short' }).toUpperCase()}</strong> ${dayDate.getDate()}`;
                if (isPublicHoliday(dayDate)) {
                    label.classList.add('is-holiday');
                }
                dayRow.appendChild(label);

                const dotsContainer = document.createElement('div');
                dotsContainer.className = 'gw-planing-year-day__dots';

                const eventsForDay = getEventsForDate(dayDate);
                if (eventsForDay.length === 0) {
                    dotsContainer.innerHTML = '&nbsp;';
                } else {
                    eventsForDay.slice(0, 3).forEach((event) => {
                        const dot = document.createElement('span');
                        dot.className = `gw-planing-year-dot ${event.classNames?.join(' ') || ''}`;
                        dot.title = event.title;
                        dotsContainer.appendChild(dot);
                    });
                }

                dayRow.appendChild(dotsContainer);
                daysContainer.appendChild(dayRow);
            });

            column.appendChild(daysContainer);
            yearViewEl.appendChild(column);
        });
    }

    function getDaysOfMonth(monthDate) {
        const days = [];
        const current = new Date(monthDate.getFullYear(), monthDate.getMonth(), 1);
        while (current.getMonth() === monthDate.getMonth()) {
            days.push(new Date(current));
            current.setDate(current.getDate() + 1);
        }
        return days;
    }

    function getEventsForDate(date) {
        const dateISO = date.toISOString().split('T')[0];
        return sampleEvents.filter((event) => {
            const start = event.start;
            const end = event.end || start;
            return dateISO >= start && dateISO <= end;
        });
    }

    function shiftDate(date, value, unit) {
        const newDate = new Date(date);
        switch (unit) {
            case 'week':
                newDate.setDate(newDate.getDate() + value * 7);
                break;
            case 'day':
                newDate.setDate(newDate.getDate() + value);
                break;
            default:
                newDate.setMonth(newDate.getMonth() + value);
        }
        return newDate;
    }

    function getSemesterStart(date) {
        const semesterIndex = Math.floor(date.getMonth() / 6);
        return new Date(date.getFullYear(), semesterIndex * 6, 1);
    }

    function shiftSemester(direction) {
        const delta = direction * 6;
        const tentative = shiftDate(yearViewStartDate, delta, 'month');
        const targetYear = selectedDate.getFullYear();
        if (tentative.getFullYear() !== targetYear) {
            yearViewStartDate = new Date(targetYear, direction > 0 ? 6 : 0, 1);
            return;
        }
        yearViewStartDate = tentative;
    }

    function applyMobileWeekLayout(viewType) {
        if (!calendarEl) {
            return;
        }
        cleanupMobileWeekHeaders();
        if (viewType !== 'dayGridWeek' || !mobileWeekMediaQuery.matches) {
            return;
        }
        const headerCells = calendarEl.querySelectorAll('.fc-col-header-cell .fc-col-header-cell-cushion');
        const headerTexts = Array.from(headerCells, (cell) => cell.textContent.trim());
        const dayCells = calendarEl.querySelectorAll('.fc-daygrid-body tr:first-child td');
        dayCells.forEach((cell, index) => {
            const dayFrame = cell.querySelector('.fc-daygrid-day-frame');
            if (!dayFrame) {
                return;
            }
            const header = document.createElement('div');
            header.className = 'gw-planing-mobile-day-header';
            header.textContent = headerTexts[index] || '';
            dayFrame.prepend(header);
        });
    }

    function cleanupMobileWeekHeaders() {
        calendarEl?.querySelectorAll('.gw-planing-mobile-day-header').forEach((header) => header.remove());
    }

    function formatMonthLabel(date) {
        return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
    }

    function updateNavButtonsVisibility(view) {
        const shouldShow = view === 'year';
        yearNavButtons.forEach((button) => {
            if (shouldShow) {
                button.removeAttribute('hidden');
            } else {
                button.setAttribute('hidden', 'hidden');
            }
        });
        if (semesterNav) {
            if (shouldShow) {
                semesterNav.removeAttribute('hidden');
            } else {
                semesterNav.setAttribute('hidden', 'hidden');
            }
        }
        toggleWeekControls(view);
    }

    function isWeekend(date) {
        const day = date.getDay();
        return day === 0 || day === 6;
    }

    function isPublicHoliday(date) {
        const year = date.getFullYear();
        if (!holidayCache[year]) {
            holidayCache[year] = getFrenchPublicHolidays(year);
        }
        const iso = formatLocalDate(date);
        return holidayCache[year].has(iso);
    }

    function getFrenchPublicHolidays(year) {
        const pad = (value) => String(value).padStart(2, '0');
        const format = (month, day) => `${year}-${pad(month + 1)}-${pad(day)}`;
        const holidays = new Set([
            format(0, 1),
            format(4, 1),
            format(4, 8),
            format(6, 14),
            format(7, 15),
            format(10, 1),
            format(10, 11),
            format(11, 25),
        ]);

        const easter = calculateEasterSunday(year);
        const pushDate = (date, offset) => {
            const copy = new Date(date);
            copy.setDate(copy.getDate() + offset);
            holidays.add(formatLocalDate(copy));
        };

        pushDate(easter, 1);
        pushDate(easter, 39);
        pushDate(easter, 50);

        return holidays;
    }

    function calculateEasterSunday(year) {
        const a = year % 19;
        const b = Math.floor(year / 100);
        const c = year % 100;
        const d = Math.floor(b / 4);
        const e = b % 4;
        const f = Math.floor((b + 8) / 25);
        const g = Math.floor((b - f + 1) / 3);
        const h = (19 * a + b - d - g + 15) % 30;
        const i = Math.floor(c / 4);
        const k = c % 4;
        const l = (32 + 2 * e + 2 * i - h - k) % 7;
        const m = Math.floor((a + 11 * h + 22 * l) / 451);
        const month = Math.floor((h + l - 7 * m + 114) / 31) - 1;
        const day = ((h + l - 7 * m + 114) % 31) + 1;
        return new Date(year, month, day);
    }

    function formatLocalDate(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    function updateWeekControls() {
        if (!weekNavWrapper) {
            return;
        }
        toggleWeekControls(calendar.view.type);
        if (weekNavWrapper.hasAttribute('hidden')) {
            return;
        }
        const referenceDate = calendar.getDate();
        if (weekInfoEl) {
            weekInfoEl.textContent = formatWeekInfo(referenceDate);
        }
        if (weekDayEl) {
            weekDayEl.textContent = referenceDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    }

    function toggleWeekControls(view) {
        if (!weekNavWrapper) {
            return;
        }
        if (view === 'dayGridWeek' || view === 'dayGridDay') {
            weekNavWrapper.removeAttribute('hidden');
        } else {
            weekNavWrapper.setAttribute('hidden', 'hidden');
        }
    }

    function formatWeekInfo(date) {
        const weekNumber = getWeekNumber(date);
        const monthYear = capitalizeFirst(date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' }));
        return `${monthYear} - S ${String(weekNumber).padStart(2, '0')}`;
    }

    function getWeekNumber(date) {
        const target = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        const dayNumber = target.getUTCDay() || 7;
        target.setUTCDate(target.getUTCDate() + 4 - dayNumber);
        const yearStart = new Date(Date.UTC(target.getUTCFullYear(), 0, 1));
        return Math.ceil(((target - yearStart) / 86400000 + 1) / 7);
    }

    function capitalizeFirst(text) {
        if (!text) {
            return '';
        }
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

})();
