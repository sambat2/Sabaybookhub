<?php
// Database connection
require_once '../config/condb.php'; // Include the database connection file
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Handle form submission for adding/updating books
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $title = htmlspecialchars(trim($_POST['title'])); // Sanitize input
    $author = htmlspecialchars(trim($_POST['author'])); // Sanitize input
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT); // Validate price
    $image = $_FILES['image'];
    $bookId = isset($_POST['book_id']) ? intval($_POST['book_id']) : null;

    if (!$title || !$author || !$price) {
        die("Invalid input. Please check your form data.");
    }

    // Handle file upload
    $imagePath = null;
    if ($image && $image['tmp_name']) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        // Sanitize file name
        $fileName = basename($image['name']);
        $fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $fileName); // Remove special characters

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowedTypes)) {
            die("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
        }

        $imagePath = $uploadDir . $fileName;
        if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
            die("Failed to upload file. Please check the directory permissions.");
        }
    }

    if ($bookId) {
        // Update existing book
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $title, $author, $price, $imagePath, $bookId);
    } else {
        // Insert new book
        $stmt = $conn->prepare("INSERT INTO books (title, author, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $title, $author, $price, $imagePath);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Book saved successfully!');</script>";
    } else {
        echo "<script>alert('Error saving book: " . htmlspecialchars($stmt->error) . "');</script>";
    }

    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: index.php");
    exit();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $bookId = intval($_GET['delete']); // Sanitize input

    // Delete the book
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $bookId);

    if ($stmt->execute()) {
        // Re-sequence the id column
        $conn->query("SET @count = 0");
        $conn->query("UPDATE books SET id = (@count := @count + 1)");
        $conn->query("ALTER TABLE books AUTO_INCREMENT = 1");

        echo "<script>alert('Book deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting book: " . htmlspecialchars($stmt->error) . "');</script>";
    }

    $stmt->close();

    // Redirect to avoid repeated deletion on refresh
    header("Location: index.php");
    exit();
}

// Handle edit request
$editBook = null;
if (isset($_GET['edit'])) {
    $bookId = intval($_GET['edit']); // Sanitize input
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editBook = $result->fetch_assoc();
    $stmt->close();
}
// Handle delete receipts request
if (isset($_GET['delete'])) {
    $bookId = intval($_GET['delete']); // Sanitize input

    // Delete the book
    $stmt = $conn->prepare("DELETE FROM purchases WHERE id = ?");
    $stmt->bind_param("i", $bookId);

    if ($stmt->execute()) {
        // Re-sequence the id column
        $conn->query("SET @count = 0");
        $conn->query("UPDATE purchases SET id = (@count := @count + 1)");
        $conn->query("ALTER TABLE purchases AUTO_INCREMENT = 1");

        echo "<script>alert('Receipt deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting Receipt: " . htmlspecialchars($stmt->error) . "');</script>";
    }

    $stmt->close();

    // Redirect to avoid repeated deletion on refresh
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="font-sans bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 h-screen bg-gray-800 text-white p-5">
            <h2 class="text-center text-2xl font-bold mb-8">Admin Dashboard</h2>
            <ul class="space-y-4">
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-gray-700">Dashboard</a></li>
                <li><a href="logout.php" class="block py-2 px-4 rounded hover:bg-gray-700">Logout</a></li>
            </ul>
        </div>
        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6">Manage Books</h1>

            <!-- Add/Edit Book Form -->
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4"><?php echo $editBook ? 'Edit Book' : 'Add Book'; ?></h2>
                <form class="space-y-4" method="POST" action="index.php" enctype="multipart/form-data">
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($editBook['id'] ?? ''); ?>">
                    <div>
                        <label for="title" class="block text-gray-700">Title</label>
                        <input type="text" id="title" name="title" class="w-full p-2 border border-gray-300 rounded" placeholder="Enter book title" value="<?php echo htmlspecialchars($editBook['title'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="author" class="block text-gray-700">Author</label>
                        <input type="text" id="author" name="author" class="w-full p-2 border border-gray-300 rounded" placeholder="Enter author name" value="<?php echo htmlspecialchars($editBook['author'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="price" class="block text-gray-700">Price</label>
                        <input type="number" id="price" name="price" class="w-full p-2 border border-gray-300 rounded" placeholder="Enter book price" step="0.01" value="<?php echo htmlspecialchars($editBook['price'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="image" class="block text-gray-700">Book Image</label>
                        <input type="file" id="image" name="image" class="w-full p-2 border border-gray-300 rounded" accept="image/*">
                    </div>
                    <button type="submit" name="save" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
                </form>
            </div>

            <!-- Books Table -->
            <div>
                <h2 class="text-xl font-bold mb-4">Books List</h2>
                <table class="w-full bg-white rounded shadow">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-2 px-4 text-left">#</th>
                            <th class="py-2 px-4 text-left">Title</th>
                            <th class="py-2 px-4 text-left">Author</th>
                            <th class="py-2 px-4 text-left">Price</th>
                            <th class="py-2 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, title, author, price FROM books";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['id']) . "</td>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['title']) . "</td>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['author']) . "</td>
                                    <td class='py-2 px-4'>\$" . htmlspecialchars($row['price']) . "</td>
                                    <td class='py-2 px-4'>
                                        <a href='index.php?edit=" . htmlspecialchars($row['id']) . "' class='text-blue-500 hover:underline'>Edit</a>
                                        <a href='index.php?delete=" . htmlspecialchars($row['id']) . "' class='text-red-500 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='py-2 px-4 text-center'>No books found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Recent Purchases Section -->
            <div class="mt-10">
                <h2 class="text-xl font-bold mb-4">Recent Purchases</h2>
                <table class="w-full bg-white rounded shadow">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-2 px-4 text-left">#</th>
                            <th class="py-2 px-4 text-left">Name</th>
                            <th class="py-2 px-4 text-left">Contact</th>
                            <th class="py-2 px-4 text-left">Price</th>
                            <th class="py-2 px-4 text-left">Receipt</th>
                            <th class="py-2 px-4 text-left">Date</th>
                            <th class="py-2 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch recent purchases from the database
                        $sql = "SELECT id, name, contact, book_price, receipt_path, created_at FROM purchases ORDER BY created_at DESC LIMIT 10";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['id']) . "</td>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['name']) . "</td>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['contact']) . "</td>
                                    <td class='py-2 px-4'>\$" . htmlspecialchars($row['book_price']) . "</td>
                                    <td class='py-2 px-4'>
                                        <a href='" . htmlspecialchars("../" . $row['receipt_path']) . "' target='_blank' class='text-blue-500 hover:underline'>View</a>
                                    </td>
                                    <td class='py-2 px-4'>" . htmlspecialchars($row['created_at']) . "</td>
                                    <td class='py-2 px-4'>
                                        <a href='index.php?delete=" . htmlspecialchars($row['id']) . "' class='text-red-500 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";

                            }
                        } else {
                            echo "<tr><td colspan='6' class='py-2 px-4 text-center'>No recent purchases found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
