<!-- Premium Professional Navigation Bar -->
<nav id="navbar" class="bg-transparent backdrop-blur-none shadow-none sticky top-0 z-50 transition-all duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-9 lg:px-9">
    <div class="flex justify-between h-16 items-center">
      <!-- Left Section - Logo/Brand -->
      <div class="flex items-center">
        <a class="flex items-center space-x-2" href="/">
          <img class="h-10 w-auto" src="{{ asset('./assets/images/logo-itplus.png') }}" alt="Your Company">
        </a>
      </div>

      <!-- Center Section - Main Navigation (Desktop) -->
      <div class="hidden md:flex items-center space-x-1">
        @foreach ($navItems as $nav)
        <div class="relative">
          <a href="{{ url($nav->slug) }}"
            class="flex items-center text-taupe-900 hover:text-zinc-900 px-3 py-2 focus:outline-none desktop-dropdown-toggle font-semibold cursor-pointer"
            type="button">
            {{ $nav->name }}
            @if ($nav->subNavigations->count())
            <svg class="w-4 h-4 ml-1 transition-transform duration-200" fill="none" stroke="currentColor"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            @endif
          </a>

          @if ($nav->subNavigations->count())
          <div class="absolute left-0 mt-2 hidden bg-white shadow-lg rounded-lg min-w-[180px] z-50 desktop-dropdown">
            <ul class="py-2">
              @foreach ($nav->subNavigations as $sub)
              <li>
                <a href="{{ url($nav->slug . '/' . $sub->slug) }}"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 font-size-xs transition-colors duration-200">
                  {{ $sub->sub_name }}
                </a>
              </li>
              @endforeach
            </ul>
          </div>
          @endif
        </div>
        @endforeach
      </div>

      <!-- Mobile Menu Button -->
      <div class="md:hidden flex items-center">
        <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
          <i class="fas fa-bars text-2xl"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
    <div class="px-2 pt-2 pb-4 space-y-1">
      <!-- Example menu items -->
      <a href="#"
        class="block px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 flex items-center transition-colors duration-200">
        <i class="fas fa-home text-blue-500 mr-3 w-5 text-center"></i>
        Home
      </a>

      <!-- Mobile Dropdown Example -->
      <div class="mobile-dropdown relative">
        <button
          class="w-full flex justify-between items-center px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors duration-200 mobile-dropdown-toggle">
          <div class="flex items-center">
            <i class="fas fa-briefcase text-blue-500 mr-3 w-5 text-center"></i>
            Products
          </div>
          <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
        </button>
        <div class="pl-4 mt-1 space-y-1 hidden mobile-dropdown-menu">
          <a href="#"
            class="block px-4 py-2 rounded-lg text-sm text-gray-700 hover:text-blue-600 hover:bg-blue-50 flex items-center transition-colors duration-200">
            <i class="fas fa-laptop text-blue-400 mr-3 w-5 text-center"></i>
            Software
          </a>
          <a href="#"
            class="block px-4 py-2 rounded-lg text-sm text-gray-700 hover:text-blue-600 hover:bg-blue-50 flex items-center transition-colors duration-200">
            <i class="fas fa-server text-blue-400 mr-3 w-5 text-center"></i>
            Services
          </a>
          <a href="#"
            class="block px-4 py-2 rounded-lg text-sm text-gray-700 hover:text-blue-600 hover:bg-blue-50 flex items-center transition-colors duration-200">
            <i class="fas fa-mobile-screen text-blue-400 mr-3 w-5 text-center"></i>
            Apps
          </a>
        </div>
      </div>

      <a href="#"
        class="block px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 flex items-center transition-colors duration-200">
        <i class="fas fa-building text-blue-500 mr-3 w-5 text-center"></i>
        Company
      </a>
    </div>
  </div>
</nav>

<script>
  // Desktop dropdown toggle
  document.querySelectorAll('.desktop-dropdown-toggle').forEach(button => {
    button.addEventListener('click', e => {
      const dropdown = button.nextElementSibling;
      const arrow = button.querySelector('svg');

      // Toggle clicked dropdown
      dropdown.classList.toggle('hidden');
      arrow.classList.toggle('rotate-180');

      // Close other dropdowns
      document.querySelectorAll('.desktop-dropdown').forEach(d => {
        if (d !== dropdown) d.classList.add('hidden');
      });
      document.querySelectorAll('.desktop-dropdown-toggle svg').forEach(a => {
        if (a !== arrow) a.classList.remove('rotate-180');
      });
    });
  });

  // Close dropdown if click outside
  document.addEventListener('click', e => {
    const isDropdown = e.target.closest('.relative');
    if (!isDropdown) {
      document.querySelectorAll('.desktop-dropdown').forEach(d => d.classList.add('hidden'));
      document.querySelectorAll('.desktop-dropdown-toggle svg').forEach(a => a.classList.remove('rotate-180'));
    }
  });

  // Mobile menu toggle
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  const mobileMenu = document.getElementById('mobile-menu');
  mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });

  // Mobile dropdown toggle
  document.querySelectorAll('.mobile-dropdown-toggle').forEach(button => {
    button.addEventListener('click', () => {
      const menu = button.nextElementSibling;
      const arrow = button.querySelector('i.fas.fa-chevron-down');

      menu.classList.toggle('hidden');
      arrow.classList.toggle('rotate-180');
    });
  });
</script>