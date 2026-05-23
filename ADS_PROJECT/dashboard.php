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

// Initialize variables for dashboard stats
$total_enrolled = 0;
$pending = 0;
$active_sections = 0;

// Fetch total enrolled students
try {
    if ($stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments")) {
        $stmt->execute();
        $stmt->bind_result($total_enrolled);
        $stmt->fetch();
        $stmt->close();
    }

    // Fetch pending enrollments
    if ($stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE status = 'Pending'")) {
        $stmt->execute();
        $stmt->bind_result($pending);
        $stmt->fetch();
        $stmt->close();
    }

    // Fetch active sections
    if ($stmt = $conn->prepare("SELECT COUNT(*) FROM sections WHERE status = 'Active'")) {
        $stmt->execute();
        $stmt->bind_result($active_sections);
        $stmt->fetch();
        $stmt->close();
    }
} catch (Exception $e) {
    // Handle database errors (log or display message)
    error_log("Database error: " . $e->getMessage());
}


// Close database connection
$conn->close();
?>
<!-- HTML Document -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stud-Track Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 text-slate-900 overflow-x-hidden flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-lg border-r border-slate-200 flex flex-col h-screen overflow-hidden">
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
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 font-medium border border-emerald-200">
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
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 font-medium border border-emerald-200">
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
  <div class="flex-1 flex flex-col min-h-screen min-w-0">
    <!-- Top Bar -->
    <header class="bg-white border-b border-slate-200 shadow-sm px-6 py-4">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-900">Dashboard</h2>
        <div class="flex items-center gap-4">
          <span class="text-sm text-slate-600">Welcome back, Admin!</span>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 overflow-y-auto p-6">
      <?php if ($isAdmin): ?>
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Admin Dashboard</h2>
          <p class="text-slate-600">Manage the school system, view reports, and keep track of enrollments.</p>
        </div>

        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Total Enrolled</p>
                <p class="text-3xl font-bold text-emerald-700"><?php echo $total_enrolled; ?></p>
              </div>
              <div class="text-3xl">👥</div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Pending Admissions</p>
                <p class="text-3xl font-bold text-amber-600"><?php echo $pending; ?></p>
              </div>
              <div class="text-3xl">⏳</div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Active Sections</p>
                <p class="text-3xl font-bold text-blue-600"><?php echo $active_sections; ?></p>
              </div>
              <div class="text-3xl">📚</div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">System Access</p>
                <p class="text-3xl font-bold text-slate-800">Admin</p>
              </div>
              <div class="text-3xl">🔐</div>
            </div>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 mb-8">
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Reports</h3>
            <p class="text-slate-600 mb-6">Open analytics and reports for student and section performance.</p>
            <a href="reports.php" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-white font-semibold hover:bg-emerald-700 transition">View Reports</a>
          </div>
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Enrollments</h3>
            <p class="text-slate-600 mb-6">Review newly submitted student enrollments and approve or reject them.</p>
            <a href="enrollment.php" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-white font-semibold hover:bg-blue-700 transition">Manage Admission</a>
          </div>
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Sectioning</h3>
            <p class="text-slate-600 mb-6">Create sections and assign students to classrooms.</p>
            <a href="sections.php" class="inline-flex items-center justify-center rounded-2xl bg-slate-800 px-5 py-3 text-white font-semibold hover:bg-slate-900 transition">Open Sectioning</a>
          </div>
        </div>
      <?php elseif ($isStaff): ?>
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Staff Dashboard</h2>
          <p class="text-slate-600">Access admissions and sectioning tools for daily operations.</p>
        </div>

        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 mb-8">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Active Sections</p>
                <p class="text-3xl font-bold text-blue-600"><?php echo $active_sections; ?></p>
              </div>
              <div class="text-3xl">📚</div>
            </div>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Pending Admissions</p>
                <p class="text-3xl font-bold text-amber-600"><?php echo $pending; ?></p>
              </div>
              <div class="text-3xl">⏳</div>
            </div>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Total Enrolled</p>
                <p class="text-3xl font-bold text-emerald-700"><?php echo $total_enrolled; ?></p>
              </div>
              <div class="text-3xl">👥</div>
            </div>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 mb-8">
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Admission</h3>
            <p class="text-slate-600 mb-6">Review and manage student admission requests.</p>
            <a href="admission.php" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-white font-semibold hover:bg-emerald-700 transition">Open Admission</a>
          </div>
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Sectioning</h3>
            <p class="text-slate-600 mb-6">Assign students to sections and keep classroom loads balanced.</p>
            <a href="sections.php" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-white font-semibold hover:bg-blue-700 transition">Open Sectioning</a>
          </div>
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Back to Home</h3>
            <p class="text-slate-600 mb-6">Go back to the public landing page whenever needed.</p>
            <a href="home.php" class="inline-flex items-center justify-center rounded-2xl bg-slate-800 px-5 py-3 text-white font-semibold hover:bg-slate-900 transition">Public Home</a>
          </div>
        </div>
      <?php else: ?>
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Student Portal</h2>
          <p class="text-slate-600">Use the links below to apply for admission or manage enrollment.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 mb-8">
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Admission</h3>
            <p class="text-slate-600 mb-6">Submit your admission request and wait for approval.</p>
            <a href="admission.php" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-white font-semibold hover:bg-emerald-700 transition">Apply Now</a>
          </div>
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Enrollment</h3>
            <p class="text-slate-600 mb-6">View enrollment status once your admission is processed.</p>
            <a href="enrollment.php" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-white font-semibold hover:bg-blue-700 transition">Open Enrollment</a>
          </div>
        </div>
      <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-white border-t border-slate-200">
      <div class="w-full px-6 py-6 text-center text-sm text-slate-600">
        <p>© 2024 San Vicente National High School - Stud-Track Management System</p>
      </div>
    </footer>
  </div>
  <!-- JavaScript for Dynamic Lists -->
  <script>
    const juniorGrades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
    const seniorGrades = ['Grade 11', 'Grade 12'];
    const sections = ['Section A', 'Section B', 'Section C'];
    let editState = { type: null, index: null };

    function renderList(containerId, items, type) {
      const container = document.getElementById(containerId);
      if (!items.length) {
        container.innerHTML = '<p class="text-sm text-slate-500 italic">No entries yet.</p>';
        return;
      }

      container.innerHTML = items.map((item, index) => {
        const isEditing = editState.type === type && editState.index === index;
        if (isEditing) {
          return `
            <div class="flex flex-col gap-2 bg-white rounded-lg p-3 border-2 border-emerald-500">
              <input id="edit-input-${type}-${index}" value="${item}" class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 outline-none focus:border-emerald-500 focus:ring-emerald-200 text-sm" />
              <div class="flex justify-end gap-2">
                <button data-action="save" data-type="${type}" data-index="${index}" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">✓ Save</button>
                <button data-action="cancel" class="rounded-lg bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-300">✕ Cancel</button>
              </div>
            </div>
          `;
        }

        return `
          <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-slate-200 hover:border-emerald-300 hover:shadow-sm transition">
            <p class="font-medium text-slate-900 text-sm">${item}</p>
            <div class="flex items-center gap-1.5">
              <button data-action="edit" data-type="${type}" data-index="${index}" class="rounded-lg bg-blue-100 text-blue-700 px-2 py-1 text-xs font-semibold transition hover:bg-blue-200">✏️ Edit</button>
              <button data-action="delete" data-type="${type}" data-index="${index}" class="rounded-lg bg-red-100 text-red-700 px-2 py-1 text-xs font-semibold transition hover:bg-red-200">🗑️ Delete</button>
            </div>
          </div>
        `;
      }).join('');
    }

    function renderLists() {
      renderList('junior-list', juniorGrades, 'junior');
      renderList('senior-list', seniorGrades, 'senior');
      renderList('section-list', sections, 'section');
    }

    function removeItem(type, index) {
      if (type === 'junior') juniorGrades.splice(index, 1);
      if (type === 'senior') seniorGrades.splice(index, 1);
      if (type === 'section') sections.splice(index, 1);
      editState = { type: null, index: null };
      renderLists();
    }

    function startEdit(type, index) {
      editState = { type, index };
      renderLists();
    }

    function saveEdit(type, index) {
      const input = document.getElementById(`edit-input-${type}-${index}`);
      const value = input ? input.value.trim() : '';
      if (!value) return;
      if (type === 'junior') juniorGrades[index] = value;
      if (type === 'senior') seniorGrades[index] = value;
      if (type === 'section') sections[index] = value;
      editState = { type: null, index: null };
      renderLists();
    }

    document.addEventListener('click', function(event) {
      const button = event.target.closest('button[data-action]');
      if (!button) return;
      const action = button.dataset.action;
      const type = button.dataset.type;
      const index = Number(button.dataset.index);

      if (action === 'delete') removeItem(type, index);
      if (action === 'edit') startEdit(type, index);
      if (action === 'save') saveEdit(type, index);
      if (action === 'cancel') {
        editState = { type: null, index: null };
        renderLists();
      }
    });

    renderLists();
  </script>
</body>
</html>
