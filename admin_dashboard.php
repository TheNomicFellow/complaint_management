<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle closing a complaint
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['close_complaint'])) {
    $complaint_id = $_POST['complaint_id'];

    // Update the status of the complaint to "Closed"
    $sql_update = "UPDATE complaints SET status='Closed' WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $complaint_id);
    if ($stmt_update->execute() === false) {
        die("Execute failed: " . $stmt_update->error);
    }
}

$sql = "SELECT complaints.id, users.username, complaints.complaint, complaints.status FROM complaints JOIN users ON complaints.user_id = users.id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<nav class="bg-gradient-to-r from-purple-400 to-pink-500 py-4">
    <div class="container mx-auto flex justify-between items-center">
        <a class="text-white text-lg font-semibold" href="#">Complaint Management</a>
        <ul class="flex items-center">
            <li class="mr-6">
                <a href="logout.php" class="text-white hover:text-gray-200">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mx-auto mt-8">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">

        <h2 class="text-3xl font-semibold text-center mb-6">Admin Dashboard</h2>

        <?php if ($result && $result->num_rows > 0): ?>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-purple-400 to-pink-500 text-white">
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">User</th>
                        <th class="px-4 py-2">Complaint</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="px-4 py-2"><?php echo $row['id']; ?></td>
                            <td class="px-4 py-2"><?php echo $row['username']; ?></td>
                            <td class="px-4 py-2"><?php echo $row['complaint']; ?></td>
                            <td class="px-4 py-2"><?php echo $row['status']; ?></td>
                            <td class="px-4 py-2">
                                <?php if ($row['status'] != 'Closed'): ?>
                                    <form method="post">
                                        <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="close_complaint" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Close</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-gray-500">No complaints found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
