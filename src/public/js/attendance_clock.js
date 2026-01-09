function updateTime() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');

    const el = document.getElementById('current-time');
    if (el) {
        el.textContent = `${h}:${m}`;
    }
}

updateTime();
setInterval(updateTime, 1000);
