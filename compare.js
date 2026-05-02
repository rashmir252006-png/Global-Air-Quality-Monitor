const apiKey = "57f84fb3948f84ba9de6a31d526820ab";

async function getCityAirData(city1, city2) {
  try {
    // Fetch geolocation for city1
    const geoRes1 = await fetch(
      `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(city1)}&limit=1&appid=${apiKey}`
    );
    if (!geoRes1.ok) throw new Error(`API Error: ${geoRes1.status}`);
    const geoData1 = await geoRes1.json();
    if (!geoData1.length) {
      alert(`City "${city1}" not found! Please enter a valid city name.`);
      return;
    }

    // Fetch geolocation for city2
    const geoRes2 = await fetch(
      `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(city2)}&limit=1&appid=${apiKey}`
    );
    if (!geoRes2.ok) throw new Error(`API Error: ${geoRes2.status}`);
    const geoData2 = await geoRes2.json();
    if (!geoData2.length) {
      alert(`City "${city2}" not found! Please enter a valid city name.`);
      return;
    }

    const { lat: lat1, lon: lon1 } = geoData1[0];
    const { lat: lat2, lon: lon2 } = geoData2[0];

    // Fetch air pollution data for city1
    const airRes1 = await fetch(
      `https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat1}&lon=${lon1}&appid=${apiKey}`
    );
    const airData1 = await airRes1.json();

    // Fetch air pollution data for city2
    const airRes2 = await fetch(
      `https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat2}&lon=${lon2}&appid=${apiKey}`
    );
    const airData2 = await airRes2.json();

    // Helper function to safely update element textContent
    function updateText(id, text) {
      const el = document.getElementById(id);
      if (el) el.textContent = text;
    }

    const components1 = airData1.list[0].components;
    const components2 = airData2.list[0].components;
    const aqi1 = airData1.list[0].main.aqi;
    const aqi2 = airData2.list[0].main.aqi;

    const aqiDescriptions = {
      1: "Good (Safe)",
      2: "Fair (Acceptable)",
      3: "Moderate (Caution)",
      4: "Poor (Unhealthy)",
      5: "Very Poor (Danger)",
    };

    updateText('txt2', `City 1: ${aqiDescriptions[aqi1] || "Unknown"} | City 2: ${aqiDescriptions[aqi2] || "Unknown"}`);

    // Update pollution components city1
    updateText('co1', components1.co);
    updateText('no21', components1.no2);
    updateText('o31', components1.o3);
    updateText('so21', components1.so2);
    updateText('pm251', components1.pm2_5); // Fixed: pm2_5
    updateText('pm101', components1.pm10);
    updateText('nh31', components1.nh3);

    // Update pollution components city2
    updateText('co2', components2.co);
    updateText('no22', components2.no2);
    updateText('o32', components2.o3);
    updateText('so22', components2.so2);
    updateText('pm252', components2.pm2_5); // Fixed: pm2_5
    updateText('pm102', components2.pm10);
    updateText('nh32', components2.nh3);

  } catch (error) {
    console.error(error);
    alert(`An error occurred: ${error.message}. Check console for details.`);
  }
}

function compareCities() {
  const city1 = document.getElementById('city1').value.trim();
  const city2 = document.getElementById('city2').value.trim();

  // Basic validation
  if (!city1 || !city2) {
    alert("Please enter both city names.");
    return;
  }

  // Regex validation: Check if input contains at least one letter
  // This prevents inputs like "123" or "..." which might return weird results or 404s
  const validNameRegex = /[a-zA-Z]/;
  if (!validNameRegex.test(city1) || !validNameRegex.test(city2)) {
    alert("Please enter valid city names (must contain letters).");
    return;
  }

  getCityAirData(city1, city2);
}
