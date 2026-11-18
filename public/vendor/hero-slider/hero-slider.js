/* Hero Slider (Admin → Hero Sliders). Plain JS, no dependencies.
   Each [data-hero-slider] element is set up once. */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function init(root) {
    if (root.__hs) return;
    root.__hs = true;

    var track = root.querySelector('.hs__track');
    var slides = Array.prototype.slice.call(root.querySelectorAll('.hs__slide'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hs-dot]'));
    var isSlide = root.classList.contains('hs--slide');
    var n = slides.length;
    if (n < 2) return;

    var i = 0;
    var timer = null;
    var autoplay = root.dataset.autoplay === '1' && !reduceMotion;
    var interval = Math.max(1500, parseInt(root.dataset.interval, 10) || 6000);
    var hovering = false, visible = true, focused = false;

    // Hidden slides use lazy images; start loading the next one ahead of time.
    function warm(k) {
      var img = slides[(k + n) % n].querySelector('img[loading="lazy"]');
      if (img) img.loading = 'eager';
    }

    function show(k) {
      i = (k + n) % n;
      slides.forEach(function (s, j) {
        var on = j === i;
        s.classList.toggle('is-active', on);
        if (on) { s.removeAttribute('aria-hidden'); s.removeAttribute('inert'); }
        else { s.setAttribute('aria-hidden', 'true'); s.setAttribute('inert', ''); }
      });
      dots.forEach(function (d, j) {
        d.classList.toggle('is-active', j === i);
        if (j === i) d.setAttribute('aria-current', 'true'); else d.removeAttribute('aria-current');
      });
      if (isSlide) track.style.transform = 'translate3d(' + (-100 * i) + '%,0,0)';
      warm(i + 1);
    }

    function stop() { if (timer) { clearInterval(timer); timer = null; } }
    function start() {
      stop();
      if (!autoplay || hovering || focused || !visible || document.hidden) return;
      timer = setInterval(function () { show(i + 1); }, interval);
    }
    function go(k) { warm(k); show(k); start(); }

    var prev = root.querySelector('[data-hs-prev]');
    var next = root.querySelector('[data-hs-next]');
    if (prev) prev.addEventListener('click', function () { go(i - 1); });
    if (next) next.addEventListener('click', function () { go(i + 1); });
    dots.forEach(function (d) {
      d.addEventListener('click', function () { go(parseInt(d.dataset.hsDot, 10)); });
    });

    if (root.dataset.pauseHover === '1') {
      root.addEventListener('mouseenter', function () { hovering = true; stop(); });
      root.addEventListener('mouseleave', function () { hovering = false; start(); });
    }

    // Keyboard users: no movement while focus is inside; arrow keys change slide.
    root.addEventListener('focusin', function () { focused = true; stop(); });
    root.addEventListener('focusout', function (e) {
      if (!root.contains(e.relatedTarget)) { focused = false; start(); }
    });
    root.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') go(i - 1);
      else if (e.key === 'ArrowRight') go(i + 1);
    });

    // Touch / pen swipe.
    var x0 = null, y0 = 0, dx = 0;
    root.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'mouse' || e.target.closest('button')) return;
      x0 = e.clientX; y0 = e.clientY; dx = 0;
    });
    root.addEventListener('pointermove', function (e) {
      if (x0 === null) return;
      dx = e.clientX - x0;
      if (Math.abs(e.clientY - y0) > Math.abs(dx) + 10) x0 = null; // vertical scroll wins
    });
    root.addEventListener('pointerup', function () {
      if (x0 !== null && Math.abs(dx) > 45) go(dx < 0 ? i + 1 : i - 1);
      x0 = null; dx = 0;
    });
    root.addEventListener('pointercancel', function () { x0 = null; });

    // Only advance while on screen and the tab is visible.
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        visible = entries[0].isIntersecting;
        if (visible) start(); else stop();
      }, { threshold: 0.2 }).observe(root);
    }
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) stop(); else start();
    });

    show(0);
    start();
  }

  // 100vw includes the vertical scrollbar; expose its width for .hs--full.
  function measureScrollbar() {
    var w = window.innerWidth - document.documentElement.clientWidth;
    document.documentElement.style.setProperty('--hs-sbw', Math.max(0, w) + 'px');
  }

  // Full-width sliders: line up exactly with the screen edges, whatever the
  // (possibly off-centre) column they sit in. CSS gives a close first guess.
  function stretch() {
    var vw = document.documentElement.clientWidth;
    document.querySelectorAll('.hs--full').forEach(function (el) {
      el.style.marginLeft = '0px';
      el.style.width = vw + 'px';
      var left = el.getBoundingClientRect().left;
      el.style.marginLeft = (-left) + 'px';
    });
  }

  function boot() {
    measureScrollbar();
    stretch();
    window.addEventListener('resize', function () { measureScrollbar(); stretch(); });
    window.addEventListener('load', stretch);
    document.querySelectorAll('[data-hero-slider]').forEach(init);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
})();
