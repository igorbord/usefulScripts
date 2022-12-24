window.addEventListener('DOMContentLoaded', () => {

    const deg = 6;
    const hr = document.querySelector('#hr');
    const sc = document.querySelector('#sc');

    showClock()
    setInterval(showClock, 1000)

    function showClock() {
        let day = new Date();
        let hh = day.getHours() * 30;
        let mm = day.getMinutes() * deg;
        let ss = day.getSeconds() * deg;

        hr.style.transform = `rotateZ(${(hh) + (mm / 12)}deg)`;
        mn.style.transform = `rotateZ(${mm}deg)`;
        sc.style.transform = `rotateZ(${ss}deg)`;
    }

    let date = new Date();
    const weekDaysName = {
        0: "Вс",
        1: "Пн",
        2: "Вт",
        3: "Ср",
        4: "Чт",
        5: "Пт",
        6: "Сб",
    }
    const monthName = {
        0: "Января",
        1: "Февраля",
        2: "Марта",
        3: "Апреля",
        4: "Мая",
        5: "Июня",
        6: "Июля",
        7: "Августа",
        8: "Сентября",
        9: "Октября",
        10: "Ноября",
        11: "Декабря",
    }

    document.querySelector('.date').innerText = `${weekDaysName[date.getDay()]}, ${date.getDate()} ${monthName[date.getMonth()]}(${date.getMonth() + 1}) ${date.getFullYear()} `;

    const calendar = document.querySelector('.calendar .table')
    let row = document.createElement('div');
    row.classList.add('row')
    for (let c = 1; c <= 7; c++) {
        let dayNum = c == 7 ? weekDaysName[0] : weekDaysName[c]
        row.insertAdjacentHTML('beforeend', `<div class="col day-name">${dayNum}</div>`)
    }
    calendar.insertAdjacentElement('beforeend', row)

    let flag = false
    let calendarDay = new Date(date.getFullYear(), date.getMonth(), 1);
    let prevDay = calendarDay.getDate()
    for (let r = 0; r < 6; r++) {
        row = document.createElement('div');
        row.classList.add('row')
        for (let c = 1; c <= 7; c++) {
            if (!flag) {
                let weekDay = calendarDay.getDay() == 0 ? 6 : calendarDay.getDay()
                if (weekDay == c) {
                    flag = true
                }
            }

            if (flag && calendarDay.getDate() >= prevDay) {
                let active = calendarDay.getDate() == date.getDate() ? 'active' : ''
                let holiday = calendarDay.getDay() == 0 || calendarDay.getDay() == 6 ? 'holiday' : ''
                row.insertAdjacentHTML('beforeend', `<div class="col ${active} ${holiday}">${calendarDay.getDate()}</div>`)
                prevDay = calendarDay.getDate()
                calendarDay.setTime(calendarDay.getTime() + 86400000)
            } else {
                row.insertAdjacentHTML('beforeend', `<div class="col"></div>`)
            }
        }
        calendar.insertAdjacentElement('beforeend', row)
        if (calendarDay.getDate() < prevDay) break;
    }
})