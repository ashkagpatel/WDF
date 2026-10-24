
const apiUrl = "api/events.php";

const eventForm = document.getElementById("eventForm");
const eventList = document.getElementById("eventList");
const message = document.getElementById("message");

async function apiRequest(method, data = null) {
    const options = { method: method };

    if (data !== null) {
        options.headers = { "Content-Type": "application/json" };
        options.body = JSON.stringify(data);
    }

    const response = await fetch(apiUrl, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

function showMessage(text) {
    message.textContent = text;
}

async function loadEvents() {
    try {
        const data = await apiRequest("GET");
        eventList.replaceChildren();

        if (data.events.length === 0) {
            eventList.textContent = "No events found.";
            return;
        }

        const table = document.createElement("table");
        const head = table.createTHead().insertRow();

        ["ID", "Title", "Date", "Category", "Actions"].forEach(name => {
            const th = document.createElement("th");
            th.textContent = name;
            head.appendChild(th);
        });

        const body = table.createTBody();

        data.events.forEach(event => {
            const row = body.insertRow();

            [event.id, event.title, event.event_date, event.category]
                .forEach(value => {
                    const cell = row.insertCell();
                    cell.textContent = value ?? "";
                });

            const actions = row.insertCell();

            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.type = "button";
            editButton.addEventListener("click", () => editEvent(event));

            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.type = "button";
            deleteButton.addEventListener("click", () => deleteEvent(event.id));

            actions.append(editButton, document.createTextNode(" "), deleteButton);
        });

        eventList.appendChild(table);
    } catch (error) {
        showMessage(error.message);
    }
}

eventForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const eventData = {
        title: document.getElementById("title").value.trim(),
        event_date: document.getElementById("event_date").value,
        category: document.getElementById("category").value.trim()
    };

    try {
        const result = await apiRequest("POST", eventData);
        showMessage(result.message);
        eventForm.reset();
        await loadEvents();
    } catch (error) {
        showMessage(error.message);
    }
});


function editEvent(event) {
    document.getElementById("editSection").hidden = false;

    document.getElementById("editId").value = event.id;
    document.getElementById("editTitle").value = event.title;
    document.getElementById("editDate").value = event.event_date;
    document.getElementById("editCategory").value = event.category;

    document.getElementById("editSection").scrollIntoView({
        behavior: "smooth"
    });
}


async function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    try {
        const result = await apiRequest("DELETE", { id: Number(id) });
        showMessage(result.message);
        await loadEvents();
    } catch (error) {
        showMessage(error.message);
    }
}


document.getElementById("editForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    const eventData = {
        id: Number(document.getElementById("editId").value),
        title: document.getElementById("editTitle").value.trim(),
        event_date: document.getElementById("editDate").value,
        category: document.getElementById("editCategory").value.trim()
    };

    try {
        const result = await apiRequest("PUT", eventData);

        showMessage(result.message);
        document.getElementById("editForm").reset();
        document.getElementById("editSection").hidden = true;

        await loadEvents();
    } catch (error) {
        showMessage(error.message);
    }
});

document.getElementById("cancelEdit").addEventListener("click", function () {
    document.getElementById("editForm").reset();
    document.getElementById("editSection").hidden = true;
});


loadEvents();
