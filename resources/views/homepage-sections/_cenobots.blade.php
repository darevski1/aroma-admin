{{--
Homepage · CenoBots promo block (redesigned).

Design reference: the hero of /services/cenobots — Archivo display type,
Barlow body type, brand blue #007be4 CTAs, dark "ink" gradient, generous
spacing and a strong left-content / right-product composition.

Content is unchanged: the original heading, lead paragraph and both
actions are reused. The "CenoBOTS" word of the heading is now rendered
with the real CenoBots logo (alt text keeps it in the accessible heading),
the lead sits under it as the hero lede, and the actions use the same
button language as the hero.

Assets: assets/images/logo/logocenotbot.png (logo, reversed to white with
a filter) and assets/images/cenobots/opt/sp50-cutout-800.webp (SP50
product cutout).

Everything is namespaced .cbh* and self-contained in this partial — no
other homepage section, and no global typography, colour or spacing, is
affected.
--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet"
  href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700&family=Barlow:wght@400;500;600&display=swap">

<style>
  /* ── CenoBots promo block · scope: .cbh* ─────────────────────────── */
  .cbh {
    --cbh-brand: #007be4;
    --cbh-brand-fg: #f9fcff;
    --cbh-fg: #f6f9fb;
    --cbh-muted: #9da5b0;
    --cbh-display: "Archivo", ui-sans-serif, system-ui, sans-serif;
    --cbh-sans: "Barlow", ui-sans-serif, system-ui, sans-serif;

    position: relative;
    isolation: isolate;
    overflow: hidden;
    display: flex;
    align-items: center;
    padding-block: clamp(4rem, 7vw, 6.5rem);
    color: var(--cbh-fg);
    background:
      radial-gradient(56rem 34rem at 80% 10%, rgba(0, 123, 228, 0.2), transparent 65%),
      radial-gradient(44rem 30rem at 4% 96%, rgba(19, 38, 67, 0.95), transparent 62%),
      linear-gradient(140deg, #050b17 0%, #101a2d 60%, #132643 100%);
  }

  /* full viewport height (svh keeps it right under mobile browser toolbars) */
  .cbh {
    min-height: 100vh;
    min-height: 100svh;
  }

  /* blueprint grid */
  .cbh::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background-image:
      repeating-linear-gradient(to right, rgba(255, 255, 255, 0.05) 0 1px, transparent 1px 72px),
      repeating-linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 0 1px, transparent 1px 72px);
    -webkit-mask-image: radial-gradient(90rem 44rem at 50% 50%, #000 18%, transparent 76%);
    mask-image: radial-gradient(90rem 44rem at 50% 50%, #000 18%, transparent 76%);
  }

  /* brand hairline along the top edge */
  .cbh::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    z-index: 0;
    pointer-events: none;
    background: linear-gradient(90deg, transparent, rgba(0, 123, 228, 0.6) 26%, rgba(255, 255, 255, 0.18) 58%, transparent);
  }

  .cbh__inner {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 80rem;
    margin-inline: auto;
    padding-inline: clamp(1.25rem, 4vw, 2rem);
    display: grid;
    gap: clamp(2.75rem, 5vw, 4.5rem);
  }

  @media (min-width: 1024px) {
    .cbh__inner {
      grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
      align-items: center;
    }
  }

  /* ── content column ─────────────────────────────────────────────── */
  .cbh__content {
    max-width: 38rem;
  }

  .cbh__rule {
    display: block;
    width: 3.5rem;
    height: 2px;
    margin-bottom: 1.75rem;
    background: linear-gradient(90deg, var(--cbh-brand), rgba(0, 123, 228, 0));
  }

  .cbh__title {
    margin: 0;
    font-family: var(--cbh-display);
    font-weight: normal;
    font-size: clamp(2rem, 1.15rem + 3.2vw, 3.5rem);
    line-height: 1.03;
    letter-spacing: -0.01em;
    text-transform: uppercase;
    color: #fff;
    text-wrap: balance;
  }

  .cbh__logo {
    display: block;
    width: fit-content;
    margin-bottom: 0.5rem;
  }

  .cbh__logo img {
    display: block;
    width: clamp(11.5rem, 15vw, 15.5rem);
    height: auto;
    /* the source lockup is brand blue on transparent — reverse it to white
       so it reads on the dark ink background, like the hero wordmark */
    filter: brightness(0) invert(1);
  }

  .cbh__title-text {
    display: block;
  }

  .cbh__lede {
    margin-top: 1.75rem;
    max-width: 34rem;
    font-family: var(--cbh-sans);
    font-size: clamp(1rem, 0.95rem + 0.3vw, 1.125rem);
    font-weight: 400;
    line-height: 1.65;
    color: var(--cbh-muted);
    text-wrap: pretty;
  }

  .cbh__lede b {
    color: #e7edf4;
    font-weight: 600;
  }

  .cbh__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 2.25rem;
  }

  /* buttons mirror .btn / .btn--brand / .btn--outline-light from the hero */
  .cbh-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.95rem 1.6rem;
    font-family: var(--cbh-display);
    font-size: 0.8125rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: 0.25rem;
    transition: background-color 0.2s, color 0.2s, border-color 0.2s,
      box-shadow 0.2s, transform 0.2s;
  }

  .cbh-btn:active {
    transform: translateY(1px);
  }

  .cbh-btn--brand {
    background: var(--cbh-brand);
    color: var(--cbh-brand-fg);
    box-shadow: 0 10px 30px -18px rgba(0, 123, 228, 0.95);
  }

  .cbh-btn--brand:hover {
    background: rgba(0, 123, 228, 0.88);
    box-shadow: 0 18px 34px -18px rgba(0, 123, 228, 0.95);
  }

  .cbh-btn--ghost {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.22);
    color: var(--cbh-fg);
  }

  .cbh-btn--ghost:hover {
    background: rgba(246, 249, 251, 0.12);
    border-color: rgba(255, 255, 255, 0.42);
  }

  .cbh a:focus-visible {
    outline: 2px solid var(--cbh-brand-fg);
    outline-offset: 3px;
  }

  /* ── product stage ──────────────────────────────────────────────── */
  .cbh__visual {
    position: relative;
    display: flex;
    justify-content: center;
  }

  .cbh__visual::before {
    content: "";
    position: absolute;
    inset: -6% -10%;
    pointer-events: none;
    background: radial-gradient(closest-side, rgba(0, 123, 228, 0.3), transparent 72%);
    filter: blur(18px);
  }

  .cbh__stage {
    position: relative;
    width: min(100%, 27rem);
    aspect-ratio: 5 / 6;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    /* fixed gutters: percentage padding would resolve against the column,
       not against the stage box itself */
    padding: 0 1.75rem 2.25rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.1rem;
    background:
      radial-gradient(120% 78% at 50% 108%, rgba(0, 123, 228, 0.24), transparent 62%),
      linear-gradient(160deg, rgba(255, 255, 255, 0.07), rgba(255, 255, 255, 0.02) 48%, rgba(255, 255, 255, 0.045));
    box-shadow:
      0 45px 90px -55px rgba(0, 0, 0, 0.95),
      inset 0 1px 0 rgba(255, 255, 255, 0.07);
  }

  .cbh__stage-grid {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image:
      repeating-linear-gradient(to right, rgba(255, 255, 255, 0.055) 0 1px, transparent 1px 44px),
      repeating-linear-gradient(to bottom, rgba(255, 255, 255, 0.055) 0 1px, transparent 1px 44px);
    -webkit-mask-image: radial-gradient(circle at 50% 42%, #000 34%, transparent 84%);
    mask-image: radial-gradient(circle at 50% 42%, #000 34%, transparent 84%);
  }

  .cbh__stage-ring {
    position: absolute;
    left: 50%;
    top: 44%;
    width: 74%;
    aspect-ratio: 1;
    transform: translate(-50%, -50%);
    border: 1px solid rgba(0, 123, 228, 0.38);
    border-radius: 50%;
    pointer-events: none;
  }

  .cbh__stage-ring::after {
    content: "";
    position: absolute;
    inset: 10%;
    border: 1px dashed rgba(255, 255, 255, 0.16);
    border-radius: 50%;
    animation: cbh-spin 44s linear infinite;
  }

  .cbh__stage-glow {
    position: absolute;
    left: 50%;
    top: 52%;
    width: 88%;
    height: 66%;
    transform: translate(-50%, -50%);
    pointer-events: none;
    background: radial-gradient(closest-side, rgba(0, 123, 228, 0.4), rgba(0, 123, 228, 0) 72%);
    filter: blur(6px);
  }

  .cbh__stage-floor {
    position: absolute;
    left: 50%;
    bottom: 7.5%;
    width: 66%;
    height: 4.5%;
    transform: translateX(-50%);
    pointer-events: none;
    background: radial-gradient(ellipse at center, rgba(0, 123, 228, 0.55), rgba(0, 123, 228, 0) 70%);
    filter: blur(4px);
  }

  .cbh__robot {
    position: relative;
    z-index: 2;
    display: block;
    width: 72%;
    height: auto;
    filter: drop-shadow(0 28px 38px rgba(0, 0, 0, 0.55));
    animation: cbh-float 7s ease-in-out infinite;
  }

  .cbh__tick {
    position: absolute;
    z-index: 3;
    width: 24px;
    height: 24px;
    border: 0 solid rgba(0, 123, 228, 0.85);
    pointer-events: none;
  }

  .cbh__tick--tl {
    top: 14px;
    left: 14px;
    border-top-width: 2px;
    border-left-width: 2px;
  }

  .cbh__tick--tr {
    top: 14px;
    right: 14px;
    border-top-width: 2px;
    border-right-width: 2px;
  }

  .cbh__tick--bl {
    bottom: 14px;
    left: 14px;
    border-bottom-width: 2px;
    border-left-width: 2px;
  }

  .cbh__tick--br {
    bottom: 14px;
    right: 14px;
    border-bottom-width: 2px;
    border-right-width: 2px;
  }

  @keyframes cbh-float {

    0%,
    100% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-10px);
    }
  }

  @keyframes cbh-spin {
    to {
      transform: rotate(360deg);
    }
  }

  @media (prefers-reduced-motion: reduce) {

    .cbh__robot,
    .cbh__stage-ring::after {
      animation: none;
    }
  }
