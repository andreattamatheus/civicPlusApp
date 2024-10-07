const apiUrl = 'http://localhost:8000/api/v1/events';

async function fetchEvents() {
    document.getElementById('loader').classList.remove('hidden');

    try {
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const events = await response.json();

        displayEvents(events['items']);
        document.getElementById('loader').classList.add('hidden');
    } catch (error) {
        document.getElementById('loader').classList.add('hidden');
        console.error('Error fetching events:', error);
    }
}

function displayEvents(events) {
    const eventList = document.getElementById('eventList');
    eventList.innerHTML = ''; // Clear previous events

    events.forEach(event => {
        const eventDiv = document.createElement('div');
        eventDiv.classList.add(
            'bg-white',
            'shadow',
            'rounded',
            'p-4',
            'mb-4'
        );

        eventDiv.innerHTML = `
            <h3 class="text-lg font-bold mb-2">${event.title}</h3>
            <p class="text-gray-700 mb-2">${event.description}</p>
            <button 
              onclick="viewEventDetails('${event.id}')" 
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
              View Details
            </button>
          `;
        eventList.appendChild(eventDiv);
    });
}

document.getElementById('addEventForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const title = document.getElementById('eventTitle').value;
    const description = document.getElementById('eventDescription').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    const newEvent = {
        title,
        description,
        startDate,
        endDate
    };

    try {
        const response = await fetch(apiUrl + '/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(newEvent)
        });

        if (!response.ok) {
            throw new Error('Failed to add event');
        }

        // Clear form and refresh events
        document.getElementById('addEventForm').reset();
        await fetchEvents();
    } catch (error) {
        console.error('Error adding event:', error);
    }
});

async function viewEventDetails(eventId) {
    document.getElementById('loader').classList.remove('hidden');
    try {
        const response = await fetch(`${apiUrl}/${eventId}`);
        if (!response.ok) {
            throw new Error('Event not found');
        }
        const event = await response.json();
        displayEventDetails(event);
        document.getElementById('loader').classList.add('hidden');
    } catch (error) {
        document.getElementById('loader').classList.add('hidden');
        console.error('Error fetching event details:', error);
    }
}

function displayEventDetails(event) {
    const detailsContent = document.getElementById('detailsContent');
    detailsContent.innerHTML = `
        <h4 class="text-2xl font-extrabold dark:text-gray">${event.title}</h4><br>
        <p class="font-normal text-gray-700 dark:text-gray-400">${event.description}</p>
        <br>
        <p class="font-normal text-gray-700 dark:text-gray-400">Start: ${new Date(event.startDate).toLocaleString()}</p>
        <br>
        <p class="font-normal text-gray-700 dark:text-gray-400">End: ${new Date(event.endDate).toLocaleString()}</p>
        <br>
    `;

    document.getElementById('eventList').classList.add('hidden');
    document.getElementById('eventDetail').classList.remove('hidden');
}

document.getElementById('backButton').addEventListener('click', () => {
    document.getElementById('eventDetail').classList.add('hidden');
    document.getElementById('eventList').classList.remove('hidden');
});

fetchEvents();
