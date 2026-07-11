<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme', 'light') }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'PAAUMENTOR') — Prince Abubakar Audu University</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/*  Page transition  */
body { animation: pageFadeIn 0.25s ease both; }
@keyframes pageFadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

/*  Top progress bar  */
#nprogress {
  position:fixed; top:0; left:0; right:0; height:3px;
  background:var(--blue-500); z-index:99999;
  transition:width 0.2s ease;
  border-radius:0 3px 3px 0;
  box-shadow:0 0 8px rgba(37,99,235,0.6);
}
#nprogress.done { opacity:0; transition:opacity 0.4s ease 0.1s; }

/*  Bell shake  */
@keyframes bellShake {
  0%,100%{ transform:rotate(0); }
  15%    { transform:rotate(18deg); }
  30%    { transform:rotate(-16deg); }
  45%    { transform:rotate(12deg); }
  60%    { transform:rotate(-8deg); }
  75%    { transform:rotate(5deg); }
}
.bell-shake { animation:bellShake 0.7s ease; }
</style>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('styles')
</head>
<body>

<nav class="navbar">
  <div class="container">
    <div class="nav-inner">
      <a href="{{ route('home') }}" class="nav-brand">
        <div class="nav-logo">PM</div>
        <span class="nav-brand-text">PAAU<span>MENTOR</span></span>
      </a>

      @auth
      <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu"><x-icon name="menu" :size="20" /></button>
      @endauth

      <div class="nav-actions">
        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">
          <x-icon name="moon" :size="16" class="icon-moon" />
          <x-icon name="sun"  :size="16" class="icon-sun" />
        </button>

        @auth
          <a href="{{ route('notifications.index') }}" style="position:relative;display:inline-flex;align-items:center;color:var(--text-2)">
            <x-icon name="bell" :size="20" />
            @if(auth()->user()->notifications()->whereNull('read_at')->count() > 0)
              <span style="position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:var(--gold);border-radius:50%;display:block"></span>
            @endif
          </a>
          <div class="avatar avatar-sm" style="cursor:pointer"
               onclick="location.href='{{ route('profile.show', auth()->user()) }}'">
            {{ auth()->user()->initials }}
          </div>
        @else
          <a href="{{ route('login') }}"    class="btn btn-outline btn-sm">Sign In</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
        @endauth
      </div>
    </div>
  </div>
</nav>


@yield('content')

<script src="{{ asset('js/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/*  Progress bar  */
(function(){
  const bar = document.createElement('div');
  bar.id = 'nprogress';
  bar.style.width = '0%';
  document.body.appendChild(bar);

  let timer;
  function start() {
    bar.style.opacity = '1';
    bar.classList.remove('done');
    bar.style.width = '0%';
    let w = 0;
    clearInterval(timer);
    timer = setInterval(() => {
      w = w < 70 ? w + Math.random() * 8 : w < 90 ? w + 0.5 : w;
      bar.style.width = w + '%';
    }, 120);
  }
  function done() {
    clearInterval(timer);
    bar.style.width = '100%';
    setTimeout(() => bar.classList.add('done'), 200);
  }

  document.addEventListener('click', e => {
    const a = e.target.closest('a');
    if (a && a.href && !a.target && !a.href.startsWith('#') &&
        !a.href.startsWith('javascript') && a.origin === location.origin) {
      start();
    }
  });
  document.addEventListener('submit', () => start());
  window.addEventListener('pageshow', () => done());
  done(); // complete on initial load
})();

/*  Bell shake  */
(function(){
  const bell = document.querySelector('a[href*="notifications"]');
  const dot  = bell?.querySelector('span');
  if (bell && dot) {
    bell.classList.add('bell-shake');
    setInterval(() => {
      bell.classList.remove('bell-shake');
      void bell.offsetWidth; // reflow to restart animation
      bell.classList.add('bell-shake');
    }, 4000);
  }
})();

/*  Page fade-out on navigation  */
document.addEventListener('click', e => {
  const a = e.target.closest('a');
  if (a && a.href && !a.target && !a.href.startsWith('#') &&
      !a.href.startsWith('javascript') && a.origin === location.origin) {
    document.body.style.transition = 'opacity 0.15s ease';
    document.body.style.opacity = '0';
  }
});
</script>

<script>
(function () {
  //  Toast preset 
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  //  Flash messages 
  @if(session('success'))
    Toast.fire({ icon: 'success', title: @json(session('success')) });
  @endif

  @if(session('error'))
    Toast.fire({ icon: 'error', title: @json(session('error')) });
  @endif

  @if(session('info'))
    Toast.fire({ icon: 'info', title: @json(session('info')) });
  @endif

  //  Confirm dialogs (any form with data-confirm attribute) 
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const self = this;
      Swal.fire({
        title: 'Are you sure?',
        text: self.dataset.confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel',
      }).then(function (result) {
        if (result.isConfirmed) self.submit();
      });
    });
  });
})();
</script>

@stack('modals')
@stack('scripts')
<script>
function toggleSidebar() {
  const sidebar  = document.querySelector('.sidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  if (!sidebar) return;
  const isOpen = sidebar.classList.toggle('open');
  overlay && overlay.classList.toggle('show', isOpen);
  document.body.style.overflow = isOpen ? 'hidden' : '';
}
function closeSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar && sidebar.classList.remove('open');
  overlay && overlay.classList.remove('show');
  document.body.style.overflow = '';
}
// Close sidebar when a nav link is clicked on mobile
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 768) closeSidebar();
    });
  });
});
</script>
</body>
</html>
