<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$displayRole = $role;
$isAdmin = $role === 'Admin';
$isStaff = $role === 'Staff';
$isStudent = $role === 'Student';

require_once __DIR__ . '/db.php';

$message = '';
$alert_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $grade_level = trim($_POST['grade_level'] ?? '');

    if (empty($first_name) || empty($last_name) || !$email || empty($guardian_name) || empty($grade_level)) {
        $message = 'Please fill in all required fields.';
        $alert_type = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO enrollments (first_name, last_name, email, phone, guardian_name, address, grade_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $first_name, $last_name, $email, $phone, $guardian_name, $address, $grade_level);
        if ($stmt->execute()) {
            $message = 'Enrollment request submitted successfully and is now pending approval.';
            $alert_type = 'success';
        } else {
            $message = 'Unable to submit enrollment: ' . $stmt->error;
            $alert_type = 'error';
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    if (in_array($action, ['approve', 'reject'], true)) {
        $status = $action === 'approve' ? 'Approved' : 'Rejected';
        $stmt = $conn->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        if ($stmt->execute()) {
            $message = "Enrollment #$id has been $status.";
            $alert_type = 'success';
        } else {
            $message = 'Unable to update status: ' . $stmt->error;
            $alert_type = 'error';
        }
        $stmt->close();
    }
}

$enrollmentResult = $conn->query("SELECT * FROM enrollments ORDER BY created_at DESC LIMIT 20");

$pendingCountResult = $conn->query("SELECT COUNT(*) AS total FROM enrollments WHERE status = 'Pending'");
$approvedCountResult = $conn->query("SELECT COUNT(*) AS total FROM enrollments WHERE status = 'Approved'");
$pendingCount = 0;
$approvedCount = 0;
if ($pendingCountResult) {
    $pendingCount = $pendingCountResult->fetch_assoc()['total'];
}
if ($approvedCountResult) {
    $approvedCount = $approvedCountResult->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Enrollment Management - Stud-Track</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 text-slate-900 flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-lg border-r border-slate-200 flex flex-col">
    <!-- Logo Section -->
    <div class="p-6 border-b border-slate-200">
      <div class="flex items-center gap-3">
        <img src="assets/images/Logo.png" alt="SVNHS Logo" class="h-10 w-10 rounded-full shadow-md object-cover" />
        <div>
          <h1 class="text-lg font-bold text-emerald-900">Stud-Track</h1>
          <p class="text-xs text-slate-500">Management System</p>
        </div>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4">
      <ul class="space-y-2">
        <?php if ($isAdmin): ?>
          <li>
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
              <span class="text-lg">📊</span>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="reports.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 font-medium border border-emerald-200">
              <span class="text-lg">📋</span>
              <span>Reports</span>
            </a>
          </li>
        <?php elseif ($isStaff): ?>
          <li>
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
              <span class="text-lg">📊</span>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="admission.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 font-medium border border-emerald-200">
              <span class="text-lg">➕</span>
              <span>Admission</span>
            </a>
          </li>
          <li>
            <a href="sections.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
              <span class="text-lg">🎯</span>
              <span>Sectioning</span>
            </a>
          </li>
        <?php else: ?>
          <li>
            <a href="admission.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 font-medium border border-emerald-200">
              <span class="text-lg">➕</span>
              <span>Admission</span>
            </a>
          </li>
          <li>
            <a href="enrollment.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
              <span class="text-lg">📝</span>
              <span>Enrollment</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>

    <!-- User Info & Logout -->
    <div class="p-4 border-t border-slate-200">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
          <span class="text-sm font-semibold text-emerald-700"><?php echo strtoupper(substr($displayRole, 0, 1)); ?></span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-slate-900 truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? 'User'); ?></p>
          <p class="text-xs text-slate-500"><?php echo htmlspecialchars($displayRole); ?></p>
        </div>
      </div>
      <a href="logout.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition w-full">
        <span class="text-lg">🚪</span>
        <span class="font-medium">Logout</span>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col min-h-screen">
    <!-- Top Bar -->
    <header class="bg-white border-b border-slate-200 shadow-sm px-6 py-4">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-900">Enrollment Management</h2>
        <div class="flex items-center gap-4">
          <span class="text-sm text-slate-600">Welcome, Student!</span>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 overflow-auto">
      <div class="p-6">
        <?php if ($message): ?>
          <div class="mb-6 rounded-2xl border px-4 py-4 text-sm <?php echo $alert_type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900'; ?>">
            <?php echo htmlspecialchars($message); ?>
          </div>
        <?php endif; ?>

        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mb-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Pending Requests</p>
            <p class="mt-4 text-3xl font-bold text-amber-600"><?php echo $pendingCount; ?></p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Approved Enrollments</p>
            <p class="mt-4 text-3xl font-bold text-emerald-700"><?php echo $approvedCount; ?></p>
          </div>
        </div>

        <div class="grid gap-6 mb-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-2xl font-bold text-slate-900 mb-6">New Student Enrollment</h3>
            <form method="post" class="space-y-6">
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">First Name *</label>
                  <input name="first_name" type="text" placeholder="Enter first name" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Last Name *</label>
                  <input name="last_name" type="text" placeholder="Enter last name" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Email *</label>
                  <input name="email" type="email" placeholder="Enter email address" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Grade Level *</label>
                  <select name="grade_level" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition" required>
                    <option value="">Select Grade Level</option>
                    <option>Grade 7</option>
                    <option>Grade 8</option>
                    <option>Grade 9</option>
                    <option>Grade 10</option>
                    <option>Grade 11</option>
                    <option>Grade 12</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                  <input name="phone" type="tel" placeholder="Enter phone number" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Guardian Name *</label>
                  <input name="guardian_name" type="text" placeholder="Enter guardian name" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition" required />
                </div>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                <textarea name="address" rows="3" placeholder="Enter address" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-emerald-200 transition"></textarea>
              </div>
              <div class="flex gap-4">
                <button type="submit" class="flex-1 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-5 py-3 text-white font-semibold hover:shadow-lg transition">✓ Submit Enrollment</button>
                <button type="reset" class="flex-1 rounded-2xl bg-slate-200 px-5 py-3 text-slate-700 font-semibold hover:bg-slate-300 transition">Clear</button>
              </div>
            </form>
          </div>
        </div>

        <div id=\"enrollment-list\" class=\"bg-white rounded-2xl p-6 shadow-sm border border-slate-200\">
          <div class=\"mb-6\">
            <h3 class=\"text-xl font-bold text-slate-900\">Recent Enrollments</h3>
            <p class=\"text-sm text-slate-500\">Approve or reject enrollment requests from here.</p>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Name</th>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Grade</th>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Email</th>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Status</th>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                <?php while ($row = $enrollmentResult->fetch_assoc()): ?>
                  <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-4 text-slate-900"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($row['grade_level']); ?></td>
                    <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="px-4 py-4">
                      <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $row['status'] === 'Approved' ? 'bg-emerald-100 text-emerald-700' : ($row['status'] === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                      </span>
                    </td>
                    <td class="px-4 py-4 text-sm font-medium text-slate-700 space-x-2">
                      <?php if ($row['status'] === 'Pending'): ?>
                        <a href="enrollment.php?action=approve&id=<?php echo $row['id']; ?>" class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 hover:bg-emerald-200 transition">Approve</a>
                        <a href="enrollment.php?action=reject&id=<?php echo $row['id']; ?>" class="rounded-full bg-rose-100 px-3 py-1 text-rose-700 hover:bg-rose-200 transition">Reject</a>
                      <?php else: ?>
                        <span class="text-slate-500">No action</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 px-6 py-4 text-center text-sm text-slate-600">
      <p>© 2024 San Vicente National High School - Stud-Track Management System</p>
    </footer>
  </div>
</aside>
</body>
</html>
