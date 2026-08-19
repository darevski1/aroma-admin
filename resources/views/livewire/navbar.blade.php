<nav id="navbar" class="site-navbar bg-transparent backdrop-blur-none shadow-none sticky top-0 z-50 transition-all duration-300">
  <div class="site-navbar__inner max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">

    <!-- Logo -->
    <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
      <img class="h-10 w-auto" src="{{ asset('./assets/images/logo-itplus.png') }}" alt="Your Company">
    </a>

    <!-- Mobile Button -->
    <button data-collapse-toggle="navbar-dropdown" type="button"
      class="site-navbar__mobile-toggle inline-flex items-center p-2 w-10 h-10 justify-center text-sm rounded-base md:hidden"
      aria-controls="navbar-dropdown" aria-expanded="false">

      <span class="sr-only">Open main menu</span>

      <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
      </svg>
    </button>

    <!-- Navigation -->
    <div class="site-navbar__menu hidden w-full md:block md:w-auto" id="navbar-dropdown">

      <ul
        class="site-navbar__list flex flex-col p-4 md:p-0 mt-4 md:flex-row md:mt-0 md:border-0">

        @foreach ($navItems as $nav)

        <!-- ITEM -->
        <li class="site-navbar__item relative {{ $nav->subNavigations->count() ? 'has-dropdown' : '' }}">

          @if ($nav->subNavigations->count())

          <!-- Parent With Dropdown -->
          <div class="site-navbar__parent">
            <a href="{{ url($nav->slug) }}" class="site-navbar__link site-navbar__link--dropdown">
              <span>{{ $nav->name }}</span>
              <svg class="site-navbar__chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6" aria-hidden="true">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="m1 1 4 4 4-4" />
              </svg>
            </a>

            <button type="button" class="site-navbar__dropdown-toggle md:hidden" aria-expanded="false" aria-label="Отвори подмени за {{ $nav->name }}">
              <svg class="site-navbar__chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6" aria-hidden="true">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="m1 1 4 4 4-4" />
              </svg>
            </button>
          </div>

          <!-- Dropdown -->
          <div
            class="site-navbar__dropdown absolute left-0 z-10 mt-2 font-normal bg-white w-64 dropdown-menu">

            <ul class="site-navbar__dropdown-list py-2 text-sm text-gray-700">

              @foreach ($nav->subNavigations as $sub)

              <li>
                <a href="{{ url($nav->slug . '/' . $sub->slug) }}" class="site-navbar__dropdown-link block">
                  {{ $sub->sub_name }}
                </a>
              </li>

              @endforeach

            </ul>

          </div>

          @else

          <!-- Normal Link -->
          <a href="{{ url($nav->slug) }}"
            class="site-navbar__link block">

            {{ $nav->name }}

          </a>

          @endif

        </li>

        @endforeach

      </ul>

    </div>
  </div>
</nav>

<script>
  // MOBILE MENU
    const mobileButton = document.querySelector('[data-collapse-toggle]');
    const mobileMenu = document.getElementById('navbar-dropdown');

    mobileButton?.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('is-open');
        mobileMenu.classList.toggle('hidden', !isOpen);
        mobileButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    // MOBILE DROPDOWNS ONLY. Parent links remain normal links.
    document.querySelectorAll('.site-navbar__dropdown-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const item = this.closest('.site-navbar__item');
            const isOpen = item.classList.toggle('is-open');
            this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
</script>
