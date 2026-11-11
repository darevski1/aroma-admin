/* ==========================================================================
   CenoBots — Static Template Script
   Vanilla ES5-safe, no dependencies, all listeners passive where possible.
   ========================================================================== */
(function () {
  'use strict';

  /* ---------------------------------------------------------------------
     1. Sticky header — solid background once scrolled past 12px
        (matches original threshold) or while the mobile menu is open.
     --------------------------------------------------------------------- */
  var header = document.getElementById('siteHeader');
  var SCROLL_THRESHOLD = 12;
  var menuOpen = false;

  function syncHeader() {
    if (!header) return;
    var stuck = window.pageYOffset > SCROLL_THRESHOLD;
    header.classList.toggle('is-stuck', stuck);
    header.classList.toggle('is-open', menuOpen);
  }

  /* ---------------------------------------------------------------------
     2. Mobile navigation toggle
     --------------------------------------------------------------------- */
  var toggle = document.getElementById('navToggle');
  var mobileNav = document.getElementById('mobileNav');

  function setMenu(open) {
    if (!toggle || !mobileNav) return;
    menuOpen = open;
    mobileNav.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Затвори мени' : 'Отвори мени');
    document.body.classList.toggle('is-locked', open);
    syncHeader();
  }

  if (toggle && mobileNav) {
    toggle.addEventListener('click', function () {
      setMenu(!menuOpen);
    });

    // Close when a link is chosen or when tapping outside the menu.
    mobileNav.addEventListener('click', function (e) {
      if (e.target.closest('a')) setMenu(false);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menuOpen) {
        setMenu(false);
        toggle.focus();
      }
    });
  }

  window.addEventListener('scroll', syncHeader, { passive: true });
  window.addEventListener('resize', function () {
    if (window.innerWidth >= 1280 && menuOpen) setMenu(false);
  });
  syncHeader();

  /* ---------------------------------------------------------------------
     3. Scroll reveal — animate elements as they enter the viewport
     --------------------------------------------------------------------- */
  var revealEls = document.querySelectorAll('.reveal');
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (revealEls.length) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      // Fallback / reduced-motion: show everything immediately.
      for (var i = 0; i < revealEls.length; i++) {
        revealEls[i].classList.add('is-visible');
      }
    } else {
      var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

      for (var j = 0; j < revealEls.length; j++) {
        revealObserver.observe(revealEls[j]);
      }
    }
  }

  /* ---------------------------------------------------------------------
     4. Active nav link — highlight the section currently in view
     --------------------------------------------------------------------- */
  var navLinks = Array.prototype.slice.call(
    document.querySelectorAll('.nav a, .mobile-nav a')
  );

  var sections = navLinks
    .map(function (link) {
      var id = link.getAttribute('href');
      if (!id || id.charAt(0) !== '#' || id.length < 2) return null;
      return { link: link, el: document.querySelector(id) };
    })
    .filter(function (item) { return item && item.el; });

  function setActive(id) {
    sections.forEach(function (item) {
      item.link.classList.toggle(
        'is-active',
        item.link.getAttribute('href') === id
      );
    });
  }

  if (sections.length && 'IntersectionObserver' in window) {
    var visible = {};
    var navObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        visible[entry.target.id] = entry.isIntersecting
          ? entry.intersectionRatio
          : 0;
      });

      // Pick whichever tracked section currently occupies the most viewport.
      var bestId = null;
      var bestRatio = 0;
      sections.forEach(function (item) {
        var ratio = visible[item.el.id] || 0;
        if (ratio > bestRatio) {
          bestRatio = ratio;
          bestId = item.el.id;
        }
      });

      setActive(bestId || '#top');
    }, { threshold: [0, 0.15, 0.4, 0.75], rootMargin: '-72px 0px -40% 0px' });

    sections.forEach(function (item) { navObserver.observe(item.el); });
  }

  /* ---------------------------------------------------------------------
     5. Footer year — keep the copyright date current
     --------------------------------------------------------------------- */
  var year = document.getElementById('year');
  if (year) year.textContent = String(new Date().getFullYear());

  /* ---------------------------------------------------------------------
     6. Projects slider — native scroll-snap (swipe on touch) with arrows,
        dots, keyboard and autoplay on top. Autoplay pauses on hover, focus
        and touch, while the tab is hidden, and via the pause button; it
        never starts for visitors who prefer reduced motion.
     --------------------------------------------------------------------- */
  var carousel = document.querySelector('[data-cb-projects]');
  if (carousel) {
    var viewport = carousel.querySelector('[data-cb-viewport]');
    var track = carousel.querySelector('[data-cb-track]');
    var dotsBox = carousel.querySelector('[data-cb-dots]');
    var toggleBtn = carousel.querySelector('[data-cb-toggle]');
    var slides = track.children;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var interval = parseInt(carousel.getAttribute('data-interval'), 10) || 6000;
    var positions = 1;
    var timer = null;
    var hovering = false;
    var userPaused = reduceMotion;

    var perView = function () {
      return parseInt(getComputedStyle(carousel).getPropertyValue('--cb-per'), 10) || 1;
    };
    var stepWidth = function () {
      if (slides.length < 2) return viewport.clientWidth;
      return slides[1].offsetLeft - slides[0].offsetLeft;
    };
    var maxScroll = function () { return Math.max(0, viewport.scrollWidth - viewport.clientWidth); };
    var currentIndex = function () {
      var step = stepWidth();
      return step ? Math.min(positions - 1, Math.max(0, Math.round(viewport.scrollLeft / step))) : 0;
    };
    var goTo = function (index) {
      var left = Math.min(index * stepWidth(), maxScroll());
      viewport.scrollTo({ left: left, behavior: reduceMotion ? 'auto' : 'smooth' });
    };
    var next = function () { goTo(currentIndex() >= positions - 1 ? 0 : currentIndex() + 1); };
    var prev = function () { goTo(currentIndex() <= 0 ? positions - 1 : currentIndex() - 1); };

    var paintDots = function () {
      var cur = currentIndex();
      for (var i = 0; i < dotsBox.children.length; i++) {
        var on = i === cur;
        dotsBox.children[i].classList.toggle('is-active', on);
        dotsBox.children[i].setAttribute('aria-current', on ? 'true' : 'false');
      }
    };

    var stop = function () { if (timer) { clearInterval(timer); timer = null; } };
    var start = function () {
      stop();
      if (userPaused || positions <= 1 || hovering || document.hidden) return;
      timer = setInterval(next, interval);
    };
    var paintToggle = function () {
      toggleBtn.setAttribute('aria-pressed', userPaused ? 'true' : 'false');
      toggleBtn.setAttribute('aria-label', userPaused ? 'Продолжи автоматско листање' : 'Паузирај автоматско листање');
      viewport.setAttribute('aria-live', userPaused ? 'polite' : 'off');
    };

    var build = function () {
      positions = Math.max(1, slides.length - perView() + 1);
      carousel.classList.toggle('is-static', positions <= 1);
      dotsBox.innerHTML = '';
      for (var i = 0; i < positions && positions > 1; i++) {
        (function (idx) {
          var dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'cb-projects__dot';
          dot.setAttribute('aria-label', 'Проект ' + (idx + 1));
          dot.addEventListener('click', function () { goTo(idx); start(); });
          dotsBox.appendChild(dot);
        })(i);
      }
      paintDots();
    };

    carousel.querySelector('[data-cb-next]').addEventListener('click', function () { next(); start(); });
    carousel.querySelector('[data-cb-prev]').addEventListener('click', function () { prev(); start(); });
    toggleBtn.addEventListener('click', function () { userPaused = !userPaused; paintToggle(); start(); });
    viewport.addEventListener('scroll', paintDots, { passive: true });
    viewport.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { e.preventDefault(); next(); start(); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); prev(); start(); }
    });
    carousel.addEventListener('mouseenter', function () { hovering = true; stop(); });
    carousel.addEventListener('mouseleave', function () { hovering = false; start(); });
    carousel.addEventListener('focusin', stop);
    carousel.addEventListener('focusout', start);
    carousel.addEventListener('touchstart', stop, { passive: true });
    carousel.addEventListener('touchend', function () { setTimeout(start, interval); }, { passive: true });
    document.addEventListener('visibilitychange', start);

    var resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () { build(); start(); }, 150);
    });

    build();
    paintToggle();
    start();
  }
})();
