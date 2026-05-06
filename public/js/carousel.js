document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('.carousel-container');
    
    containers.forEach(container => {
        const track = container.querySelector('.carousel-track');
        const btnPrev = container.querySelector('.carousel-nav.prev');
        const btnNext = container.querySelector('.carousel-nav.next');

        if (!track) return;

        let isDown = false;
        let startX;
        let scrollLeft;

        track.addEventListener('mousedown', (e) => {
            isDown = true;
            track.classList.add('active');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });

        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('active');
        });

        track.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('active');
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 2;
            track.scrollLeft = scrollLeft - walk;
        });
        
        track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });

        track.addEventListener('touchmove', (e) => {
            const x = e.touches[0].pageX - track.offsetLeft;
            const walk = (x - startX) * 2;
            track.scrollLeft = scrollLeft - walk;
        });

        if (btnPrev && btnNext) {
            btnNext.addEventListener('click', () => {
                track.scrollBy({ left: 400, behavior: 'smooth' });
            });

            btnPrev.addEventListener('click', () => {
                track.scrollBy({ left: -400, behavior: 'smooth' });
            });
        }
    });
});
