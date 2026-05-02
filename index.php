<?php
require('db.php');

$successMsg = $errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!$email) {
        $errorMsg = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $errorMsg = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errorMsg = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $insert->bind_param("ss", $email, $hashed);
            if ($insert->execute()) {
                $successMsg = "Signup successful! <a href='login.php'>Login here</a>";
            } else {
                $errorMsg = "Error: Please try again.";
            }
            $insert->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>VJ WEATHER - Signup</title>
    <style>
        body,html{margin:0;padding:0;height:100%;font-family:sans-serif;
            background:linear-gradient(rgba(57,82,157,0.7),rgba(57,82,157,0.7)),
            url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
            background-size:cover;display:flex;justify-content:center;align-items:center;}
        .card{background:white;border-radius:15px;padding:40px 30px;width:90%;max-width:400px;
            box-shadow:0 8px 30px rgba(57,82,157,0.3);text-align:center;}
        h2{margin-bottom:20px;color:rgb(57,82,157);}
        input{width:100%;padding:12px;margin-bottom:15px;border-radius:10px;
            border:1.5px solid #999;font-size:1rem;}
        input:focus{border-color:rgb(57,82,157);outline:none;
            box-shadow:0 0 8px rgba(57,82,157,0.4);}
        button{width:100%;padding:12px;border-radius:25px;border:none;
            background:rgb(57,82,157);color:white;font-size:1.1rem;font-weight:700;
            cursor:pointer;}
        button:hover{background:rgb(43,62,120);}
        .msg{padding:10px;border-radius:8px;margin-bottom:15px;}
        .error{background:#fdd;color:#d93025;}
        .success{background:#dfd;color:#207520;}
    </style>
</head>
<body>
<div class="card">
    <h2>SIGN UP</h2>
    <?php if($errorMsg): ?><div class="msg error"><?= $errorMsg ?></div><?php endif; ?>
    <?php if($successMsg): ?><div class="msg success"><?= $successMsg ?></div><?php endif; ?>

    <form method="POST" id="signupForm">
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required minlength="6">
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">SIGN UP</button>
    </form>
    <a href="login.php">Login Here </a>
</div>
</body>
</html>