</style>

<section class="cbh" aria-labelledby="cbh-title">
  <div class="cbh__inner">

    {{-- ── content ─────────────────────────────────────────────── --}}
    <div class="cbh__content">
      <span class="cbh__rule" aria-hidden="true"></span>

      <h1 class="cbh__title" id="cbh-title">
        <span class="cbh__logo">
          <img src="{{ asset('assets/images/logo/logocenotbot.png') }}" alt="CenoBots" width="689" height="230">
        </span>
        <span class="cbh__title-text">Индустриски решенија за чистење</span>
      </h1>

      <p class="cbh__lede">
        Нудиме продажба и изнајмување на индустриски роботи <b>(Cenobots) </b> за автоматизација на производствени
        процеси.
        Зголемете ја продуктивноста, намалете ги трошоците и оптимизирајте ја вашата работа со модерни роботски
        решенија.
      </p>

      <div class="cbh__actions">
        <a class="cbh-btn cbh-btn--brand" href="/services/cenobots">Дознај повеќе</a>
        <a class="cbh-btn cbh-btn--ghost" href="/services/cenobots#technology">Технологија</a>
      </div>
    </div>

    {{-- ── SP50 product stage ──────────────────────────────────── --}}
    <div class="cbh__visual">
      <div class="cbh__stage">
        <span class="cbh__stage-grid" aria-hidden="true"></span>
        <span class="cbh__stage-ring" aria-hidden="true"></span>
        <span class="cbh__stage-glow" aria-hidden="true"></span>
        <span class="cbh__stage-floor" aria-hidden="true"></span>

        <img class="cbh__robot" src="{{ asset('assets/images/cenobots/opt/sp50-cutout-800.webp') }}"
          alt="CenoBots SP50 — автономен робот за чистење" width="800" height="1253" loading="lazy" decoding="async">

        <span class="cbh__tick cbh__tick--tl" aria-hidden="true"></span>
        <span class="cbh__tick cbh__tick--tr" aria-hidden="true"></span>
        <span class="cbh__tick cbh__tick--bl" aria-hidden="true"></span>
        <span class="cbh__tick cbh__tick--br" aria-hidden="true"></span>
      </div>
    </div>

  </div>
</section>