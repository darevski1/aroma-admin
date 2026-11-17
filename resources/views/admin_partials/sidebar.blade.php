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
        <a class="nav-link {{ $chatbotOpen ? 'active bg-gradient-dark text-white' : 'text-dark' }}" data-bs-toggle="collapse" href="#chatbotMenu" role="button" aria-expanded="{{ $chatbotOpen ? 'true' : 'false' }}" aria-controls="chatbotMenu">
          <i class="material-symbols-rounded opacity-5">smart_toy</i>
          <span class="nav-link-text ms-1">Chatbot</span>
        </a>
        <div class="collapse {{ $chatbotOpen ? 'show' : '' }}" id="chatbotMenu">
          <ul class="nav ms-4 ps-1">
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
