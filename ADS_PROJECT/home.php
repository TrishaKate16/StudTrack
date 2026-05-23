<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>San Vicente National High School</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-white text-slate-900">
  <!-- Header -->
  <header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="mx-auto max-w-full px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <img src="assets/images/Logo.png" alt="SVNHS Logo" class="h-14 w-14 rounded-full shadow-md object-cover" />
          <div>
          <h1 class="text-2xl font-bold text-emerald-900">San Vicente National High School</h1>
          <p class="text-sm text-slate-600">San Vicente, Roxas, Oriental Mindoro</p>
          </div>
        </div>
        <div class="flex items-center gap-6">
          <nav class="hidden gap-3 md:flex">
            <a href="home.php" class="rounded-full px-5 py-2 text-base font-semibold text-slate-700 transition hover:bg-emerald-700 hover:text-white">Home</a>
            <a href="#about" class="rounded-full px-5 py-2 text-base font-semibold text-slate-700 transition hover:bg-emerald-700 hover:text-white">About Us</a>
            <a href="#contact" class="rounded-full px-5 py-2 text-base font-semibold text-slate-700 transition hover:bg-emerald-700 hover:text-white">Contact Us</a>
          </nav>
          <a href="login.php" class="rounded-[20px] bg-emerald-700 px-6 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-800">
            Login
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="home" class="relative min-h-screen bg-cover bg-right overflow-hidden shadow-2xl" style="background-image: url('assets/images/SVNHS.png'); background-attachment: fixed;">
    <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/70 to-emerald-900/20"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-white/10"></div>

    <div class="relative mx-auto max-w-full px-6 py-20 min-h-screen flex items-center">
      <div class="grid gap-8 w-full lg:grid-cols-5">
        <!-- Left Content -->
        <div class="space-y-6 lg:col-span-2 z-10">
          <div class="space-y-2">
            <p class="text-lg font-medium font-bold text-emerald-900">Welcome to</p>
            <h2 class="text-6xl font-bold text-emerald-1500">STUD-TRACK</h2>
          </div>

          <div class="space-y-2 inline-flex flex-col items-start gap-2">
            <h3 class="text-3xl font-bold text-emerald-800">San Vicente National High School</h3>
            <p class="italic text-emerald-700 font-semibold text-lg">An Emblem of Holistic Excellence</p>
            <div class="h-1 w-max rounded-full bg-yellow-500"></div>
          </div>

          <p class="leading-relaxed font-bold text-slate-700 text-sm">
            We are committed to providing quality education that nurtures minds, builds character, and empowers students for a better tomorrow.
          </p>

          <div class="flex gap-4 pt-4">
            <button type="button" data-scroll="#about" class="scroll-btn rounded-[24px] bg-emerald-700 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-emerald-800 hover:shadow-xl inline-flex items-center gap-2">
              <span>✓</span>
              Learn More About Us
            </button>
            <button type="button" data-scroll="#contact" class="scroll-btn rounded-[24px] border-2 border-emerald-700 px-6 py-3 font-semibold text-emerald-700 bg-white shadow-md transition hover:bg-emerald-50 hover:shadow-lg inline-flex items-center gap-2">
              <span>✉</span>
              Contact Us
            </button>
          </div>
        </div>

        <!-- Center Logo -->
        <div class="hidden lg:col-span-1"></div>

        <!-- Right Content -->
        <div class="hidden lg:col-span-2"></div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="bg-white py-20">
    <div class="mx-auto max-w-4xl px-6">
      <div class="text-center">
        <h2 class="text-4xl font-bold text-slate-900">About Our School</h2>
        <div class="mx-auto mt-4 h-1 w-20 bg-yellow-500"></div>
      </div>

      <p class="mt-10 text-center leading-relaxed text-slate-700">
        San Vicente National High School is dedicated to academic excellence and holistic development. Together with our community, we strive to build responsible, confident, and future-ready learners who will contribute positively to society and achieve their fullest potential in all aspects of life.
      </p>

      <div class="mt-16 grid gap-8 md:grid-cols-3">
        <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-8 text-center shadow-md transition hover:shadow-lg hover:border-emerald-300">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-700 text-2xl text-white">
            📚
          </div>
          <h3 class="mt-4 text-xl font-semibold text-slate-900">Quality Education</h3>
          <p class="mt-3 text-slate-600">Providing comprehensive and standards-based curriculum designed for student success.</p>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-8 text-center shadow-md transition hover:shadow-lg hover:border-emerald-300">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-700 text-2xl text-white">
            🎯
          </div>
          <h3 class="mt-4 text-xl font-semibold text-slate-900">Character Building</h3>
          <p class="mt-3 text-slate-600">Developing responsible and ethical individuals with strong moral values.</p>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-8 text-center shadow-md transition hover:shadow-lg hover:border-emerald-300">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-700 text-2xl text-white">
            🌟
          </div>
          <h3 class="mt-4 text-xl font-semibold text-slate-900">Future Ready</h3>
          <p class="mt-3 text-slate-600">Empowering students with skills and knowledge for a better tomorrow.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="bg-emerald-50 py-20">
    <div class="mx-auto max-w-4xl px-6">
      <div class="text-center">
        <h2 class="text-4xl font-bold text-slate-900">Contact Us</h2>
        <div class="mx-auto mt-4 h-1 w-20 bg-yellow-500"></div>
      </div>

      <div class="mt-12 grid gap-8 md:grid-cols-3">
        <div class="rounded-[20px] bg-white p-8 text-center shadow-md">
          <div class="text-4xl">📍</div>
          <h3 class="mt-4 font-semibold text-slate-900">Location</h3>
          <p class="mt-2 text-slate-600">San Vicente, Roxas, Oriental Mindoro</p>
        </div>

        <div class="rounded-[20px] bg-white p-8 text-center shadow-md">
          <div class="text-4xl">📞</div>
          <h3 class="mt-4 font-semibold text-slate-900">Phone</h3>
          <p class="mt-2 text-slate-600">09262923681</p>
        </div>

        <div class="rounded-[20px] bg-white p-8 text-center shadow-md">
          <div class="text-4xl">📧</div>
          <h3 class="mt-4 font-semibold text-slate-900">Email</h3>
          <p class="mt-2 text-slate-600">309021@deped.gov.ph</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-emerald-900 py-8 text-white">
    <div class="mx-auto px-6 text-center">
      <p>© 2024 San Vicente National High School. All rights reserved.</p>
      <p class="mt-2 text-sm text-emerald-100">An Emblem of Holistic Excellence</p>
    </div>
  </footer>

  <script src="assets/js/script.js"></script>
</body>
</html>
