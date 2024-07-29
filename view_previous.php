<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT *, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s') AS complaint_date FROM complaints WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Previous Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .complaint {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .profile-tooltip {
            position: relative;
            display: inline-block;
        }

        .profile-tooltip .tooltip-content {
            visibility: hidden;
            width: max-content;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .profile-tooltip:hover .tooltip-content {
            visibility: visible;
            opacity: 1;
        }
        .gradient-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-image: linear-gradient(to right, #8E2DE2, #4A00E0); /* Update gradient colors as needed */
            color: #fff; /* Text color */
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    
<nav class="bg-gradient-to-r from-purple-400 to-pink-500 p-4 mb-8 rounded-lg shadow-lg">
        <div class="flex justify-between items-center">
            <a class="text-white font-bold text-2xl" href="#">Complaint Management</a>
            <div>
                <a class="text-white hover:text-gray-200 px-4 profile-tooltip" href="#">
                    Profile
                    <div class="tooltip-content">
                        <p><?php echo $_SESSION['username']; ?></p>
                    </div>
                </a>
                <a class="text-white hover:text-gray-200 px-4" href="user_dashboard.php">Dashboard</a>
                <a class="text-white hover:text-gray-200 px-4" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Previous Requests</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="complaint">
                <p><strong>Date:</strong> <?php echo $row['complaint_date']; ?></p>
                <p><strong>Complaint:</strong> <?php echo $row['complaint']; ?></p>
                <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-purple-400 to-pink-500 text-white text-center py-4">
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
