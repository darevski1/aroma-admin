<script src="https://unpkg.com/@studio-freight/lenis@1.0.14/bundled/lenis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
<!-- Scroll Zoom In/Out Script -->
<script>
  const heroImage = document.getElementById('hero-image');
  const heroSection = document.getElementById('hero-section');

  window.addEventListener('scroll', () => {
    const rect = heroSection.getBoundingClientRect();
    const windowHeight = window.innerHeight;

    // Calculate percentage the section is in view
    let visible = 1 - (rect.top / windowHeight);
    visible = Math.min(Math.max(visible, 0), 4); // clamp 0–1

    // Zoom from 1 -> 1.2 -> 1 depending on scroll
    let scale = 1 + Math.sin(visible * Math.PI) * 0.4 // zoom in then out
    heroImage.style.transform = `scale(${scale})`;
  });
</script>




<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  new Swiper('.swiper',{loop:true,autoplay:{delay:0,disableOnInteraction:false},speed:3000,slidesPerView:'auto',spaceBetween:24,allowTouchMove:true,freeMode:true});
</script>

<script>
  const navbar = document.getElementById('navbar');

  window.addEventListener('scroll', () => {
  if (window.scrollY > 50) {
  navbar.classList.add('bg-white/60', 'backdrop-blur-xl', 'shadow-lg');
} else {
  navbar.classList.remove('bg-white/60', 'backdrop-blur-xl', 'shadow-lg');
}
  });

   
</script>