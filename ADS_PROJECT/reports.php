<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
if ($role !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/db.php';

$total_enrolled = 0;
$pending = 0;
$approved = 0;
$active_sections = 0;
$total_assignments = 0;

if ($stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments")) {
    $stmt->execute();
    $stmt->bind_result($total_enrolled);
    $stmt->fetch();
    $stmt->close();
}
if ($stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE status = 'Pending'")) {
    $stmt->execute();
    $stmt->bind_result($pending);
    $stmt->fetch();
    $stmt->close();
}
if ($stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE status = 'Approved'")) {
    $stmt->execute();
    $stmt->bind_result($approved);
    $stmt->fetch();
    $stmt->close();
}
if ($stmt = $conn->prepare("SELECT COUNT(*) FROM sections WHERE status = 'Active'")) {
    $stmt->execute();
    $stmt->bind_result($active_sections);
    $stmt->fetch();
    $stmt->close();
}
if ($stmt = $conn->prepare("SELECT COUNT(*) FROM section_assignments")) {
    $stmt->execute();
    $stmt->bind_result($total_assignments);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reports - Stud-Track</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 text-slate-900 flex">
  <aside class="w-64 bg-white shadow-lg border-r border-slate-200 flex flex-col">
    <div class="p-6 border-b border-slate-200">
      <div class="flex items-center gap-3">
        <img src="assets/images/Logo.png" alt="SVNHS Logo" class="h-10 w-10 rounded-full shadow-md object-cover" />
        <div>
          <h1 class="text-lg font-bold text-emerald-900">Stud-Track</h1>
          <p class="text-xs text-slate-500">Management System</p>
        </div>
      </div>
    </div>

    <nav class="flex-1 p-4">
      <ul class="space-y-2">
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
      </ul>
    </nav>

    <div class="p-4 border-t border-slate-200">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
          <span class="text-sm font-semibold text-emerald-700">A</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-slate-900 truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? 'Admin'); ?></p>
          <p class="text-xs text-slate-500">Admin</p>
        </div>
      </div>
      <a href="logout.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition w-full">
        <span class="text-lg">🚪</span>
        <span class="font-medium">Logout</span>
      </a>
    </div>
  </aside>

  <div class="flex-1 flex flex-col min-h-screen">
    <header class="bg-white border-b border-slate-200 shadow-sm px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-slate-900">Reports</h2>
          <p class="text-slate-600">Overview of enrollments, sections, and assignment activity.</p>
        </div>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6">
      <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
          <p class="text-sm font-medium text-slate-600 mb-2">Total Enrolled</p>
          <p class="text-3xl font-bold text-emerald-700"><?php echo $total_enrolled; ?></p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
          <p class="text-sm font-medium text-slate-600 mb-2">Pending Admissions</p>
          <p class="text-3xl font-bold text-amber-600"><?php echo $pending; ?></p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
          <p class="text-sm font-medium text-slate-600 mb-2">Approved Admissions</p>
          <p class="text-3xl font-bold text-blue-600"><?php echo $approved; ?></p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
          <p class="text-sm font-medium text-slate-600 mb-2">Active Sections</p>
          <p class="text-3xl font-bold text-slate-900"><?php echo $active_sections; ?></p>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Section Assignments</h3>
        <p class="text-slate-600 mb-4">Total assigned student placements across all sections.</p>
        <div class="rounded-2xl bg-slate-50 p-5">
          <p class="text-3xl font-bold text-slate-900"><?php echo $total_assignments; ?></p>
          <p class="text-sm text-slate-600">Assigned students</p>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
