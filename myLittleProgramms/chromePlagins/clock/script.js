const deg = 6;
const hr = document.querySelector('#hr');
const sc = document.querySelector('#sc');

setInterval(() => {
    let day = new Date();
    let hh = day.getHours() * 30;
    let mm = day.getMinutes() * deg;
    let ss = day.getSeconds() * deg;

    hr.style.transform = `rotateZ(${(hh) + (mm / 12)}deg)`;
    mn.style.transform = `rotateZ(${mm}deg)`;
    sc.style.transform = `rotateZ(${ss}deg)`;

    const weekDaysName = {
        0: "Воскресенье",
        1: "Понедельник",
        2: "Вторник",
        3: "Среда",
        4: "Четверг",
        5: "Пятница",
        6: "Суббота",
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

    // ${day.getHours()}:${day.getMinutes()}:${day.getSeconds()}
    document.querySelector('.date').innerText = `${weekDaysName[day.getDay()]}, ${day.getDate()} ${monthName[day.getMonth()]}(${day.getMonth() + 1}) ${day.getFullYear()} `;
})