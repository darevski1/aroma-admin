<script src="https://unpkg.com/@studio-freight/lenis@1.0.14/bundled/lenis.min.js"></script>
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