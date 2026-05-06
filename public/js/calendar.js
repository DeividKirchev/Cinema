document.addEventListener('DOMContentLoaded', () => {
    const calendarTrack = document.getElementById('calendar-track');
    const btnPrev = document.getElementById('cal-prev');
    const btnNext = document.getElementById('cal-next');

    if (!calendarTrack) return;

    const days = 30;
    const months = ['ЯНУ', 'ФЕВ', 'МАР', 'АПР', 'МАЙ', 'ЮНИ', 'ЮЛИ', 'АВГ', 'СЕП', 'ОКТ', 'НОЕ', 'ДЕК'];
    const weekdays = ['НЕДЕЛЯ', 'ПОНЕДЕЛНИК', 'ВТОРНИК', 'СРЯДА', 'ЧЕТВЪРТЪК', 'ПЕТЪК', 'СЪБОТА'];

    const today = new Date();
    
    for (let i = 0; i < days; i++) {
        const date = new Date();
        date.setDate(today.getDate() + i);

        const dateCard = document.createElement('div');
        dateCard.className = `date-card ${i === 0 ? 'active' : ''}`;
        
        dateCard.innerHTML = `
            <span class="date-month">${months[date.getMonth()]}</span>
            <span class="date-day">${date.getDate()}</span>
            <span class="date-weekday">${weekdays[date.getDay()]}</span>
        `;

        dateCard.addEventListener('click', () => {
            document.querySelectorAll('.date-card').forEach(c => c.classList.remove('active'));
            dateCard.classList.add('active');
        });

        calendarTrack.appendChild(dateCard);
    }

    btnNext.addEventListener('click', () => {
        calendarTrack.scrollLeft += 300;
    });

    btnPrev.addEventListener('click', () => {
        calendarTrack.scrollLeft -= 300;
    });
});
