<?php
require('db.php');
require('auth_session.php');

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// ---------- ADD HISTORY ----------
if ($action === 'add' && isset($_POST['text'])) {
    $text = trim($_POST['text']);
    if(!empty($text)){
        $stmt = $conn->prepare("INSERT INTO history (user_id, text, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $text);
        if ($stmt->execute()) {
            echo "Added";
        } else {
            http_response_code(500);
            echo "Error: " . $conn->error;
        }
        $stmt->close();
    }
    exit;
}

// ---------- FETCH HISTORY ----------
elseif ($action === 'fetch') {
    $stmt = $conn->prepare("SELECT id, text, created_at FROM history WHERE user_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($rows);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport meta tag for mobile -->
    <title>History</title>
    <style>
        /* General Styles */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
          font-family: Arial, sans-serif;
          min-height: 100vh;
          display: flex;
          flex-direction: column;
          color: #fff;
          background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
          background-size: 400% 400%;
          animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
          0% { background-position: 0% 50%; }
          50% { background-position: 100% 50%; }
          100% { background-position: 0% 50%; }
        }

        /* Navigation Menu */
        /* Navigation Menu */
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

        /* Table */
        table {
          border-collapse: collapse;
          width: 90%;
          max-width: 1000px;
          margin: 20px auto;
          backdrop-filter: blur(6px);
          -webkit-backdrop-filter: blur(6px);
          background-color: rgba(255, 255, 255, 0.15);
          border-radius: 10px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        th, td {
          border: 1px solid rgba(255, 255, 255, 0.3);
          padding: 15px;
          text-align: center;
        }

        th {
          font-size: 1.2rem;
        }

        td {
          font-size: 1.1rem;
        }

        /* Buttons */
        button {
          margin: 2px;
          padding: 5px 10px;
          cursor: pointer;
          border-radius: 5px;
          border: none;
          background: rgba(0, 0, 0, 0.3);
          color: #fff;
          font-weight: bold;
          transition: background 0.3s;
        }

        button:hover {
          background: rgba(0, 0, 0, 0.6);
        }

        /* Header */
        h2 {
          text-align: center;
          margin: 20px 0;
          font-size: 2rem;
          text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4);
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
          /* Stack navigation items vertically */


          /* Table font size adjustments */
          th, td {
            font-size: 1rem;
            padding: 12px;
          }

          /* Adjust table width to allow horizontal scroll */
          table {
            width: 100%;
            overflow-x: auto;
          }

          /* Adjust the page header size */
          h2 {
            font-size: 1.5rem;
          }
        }

        @media (max-width: 480px) {
          /* Further adjustments for smaller devices */
          th, td {
            font-size: 0.9rem;
            padding: 10px;
          }

          h2 {
            font-size: 1.3rem;
          }

          /* Adjust buttons for small screens */
          button {
            padding: 8px 15px;
            font-size: 1rem;
          }
        }
    </style>
</head>
<body>

<h2>Search History</h2>

<nav class="links">
    <a href="home.html">Home</a>
    <a href="homePage.php">Search</a>
    <a href="compare.html">Compare</a>
    <a href="history.php">History</a>
    <a href="logout.php">Logout</a>
</nav>

<div style="overflow-x: auto; width: 100%;">
    <table>
        <thead>
            <tr><th>ID</th><th>City</th><th>Date</th></tr>
        </thead>
        <tbody id="historyBody"></tbody>
    </table>
</div>

<script>
    function fetchHistory() {
        fetch("history.php?action=fetch")
        .then(res => res.json())
        .then(data => {
            let rows = "";
            data.forEach(r => rows += `<tr>
                <td>${r.id}</td>
                <td>${r.text}</td>
                <td>${r.created_at}</td>
            </tr>`);
            document.getElementById("historyBody").innerHTML = rows;
        });
    }

    document.addEventListener("DOMContentLoaded", fetchHistory);
</script>

</body>
</html>
