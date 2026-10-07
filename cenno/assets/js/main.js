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
})();
