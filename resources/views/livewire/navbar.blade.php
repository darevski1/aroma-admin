<nav id="navbar" class="bg-transparent backdrop-blur-none shadow-none sticky top-0 z-50 transition-all duration-300">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">

    <!-- Logo -->
    <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
      <img class="h-10 w-auto" src="{{ asset('./assets/images/logo-itplus.png') }}" alt="Your Company">
    </a>

    <!-- Mobile Button -->
    <button data-collapse-toggle="navbar-dropdown" type="button"
      class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-body rounded-base md:hidden hover:bg-neutral-secondary-soft hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary"
      aria-controls="navbar-dropdown" aria-expanded="false">

      <span class="sr-only">Open main menu</span>

      <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
      </svg>
    </button>

    <!-- Navigation -->
    <div class="hidden w-full md:block md:w-auto" id="navbar-dropdown">

      <ul
        class="flex flex-col font-medium p-4 md:p-0 mt-4   bg-neutral-secondary-soft md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-neutral-primary">

        @foreach ($navItems as $nav)

        <!-- ITEM -->
        <li class="relative group">

          @if ($nav->subNavigations->count())

          <!-- Parent With Dropdown -->
          <button type="button"
            class="flex items-center justify-between w-full py-2 px-3 text-heading hover:text-fg-brand md:hover:bg-transparent md:border-0 md:p-0 dropdown-toggle">

            {{ $nav->name }}

            <svg class="w-2.5 h-2.5 ms-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 4 4 4-4" />
            </svg>
          </button>

          <!-- Dropdown -->
          <div
            class="hidden absolute left-0 z-10 mt-2 font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-56 dropdown-menu">

            <ul class="py-2 text-sm text-gray-700">

              @foreach ($nav->subNavigations as $sub)

              <li>
                <a href="{{ url($nav->slug . '/' . $sub->slug) }}" class="block px-4 py-2 hover:bg-gray-100">
                  {{ $sub->sub_name }}
                </a>
              </li>

              @endforeach

            </ul>

          </div>

          @else

          <!-- Normal Link -->
          <a href="{{ url($nav->slug) }}"
            class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0">

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

    mobileButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // DROPDOWNS
    document.querySelectorAll('.dropdown-toggle').forEach(button => {

        button.addEventListener('click', function(e) {

            e.stopPropagation();

            const menu = this.nextElementSibling;

            // close others
            document.querySelectorAll('.dropdown-menu').forEach(drop => {
                if (drop !== menu) {
                    drop.classList.add('hidden');
                }
            });

            menu.classList.toggle('hidden');
        });

    });

    // close dropdowns outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(drop => {
            drop.classList.add('hidden');
        });
    });
</script>