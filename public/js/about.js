document.addEventListener('DOMContentLoaded', () => {
    let lat = document.getElementById('latitudine-hidden').value;
    let lon = document.getElementById('longitudine-hidden').value;  

        if (lat && lon) {
            const map = L.map('map').setView([lat, lon], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([lat, lon]).addTo(map)
            .bindPopup('We are located here')  // The message that appears when the marker is clicked
            .openPopup();
            }
});