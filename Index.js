// ---- CRUD FUNCTIONS ----
export function fetchHistory() {
  fetch("fetch.php")
    .then(res => res.json())
    .then(data => {
      let rows = "";
      data.forEach(row => {
        rows += `<tr>
          <td>${row.id}</td>
          <td id="text-${row.id}">${row.text}</td>
          <td>${row.created_at}</td>
          <td>
            <button onclick="editHistory(${row.id})">Edit</button>
            <button onclick="deleteHistory(${row.id})">Delete</button>
          </td>
        </tr>`;
      });
      document.querySelector("#historyTable tbody").innerHTML = rows;
    })
    .catch(err => console.error("Fetch history error:", err));
}

export function addHistory(city) {
  if (!city || city.trim() === "") return;
  const formData = new FormData();
  formData.append("text", city);

  fetch("add.php", { method: "POST", body: formData })
    .then(() => fetchHistory())
    .catch(err => console.error("Add history error:", err));
}

// ---- UPDATE + DELETE ----
window.editHistory = function (id) {
  const currentText = document.getElementById("text-" + id).innerText;
  document.getElementById("text-" + id).innerHTML = `
    <input type="text" id="edit-${id}" value="${currentText}">
    <button onclick="updateHistory(${id})">Submit</button>
  `;
};

window.updateHistory = function (id) {
  const newText = document.getElementById("edit-" + id).value;
  const formData = new FormData();
  formData.append("id", id);
  formData.append("text", newText);

  fetch("update.php", { method: "POST", body: formData })
    .then(() => fetchHistory());
};

window.deleteHistory = function (id) {
  if (!confirm("Are you sure?")) return;
  const formData = new FormData();
  formData.append("id", id);

  fetch("delete.php", { method: "POST", body: formData })
    .then(() => fetchHistory());
};

// ---- AQI FETCHING ----
const apiKey = "57f84fb3948f84ba9de6a31d526820ab";

export async function getCityAirData(city) {
  try {
    const geoRes = await fetch(
      `https://api.openweathermap.org/geo/1.0/direct?q=${city}&limit=1&appid=${apiKey}`
    );
    const geoData = await geoRes.json();
    if (geoData.length === 0) { alert("City not found!"); return; }

    const { lat, lon } = geoData[0];
    const airRes = await fetch(
      `https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat}&lon=${lon}&appid=${apiKey}`
    );
    const airData = await airRes.json();

    const comp = airData.list[0].components;
    const aqi = airData.list[0].main.aqi;

    let msg = "";
    if (aqi === 1) msg = "Air is clean ✅ Safe";
    else if (aqi === 2) msg = "Fair 🙂 Acceptable, minor effects";
    else if (aqi === 3) msg = "Moderate 😐 Some health concerns";
    else if (aqi === 4) msg = "Poor ⚠️ Unhealthy for sensitive groups";
    else msg = "Very Poor ❌ Unsafe for everyone";

    document.getElementById("txt2").textContent = msg;
    document.getElementById("co").textContent = comp.co;
    document.getElementById("no2").textContent = comp.no2;
    document.getElementById("o3").textContent = comp.o3;
    document.getElementById("so2").textContent = comp.so2;
    document.getElementById("pm25").textContent = comp.pm2_5;
    document.getElementById("pm10").textContent = comp.pm10;
    document.getElementById("nh3").textContent = comp.nh3;
  } catch (err) { console.error("Air data error:", err); }
}