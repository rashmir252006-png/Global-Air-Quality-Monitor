<?php
require('auth_session.php');
require('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport Meta Tag for mobile -->
    <title>Air Quality Monitor</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            color: #fff;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .head {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }

        nav.links {
            background-color: rgba(255, 255, 255, 0.15);
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 1rem 0;
            border-radius: 8px;
            margin: 0 1rem;
            flex-wrap: wrap;
        }

        nav.links a {
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1.1rem;
        }

        nav.links a:hover {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .menu {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .menu input, .menu button {
            padding: 12px 15px;
            border-radius: 30px;
            border: none;
            font-size: 1rem;
            max-width: 90vw;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .menu button {
            background: rgba(0,0,0,0.3);
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }

        .menu button:hover {
            background: rgba(0,0,0,0.6);
        }

        #air-quality {
            display: none;
            margin: 20px auto;
            width: 90%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.2s ease;
        }

        .box:hover {
            transform: translateY(-5px);
        }

        .box h2 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .box b {
            font-weight: bold;
        }

        /* Mobile-specific Styles */
        @media (max-width: 768px) {
            /* Make the text size smaller and adjust padding */
            .head h1 {
                font-size: 1.5rem;
                padding: 12px;
            }



            /* Adjust search bar size */
            .menu input, .menu button {
                font-size: 1rem;
                padding: 12px 20px;
                width: 100%;
            }

            /* Air quality box: single column on mobile */
            #air-quality {
                grid-template-columns: 1fr;
                padding: 10px;
            }

            /* Box padding and font size */
            .box {
                padding: 15px;
                font-size: 1rem;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }

            .menu input, .menu button {
                width: 100%;
                font-size: 1rem;
            }

            /* Adjust font sizes */
            .box h2 {
                font-size: 1.1rem;
            }
        }

        /* Further adjustment for smaller screens (below 480px) */
        @media (max-width: 480px) {
            .box {
                padding: 12px;
                font-size: 0.9rem;
            }

            .menu input, .menu button {
                font-size: 0.9rem;
            }

            .head h1 {
                font-size: 1.2rem;
            }
        }

    </style>
</head>
<body>

    <div class="head">
        <h1>Global Air Quality Monitor</h1>
        <p>Welcome</p>
    </div>

    <nav class="links">
        <a href="home.html">Home</a>
        <a href="homePage.php">Search</a>
        <a href="compare.html">Compare</a>
        <a href="history.php">History</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="menu">
        <input type="text" id="search" placeholder="Enter city or country">
        <button id="search-btn">Search</button>
    </div>

    <div id="air-quality">
        <div class="box"><h2>QUALITY OF AIR: <span id="txt2"></span></h2></div>
        <div class="box"><b>CO:</b> <span id="co"></span></div>
        <div class="box"><b>NO₂:</b> <span id="no2"></span></div>
        <div class="box"><b>O₃:</b> <span id="o3"></span></div>
        <div class="box"><b>SO₂:</b> <span id="so2"></span></div>
        <div class="box"><b>PM2.5:</b> <span id="pm25"></span></div>
        <div class="box"><b>PM10:</b> <span id="pm10"></span></div>
        <div class="box"><b>NH₃:</b> <span id="nh3"></span></div>
    </div>

    <script>
        const apiKey = "57f84fb3948f84ba9de6a31d526820ab";

        async function getCityAirData(city) {
            try {
                const geoRes = await fetch(`https://api.openweathermap.org/geo/1.0/direct?q=${city}&limit=1&appid=${apiKey}`);
                const geoData = await geoRes.json();
                
                if (!geoData.length) {
                    alert("City not found!");
                    document.getElementById("air-quality").style.display = "none";
                    return;
                }

                const { lat, lon } = geoData[0];
                const airRes = await fetch(`https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat}&lon=${lon}&appid=${apiKey}`);
                const airData = await airRes.json();
                const comp = airData.list[0].components;
                const aqi = airData.list[0].main.aqi;

                const messages = [
                    "Air is clean ✅ Safe",
                    "Fair 🙂 Acceptable, minor effects",
                    "Moderate 😐 Some health concerns",
                    "Poor ⚠️ Unhealthy for sensitive groups",
                    "Very Poor ❌ Unsafe for everyone"
                ];

                document.getElementById("txt2").textContent = messages[aqi - 1] || "Unknown";
                document.getElementById("co").textContent = comp.co;
                document.getElementById("no2").textContent = comp.no2;
                document.getElementById("o3").textContent = comp.o3;
                document.getElementById("so2").textContent = comp.so2;
                document.getElementById("pm25").textContent = comp.pm2_5;
                document.getElementById("pm10").textContent = comp.pm10;
                document.getElementById("nh3").textContent = comp.nh3;

                // City found, show grid and add to history
                document.getElementById("air-quality").style.display = "grid";
                addHistory(city);

            } catch (err) { console.error(err); }
        }

       function addHistory(city) {
            const fd = new FormData();
            fd.append("text", city);
            fetch("history.php?action=add", { method: "POST", body: fd })
                .catch(err => console.error(err));
        }

        document.getElementById("search-btn").addEventListener("click", () => {
            const city = document.getElementById("search").value.trim();
            if (!city) return alert("Enter a city!");
            // Provide visual feedback (optional) but don't show grid yet
            getCityAirData(city);
        });
    </script>
</body>
</html>
