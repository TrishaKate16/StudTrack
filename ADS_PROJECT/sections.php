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

if ($isStudent) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/db.php';

$message = '';
$alert_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';
    if ($mode === 'create_section') {
        $grade_level = trim($_POST['grade_level'] ?? '');
        $section_name = trim($_POST['section_name'] ?? '');
        $adviser_name = trim($_POST['adviser_name'] ?? '');
        $capacity = intval($_POST['capacity'] ?? 30);
        $room_number = trim($_POST['room_number'] ?? '');
        $status = $_POST['status'] ?? 'Active';

        if (empty($grade_level) || empty($section_name) || $capacity <= 0) {
            $message = 'Please provide grade level, section name and valid capacity.';
            $alert_type = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO sections (grade_level, section_name, adviser_name, capacity, room_number, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssiss', $grade_level, $section_name, $adviser_name, $capacity, $room_number, $status);
            if ($stmt->execute()) {
                $message = 'Section created successfully.';
                $alert_type = 'success';
            } else {
                $message = 'Unable to create section: ' . $stmt->error;
                $alert_type = 'error';
            }
            $stmt->close();
        }
    } elseif ($mode === 'assign_student') {
        $section_id = intval($_POST['section_id'] ?? 0);
        $enrollment_id = intval($_POST['enrollment_id'] ?? 0);
        if ($section_id <= 0 || $enrollment_id <= 0) {
            $message = 'Please select both a section and a student.';
            $alert_type = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO section_assignments (section_id, enrollment_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $section_id, $enrollment_id);
            if ($stmt->execute()) {
                $message = 'Student assigned to section successfully.';
                $alert_type = 'success';
            } else {
                $message = 'Unable to assign student: ' . $stmt->error;
                $alert_type = 'error';
            }
            $stmt->close();
        }
    }
}

$sectionsResult = $conn->query("SELECT s.*, COUNT(sa.id) AS assigned_students FROM sections s LEFT JOIN section_assignments sa ON s.id = sa.section_id GROUP BY s.id ORDER BY s.grade_level, s.section_name");
$availableStudentsResult = $conn->query("SELECT id, first_name, last_name, grade_level, status FROM enrollments WHERE status = 'Approved' ORDER BY created_at DESC");
$assignmentsResult = $conn->query("SELECT sa.id, sa.assigned_at, s.grade_level, s.section_name, e.first_name, e.last_name FROM section_assignments sa JOIN sections s ON sa.section_id = s.id JOIN enrollments e ON sa.enrollment_id = e.id ORDER BY sa.assigned_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sections Management - Stud-Track</title>
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
            <a href="reports.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
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
            <a href="admission.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
              <span class="text-lg">➕</span>
              <span>Admission</span>
            </a>
          </li>
          <li>
            <a href="sections.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 font-medium border border-emerald-200">
              <span class="text-lg">🎯</span>
              <span>Sectioning</span>
            </a>
          </li>
        <?php else: ?>
          <li>
            <a href="admission.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
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
        <h2 class="text-2xl font-bold text-slate-900">Sections Management</h2>
        <div class="flex items-center gap-4">
          <span class="text-sm text-slate-600">Welcome back, Admin!</span>
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

        <div class="grid gap-6 mb-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-2xl font-bold text-slate-900 mb-6">Create New Section</h3>
            <form method="post" class="space-y-6">
              <input type="hidden" name="mode" value="create_section">
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Grade Level *</label>
                  <select name="grade_level" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" required>
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
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Section Name *</label>
                  <input name="section_name" type="text" placeholder="e.g., Section A" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Adviser Name</label>
                  <input name="adviser_name" type="text" placeholder="Enter adviser name" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Capacity *</label>
                  <input name="capacity" type="number" min="1" placeholder="Enter capacity" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" value="30" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Room Number</label>
                  <input name="room_number" type="text" placeholder="Enter room number" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                  <select name="status" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition">
                    <option>Active</option>
                    <option>Inactive</option>
                  </select>
                </div>
              </div>
              <div class="flex gap-4">
                <button type="submit" class="flex-1 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3 text-white font-semibold hover:shadow-lg transition">✓ Create Section</button>
                <button type="reset" class="flex-1 rounded-2xl bg-slate-200 px-5 py-3 text-slate-700 font-semibold hover:bg-slate-300 transition">Clear</button>
              </div>
            </form>
          </div>

          <div class=\"bg-white rounded-2xl p-6 shadow-sm border border-slate-200\">
            <h3 class=\"text-xl font-bold text-slate-900 mb-6\">Assign Approved Student</h3>
            <form method="post" class="space-y-4">
              <input type="hidden" name="mode" value="assign_student">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Section</label>
                <select name="section_id" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" required>
                  <option value="">Select section</option>
                  <?php while ($section = $sectionsResult->fetch_assoc()): ?>
                    <option value="<?php echo $section['id']; ?>"><?php echo htmlspecialchars($section['grade_level'] . ' - ' . $section['section_name'] . ' (' . $section['adviser_name'] . ')'); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Approved Student</label>
                <select name="enrollment_id" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:border-blue-500 focus:ring-blue-200 transition" required>
                  <option value="">Select approved student</option>
                  <?php while ($student = $availableStudentsResult->fetch_assoc()): ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name'] . ' — ' . $student['grade_level']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-5 py-3 text-white font-semibold hover:shadow-lg transition">➕ Assign Student</button>
            </form>
          </div>
        </div>

        <?php
        $sectionsResult = $conn->query("SELECT s.*, COUNT(sa.id) AS assigned_students FROM sections s LEFT JOIN section_assignments sa ON s.id = sa.section_id GROUP BY s.id ORDER BY s.grade_level, s.section_name");
        ?>

        <div class=\"grid gap-6 md:grid-cols-2 lg:grid-cols-3 mb-6\">
          <?php while ($section = $sectionsResult->fetch_assoc()): ?>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h4 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($section['grade_level'] . ' - ' . $section['section_name']); ?></h4>
                  <p class="text-sm text-slate-500"><?php echo htmlspecialchars($section['room_number'] ? $section['room_number'] . ' • ' : '') . htmlspecialchars($section['adviser_name']); ?></p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $section['status'] === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>"><?php echo htmlspecialchars($section['status']); ?></span>
              </div>
              <div class="space-y-2 mb-4 pb-4 border-b border-slate-200">
                <p class="text-sm text-slate-600"><strong>Students:</strong> <?php echo htmlspecialchars($section['assigned_students']); ?> / <?php echo htmlspecialchars($section['capacity']); ?></p>
              </div>
            </div>
          <?php endwhile; ?>
        </div>

        <div class=\"bg-white rounded-2xl p-6 shadow-sm border border-slate-200\">
          <div class=\"mb-6\">
            <h3 class=\"text-xl font-bold text-slate-900\">Assigned Students</h3>
            <p class=\"text-sm text-slate-500\">Review the latest section assignments.</p>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Student</th>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Section</th>
                  <th class="px-4 py-3 text-sm font-semibold text-slate-700">Assigned At</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                <?php while ($assignment = $assignmentsResult->fetch_assoc()): ?>
                  <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-4 text-slate-900"><?php echo htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']); ?></td>
                    <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($assignment['grade_level'] . ' - ' . $assignment['section_name']); ?></td>
                    <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($assignment['assigned_at']); ?></td>
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
