<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complaint'])) {
    $complaint = $_POST['complaint'];

    // Insert the complaint into the database
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO complaints (user_id, complaint) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $complaint);
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    }

    // Redirect to clear the form and prevent resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Fetch user's complaints count
$user_id = $_SESSION['user_id'];
$sql_count = "SELECT COUNT(*) AS count FROM complaints WHERE user_id=?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$complaints_count = $row_count['count'];

// Fetch resolved complaints count
$sql_resolved_count = "SELECT COUNT(*) AS resolved_count FROM complaints WHERE user_id=? AND (status='Resolved' OR status='Closed')";
$stmt_resolved_count = $conn->prepare($sql_resolved_count);
$stmt_resolved_count->bind_param("i", $user_id);
$stmt_resolved_count->execute();
$result_resolved_count = $stmt_resolved_count->get_result();
$row_resolved_count = $result_resolved_count->fetch_assoc();
$resolved_count = $row_resolved_count['resolved_count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
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
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">

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
                <a class="text-white hover:text-gray-200 px-4" href="#">Dashboard</a>
                <a class="text-white hover:text-gray-200 px-4" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="grid grid-cols-12 gap-4">

        <!-- Left Sidebar -->
        <div class="col-span-3">
            <div class="bg-white p-4 rounded-lg shadow">
                <h4 class="text-xl font-bold mb-4">Dashboard</h4>
                <a href="view_previous.php" class="block py-2 px-4 text-blue-500 hover:bg-gray-200 rounded">View Previous Complaints</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-span-9">
        <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-2xl font-bold mb-4">Welcome, <?php echo $_SESSION['username']; ?></h2>
                
                <!-- Total Complaints -->
                <div class="bg-gradient-to-r from-green-400 to-blue-500 p-4 rounded-lg text-white mb-4">
                    <p class="text-lg font-semibold mb-2">Total Complaints</p>
                    <p class="text-3xl font-bold"><?php echo $complaints_count; ?></p>
                </div>

                
                <!-- Resolved Complaints -->
                <div class="bg-gradient-to-r from-purple-400 to-pink-500 p-4 rounded-lg text-white mb-4">
                    <p class="text-lg font-semibold mb-2">Resolved Complaints</p>
                    <p class="text-3xl font-bold"><?php echo $resolved_count; ?></p>
                </div>

                <!-- New Complaint Form -->
                <form method="post" class="mb-4">
                    <div class="mb-4">
                        <label for="complaint" class="block text-sm font-semibold mb-2">New Complaint:</label>
                        <textarea class="form-textarea border rounded-lg w-full p-2" id="complaint" name="complaint" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">File Complaint</button>
                </form>
            </div>
        </div>

    </div>

</div>
<footer class="gradient-footer">
    <!-- Footer content goes here -->
</footer>
</body>
</html>