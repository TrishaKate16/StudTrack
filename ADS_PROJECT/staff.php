<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
if ($role !== 'Staff') {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Path - Stud-Track</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
  <div class="mx-auto max-w-4xl px-6 py-12">
    <div class="rounded-3xl bg-white p-10 shadow-xl border border-slate-200">
      <h1 class="text-4xl font-bold text-slate-900 mb-4">Staff Test Path</h1>
      <p class="text-slate-600 mb-8">This is the staff route. Use it to verify staff access and navigate to staff pages.</p>
      <div class="space-y-4">
        <a href="dashboard.php" class="block rounded-2xl bg-emerald-600 px-6 py-4 text-white font-semibold shadow hover:bg-emerald-700">Open Dashboard</a>
        <a href="admission.php" class="block rounded-2xl bg-blue-600 px-6 py-4 text-white font-semibold shadow hover:bg-blue-700">Open Admission</a>
        <a href="sections.php" class="block rounded-2xl bg-violet-600 px-6 py-4 text-white font-semibold shadow hover:bg-violet-700">Open Sectioning</a>
        <a href="logout.php" class="block rounded-2xl bg-slate-200 px-6 py-4 text-slate-900 font-semibold shadow hover:bg-slate-300">Logout</a>
      </div>
    </div>
  </div>
</body>
</html>
