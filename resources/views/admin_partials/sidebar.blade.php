<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
  id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
      aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand px-4 py-3 m-0" href="{{ route('home') }}">
      <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
      <span class="ms-1 text-sm text-dark">ITPlus Admin</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0 mb-2">
  <div class=" " id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('home') }}">
          <i class="material-symbols-rounded opacity-5">dashboard</i>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('users') }}">
          <i class="material-symbols-rounded opacity-5">manage_accounts</i>
          <span class="nav-link-text ms-1">User Management</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('navigation') }}">
          <i class="material-symbols-rounded opacity-5">navigation</i>
          <span class="nav-link-text ms-1">Navigation Builder</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('pages.index') }}">
          <i class="material-symbols-rounded opacity-5">description</i>
          <span class="nav-link-text ms-1">Pages</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('posts.index') }}">
          <i class="material-symbols-rounded opacity-5">article</i>
          <span class="nav-link-text ms-1">Posts</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('site-images.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('site-images.index') }}">
          <i class="material-symbols-rounded opacity-5">image</i>
          <span class="nav-link-text ms-1">Site Images</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('media.index') }}">
          <i class="material-symbols-rounded opacity-5">perm_media</i>
          <span class="nav-link-text ms-1">Media Library</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('homepage-sections.index') }}">
          <i class="material-symbols-rounded opacity-5">view_quilt</i>
          <span class="nav-link-text ms-1">Homepage Sections</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cenobots-clients.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('cenobots-clients.index') }}">
          <i class="material-symbols-rounded opacity-5">handshake</i>
          <span class="nav-link-text ms-1">CenoBots Projects</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('analytics.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('analytics.index') }}">
          <i class="material-symbols-rounded opacity-5">analytics</i>
          <span class="nav-link-text ms-1">Analytics</span>
        </a>
      </li>
      @php $chatbotOpen = request()->routeIs('chatbot.admin.*'); @endphp
      <li class="nav-item">
        {{-- The theme's collapse caret is a Font Awesome glyph; a Material Symbols chevron is used instead. --}}
        <style>
          .chatbot-menu-toggle::after { display: none !important; }
          .chatbot-menu-toggle .chatbot-chevron { font-size: 18px; transition: transform .2s ease; }
          .chatbot-menu-toggle[aria-expanded="true"] .chatbot-chevron { transform: rotate(180deg); }
          #chatbot-submenu { display: none !important; }
          #chatbot-submenu.is-open { display: block !important; }
        </style>
        {{-- The submenu deliberately does not use Bootstrap's .collapse: the
             admin loads Bootstrap twice plus Preline/Flowbite, and the menu
             was being closed again right after opening. A small script below
             owns the open state on its own and remembers it across pages.
             On Chatbot pages the submenu is always open and "Chatbot" links
             to the Chatbot dashboard. --}}
        @if ($chatbotOpen)
        <a class="nav-link chatbot-menu-toggle active bg-gradient-dark text-white" href="{{ route('chatbot.admin.dashboard') }}" aria-expanded="true" aria-controls="chatbot-submenu">
        @else
        <a class="nav-link chatbot-menu-toggle text-dark" href="{{ route('chatbot.admin.dashboard') }}" role="button" data-chatbot-toggle aria-expanded="false" aria-controls="chatbot-submenu">
        @endif
          <i class="material-symbols-rounded opacity-5">smart_toy</i>
          <span class="nav-link-text ms-1">Chatbot</span>
          <i class="material-symbols-rounded chatbot-chevron ms-auto opacity-7" aria-hidden="true">expand_more</i>
        </a>
        <div class="{{ $chatbotOpen ? 'is-open' : '' }}" id="chatbot-submenu">
          <ul class="nav flex-column ms-4 ps-1">
            @foreach ([
              'chatbot.admin.dashboard' => 'Dashboard',
              'chatbot.admin.settings' => 'General Settings',
              'chatbot.admin.providers' => 'AI Provider Settings',
              'chatbot.admin.instructions' => 'System Instructions',
              'chatbot.admin.knowledge.index' => 'Knowledge Base',
              'chatbot.admin.faqs.index' => 'FAQ Management',
              'chatbot.admin.categories.index' => 'Categories',
              'chatbot.admin.conversations.index' => 'Conversations',
              'chatbot.admin.statistics' => 'Usage Statistics',
            ] as $chatbotRoute => $chatbotLabel)
              <li class="nav-item">
                <a class="nav-link py-1 text-sm {{ request()->routeIs(\Illuminate\Support\Str::beforeLast($chatbotRoute, '.index') . '*') ? 'font-weight-bold text-dark' : 'text-secondary' }}" href="{{ route($chatbotRoute) }}">
                  <span class="nav-link-text">{{ $chatbotLabel }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
        <script>
          // Runs right away (not on DOMContentLoaded) so it is bound before
          // slow dev-server modules load, and the remembered state is applied
          // before the sidebar is painted.
          (function () {
            var toggle = document.querySelector('[data-chatbot-toggle]');
            var menu = document.getElementById('chatbot-submenu');
            var KEY = 'admin.chatbotMenuOpen';
            var read = function () { try { return localStorage.getItem(KEY) === '1'; } catch (e) { return false; } };
            var save = function (open) { try { localStorage.setItem(KEY, open ? '1' : '0'); } catch (e) {} };
            if (!menu) return;
            if (!toggle) { save(true); return; } // Chatbot pages: always open, plain link
            var set = function (open) {
              menu.classList.toggle('is-open', open);
              toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            };
            set(read());
            toggle.addEventListener('click', function (e) {
              e.preventDefault();
              e.stopPropagation(); // keep page-wide click handlers out of it
              var open = !menu.classList.contains('is-open');
              set(open);
              save(open);
            });
          })();
        </script>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('health-check') }}">
          <i class="material-symbols-rounded opacity-5">monitor_heart</i>
          <span class="nav-link-text ms-1">Health Check</span>
        </a>
      </li>
    </ul>
  </div>

</aside>
