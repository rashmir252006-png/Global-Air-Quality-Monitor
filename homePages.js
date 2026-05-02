import { addHistory, getCityAirData, fetchHistory } from "./index.js";

// ---- SEARCH FUNCTION ----
window.searchCity = function () {
  const city = document.getElementById("search").value.trim();
  if (!city) return alert("Enter a city!");

  document.getElementById("air-quality").style.display = "grid";

  getCityAirData(city);   // Display AQI
  addHistory(city);       // Save history
};

// ---- AUTO LOAD HISTORY ON PAGE LOAD ----
document.addEventListener("DOMContentLoaded", fetchHistory);