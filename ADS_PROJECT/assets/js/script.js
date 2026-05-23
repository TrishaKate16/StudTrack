document.addEventListener('DOMContentLoaded', function () {
  // Login / register page behavior
  const form = document.getElementById('loginForm');
  const alertBox = document.getElementById('alert');
  const googleButton = document.getElementById('googleSignIn');
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');

  function showAlert(message, type = 'error') {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.classList.remove('hidden');
    alertBox.classList.add('show');
    alertBox.classList.toggle('border-emerald-200', type === 'success');
    alertBox.classList.toggle('bg-emerald-50', type === 'success');
    alertBox.classList.toggle('text-emerald-900', type === 'success');
    alertBox.classList.toggle('border-rose-200', type !== 'success');
    alertBox.classList.toggle('bg-rose-50', type !== 'success');
    alertBox.classList.toggle('text-rose-900', type !== 'success');
  }

  if (togglePassword && passwordInput) {
    const eyeOpen = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    const eyeClosed = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.22 18.22 0 0 1 5-5.06"/><path d="M3 3l18 18"/><path d="M9.53 9.53a3 3 0 0 0 4.24 4.24"/><path d="M14.47 14.47a3 3 0 0 1-4.24-4.24"/><path d="M14.12 9.88A3 3 0 0 1 15 12"/></svg>';

    togglePassword.innerHTML = eyeOpen;
    togglePassword.addEventListener('click', function () {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      this.innerHTML = isPassword ? eyeClosed : eyeOpen;
    });
  }

  if (form) {
    form.addEventListener('submit', function (event) {
      if (!alertBox) return;
      alertBox.classList.add('hidden');

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value.trim();
      const role = document.getElementById('role') ? document.getElementById('role').value : '';

      if (!email || !email.includes('@')) {
        showAlert('Please enter a valid email address.');
        event.preventDefault();
        return;
      }

      if (password.length < 6) {
        showAlert('Please enter a password with at least 6 characters.');
        event.preventDefault();
        return;
      }

      if (document.getElementById('role') && !role) {
        showAlert('Please select a role.');
        event.preventDefault();
        return;
      }
    });
  }

  if (googleButton) {
    googleButton.addEventListener('click', function () {
      showAlert('Google sign-in is not enabled in this demo.', 'error');
    });
  }

  // Home page behavior
  const navLinks = document.querySelectorAll('nav a[href^="#"]');
  const scrollButtons = document.querySelectorAll('button.scroll-btn');
  const header = document.querySelector('header');

  function smoothScroll(target) {
    const headerHeight = header ? header.offsetHeight : 0;
    const targetPosition = target.offsetTop - headerHeight;

    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    });
  }

  if (navLinks.length > 0) {
    navLinks.forEach((link) => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        const href = this.getAttribute('href');
        const target = document.querySelector(href);
        if (target) smoothScroll(target);
      });
    });
  }

  if (scrollButtons.length > 0) {
    scrollButtons.forEach((button) => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        const scrollTo = this.getAttribute('data-scroll');
        const target = document.querySelector(scrollTo);
        if (target) smoothScroll(target);
      });
    });
  }

  if (header) {
    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;
      if (currentScroll > 50) {
        header.classList.add('shadow-lg');
      } else {
        header.classList.remove('shadow-lg');
      }
    });
  }
});
