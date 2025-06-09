@include('admin_partials.header')

<body class="g-sidenav-show  bg-gray-100">
  @include('admin_partials.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    @include('admin_partials.navbar')
    <!-- End Navbar -->
    <div class="container-fluid py-2">
        @yield('content')
        @include('admin_partials.footer')
    </div>
  </main>
  @include('admin_partials.configurator')
    @include('admin_partials.scripts')
</body>

</html>