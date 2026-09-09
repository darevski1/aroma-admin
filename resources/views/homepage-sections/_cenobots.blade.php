<section class="relative min-h-screen flex items-center bg-gray-100 overflow-hidden">

  <!-- 🔵 Background blobs -->
  <div class="absolute w-72 h-72 bg-blue-200/50 rounded-full blur-3xl top-20 left-10 animate-pulse"></div>
  <div class="absolute w-96 h-96 bg-blue-100/40 rounded-full blur-3xl bottom-10 right-10 animate-pulse"></div>

  <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">

    <!-- ✏️ LEFT CONTENT -->
    <div class="space-y-6 opacity-0 translate-y-10 animate-fadeIn">

      <h1 class="block font-medium text-foreground-inverse text-4xl sm:text-5xl md:text-6xl lg:text-7xl">
        Ceno<span class="text-[#5c59f5]">BOTS</span> Индустриски решенија за чистење
      </h1>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950 mb-6">
        Нудиме продажба и изнајмување на индустриски роботи <b>(Cenobots) </b> за автоматизација на производствени
        процеси.
        Зголемете ја продуктивноста, намалете ги трошоците и оптимизирајте ја вашата работа со модерни роботски
        решенија.
      </p>

      <div class="flex gap-4 pt-4">
        <a href="/services/cenobots" class="px-6 py-3 rounded-full bg-[#5c59f5] text-white hover:bg-black transition">
          Дознај повеќе
        </a>

        <a href="#tehnologija"
          class="px-6 py-3 rounded-full border border-[#5c59f5] text-[#5c59f5] hover:bg-black hover:text-white transition">
          Технологија
        </a>
      </div>

    </div>

    <!-- 🤖 RIGHT IMAGE -->
    <div class="flex justify-center relative">

      <!-- glow behind robot -->
      <div class="absolute w-[400px] h-[400px] bg-blue-300/30 blur-3xl rounded-full"></div>

      <img id="heroRobot" src="{{ asset('./../assets/images/cenobot.png') }}" alt="CenoBot Robot"
        class="relative z-10 max-h-[60vh] object-contain transition-transform duration-300 ease-out animate-float" />

    </div>

  </div>
</section>

<script>
  const robot = document.getElementById('heroRobot');
  if (robot) {
    window.addEventListener('scroll', () => {
      const scrollY = window.scrollY;
      let scale = 1 + scrollY * 0.0003;
      scale = Math.min(Math.max(scale, 1), 1.2);
      robot.style.transform = `scale(${scale})`;
    });
  }
</script>
