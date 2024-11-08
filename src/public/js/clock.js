//全画面上部時計表示
function updateClock() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    const currentDate = `${year}-${month}-${day}`;
    const currentTime = `${hours}:${minutes}:${seconds}`;

    document.getElementById('date').textContent = currentDate;
    document.getElementById('time').textContent = currentTime;
}

setInterval(updateClock, 1000);
updateClock();
