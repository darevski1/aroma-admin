@extends('layouts.app')


@include('partials.page-seo', ['seoPage' => $page ?? null, 'seoTitle' => 'ITPlus | Сервис и продажба на принтери', 'seoDescription' => 'Сервис, продажба и изнајмување на принтери од ITPlus: мрежни принтери, одржување, конфигурација и техничка поддршка.'])
@section('content')
@php
    $s = $page->settings ?? [];

    $heroImage = $s['hero_image'] ?? asset('assets/images/printer-hero.jpg');
    $contactUrl = route('kontakt');
    $phone = '+389 71 354 822';
    $email = 'info@itplus.mk';

    // The three offers: overview cards and detail sections share this copy.
    $offers = [
        'rent' => [
            'anchor' => 'iznajmuvanje',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'card' => 2,
            'title' => 'Изнајмување',
            'text' => 'Флексибилни решенија за изнајмување уреди за печатење со месечна поддршка и одржување.',
            'lead' => 'Печатете без да инвестирате во опрема. Изнајмете уред што одговара на вашиот обем на печатење, а за поддршката и одржувањето се грижиме ние.',
            'points' => [
                'Флексибилни услови за изнајмување',
                'Месечна поддршка и одржување',
                'Решенија за компании, институции и деловни корисници',
                'Уреди од реномирани брендови',
            ],
            'image' => asset('assets/images/printer.jpg'),
            'alt' => 'Канцелариски мултифункциски принтер достапен за изнајмување',
        ],
        'sale' => [
            'anchor' => 'prodazba',
            'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
            'card' => 1,
            'title' => 'Продажба',
            'text' => 'Продажба на нови и професионални уреди за печатење од реномирани брендови за сите деловни потреби.',
            'lead' => 'Ви помагаме да го изберете вистинскиот уред за вашата работа, од мрежни принтери до мултифункциски системи, и да го пуштиме во употреба.',
            'points' => [
                'Нови и професионални уреди за печатење',
                'Мрежни принтери за деловна употреба',
                'Опрема од реномирани брендови',
                'Конфигурација и поврзување во вашата мрежа',
            ],
            'image' => asset('assets/images/printer.png'),
            'alt' => 'Нов професионален мултифункциски уред за печатење',
        ],
        'service' => [
            'anchor' => 'servis',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'card' => 3,
            'title' => 'Сервис',
            'text' => 'Брз и професионален сервис за сите уреди, дијагностика и техничка поддршка.',
            'lead' => 'Кога уредот ќе застане, работата не треба да чека. Дијагностицираме, поправаме и редовно одржуваме уреди за печатење од сите типови.',
            'points' => [
                'Дијагностика и поправка на уреди',
                'Сервис за сите типови уреди',
                'Редовно одржување и техничка поддршка',
                'Конфигурација на мрежни принтери',
            ],
            'image' => asset('assets/images/printer1.png'),
            'alt' => 'Илустрација на уред за печатење со документи',
            'contain' => true,
        ],
    ];
@endphp

<style>
  /* ── Printers page · scope: .prn* ─────────────────────────────────── */
  .prn {
    --prn-accent: #e04534;
    --prn-accent-strong: #ce3b2a;
    --prn-ink: #14171c;
    --prn-muted: #5b6470;
    --prn-line: #e6e8ec;
    --prn-soft: #f5f6f8;
    color: var(--prn-ink);
    overflow-x: clip;
  }
  .prn *, .prn *::before, .prn *::after { box-sizing: border-box; }
  .prn :where(h1, h2, h3, p, ul) { margin: 0; padding: 0; }
  .prn :where(h1, h2, h3) { text-wrap: balance; }
  .prn :where(ul) { list-style: none; }
  .prn img { display: block; max-width: 100%; }
  .prn a:focus-visible { outline: 3px solid var(--prn-accent); outline-offset: 3px; }

  .prn__shell {
    width: 100%;
    max-width: 80rem;
    margin-inline: auto;
    padding-inline: clamp(1.25rem, 4vw, 3rem);
  }

  .prn__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.45rem 1rem;
    border-radius: 9999px;
    font-size: 0.9rem;
    font-weight: 500;
    letter-spacing: 0.02em;
    background: var(--prn-soft);
    color: var(--prn-ink);
  }
  .prn__eyebrow::before {
    content: "";
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 9999px;
    background: var(--prn-accent);
  }

  .prn__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.55rem;
    padding: 0.85rem 1.7rem;
    border-radius: 9999px;
    border: 1px solid transparent;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.2;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.25s, color 0.25s, border-color 0.25s, transform 0.25s, box-shadow 0.25s;
  }
  .prn__btn svg { width: 1.15rem; height: 1.15rem; flex-shrink: 0; }
  .prn__btn--primary { background: var(--prn-accent-strong); color: #fff; box-shadow: 0 10px 28px -12px rgba(224, 69, 52, 0.9); }
  .prn__btn--primary:hover { background: #b9301f; transform: translateY(-2px); }
  .prn__btn--ghost { background: rgba(255, 255, 255, 0.08); color: #fff; border-color: rgba(255, 255, 255, 0.55); }
  .prn__btn--ghost:hover { background: #fff; color: var(--prn-ink); }
  .prn__btn--dark { background: #000; color: #fff; }
  .prn__btn--dark:hover { background: #2a2d33; transform: translateY(-2px); }

  /* ── hero ──────────────────────────────────────────────────────────── */
  .prn-hero {
    position: relative;
    isolation: isolate;
    display: flex;
    align-items: center;
    min-height: min(46rem, 86vh);
    padding-block: clamp(4rem, 8vw, 7rem) clamp(7rem, 11vw, 9rem);
    color: #fff;
    background: #0d1118;
  }
  .prn-hero__bg {
    position: absolute;
    inset: 0;
    z-index: -2;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: 70% center;
  }
  .prn-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background:
      linear-gradient(90deg, rgba(9, 12, 18, 0.92) 0%, rgba(9, 12, 18, 0.78) 42%, rgba(9, 12, 18, 0.25) 100%),
      linear-gradient(0deg, rgba(9, 12, 18, 0.55), transparent 40%);
  }
  .prn-hero__content { max-width: 40rem; }
  .prn-hero .prn__eyebrow { background: rgba(255, 255, 255, 0.12); color: #fff; backdrop-filter: blur(6px); }
  .prn-hero h1 {
    margin-top: 1.4rem;
    font-size: clamp(2.4rem, 5.6vw, 4.6rem);
    font-weight: 500;
    line-height: 1.06;
    letter-spacing: -0.03em;
  }
  .prn-hero h1 span { color: #ff6b5a; }
  .prn-hero__lead {
    margin-top: 1.5rem;
    max-width: 34rem;
    font-size: clamp(1.02rem, 1.4vw, 1.2rem);
    line-height: 1.75;
    color: rgba(255, 255, 255, 0.84);
  }
  .prn-hero__actions { display: flex; flex-wrap: wrap; gap: 0.9rem; margin-top: 2.2rem; }

  .prn-stats {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    transform: translateY(50%);
    z-index: 2;
  }
  .prn-stats__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    background: #fff;
    border: 1px solid var(--prn-line);
    border-radius: 1.25rem;
    box-shadow: 0 30px 60px -30px rgba(10, 14, 22, 0.45);
  }
  .prn-stats__item { padding: clamp(1rem, 2.2vw, 1.7rem) 1rem; text-align: center; }
  .prn-stats__item + .prn-stats__item { border-left: 1px solid var(--prn-line); }
  .prn-stats__value { font-size: clamp(1.6rem, 3.4vw, 2.5rem); font-weight: 500; color: var(--prn-accent); line-height: 1.1; }
  .prn-stats__label { margin-top: 0.35rem; font-size: clamp(0.78rem, 1.3vw, 0.95rem); color: var(--prn-muted); }

  /* ── section scaffolding ───────────────────────────────────────────── */
  .prn-section { padding-block: clamp(4rem, 8vw, 7rem); }
  .prn-section--soft { background: var(--prn-soft); }
  .prn-head { max-width: 46rem; margin-inline: auto; text-align: center; }
  .prn-head h2 { margin-top: 1.1rem; font-size: clamp(1.9rem, 3.8vw, 3rem); font-weight: 500; line-height: 1.15; letter-spacing: -0.02em; }
  .prn-head p { margin-top: 1.1rem; font-size: 1.08rem; line-height: 1.75; color: var(--prn-muted); }

  /* ── brands ────────────────────────────────────────────────────────── */
  .prn-brands { padding-top: clamp(6rem, 10vw, 8.5rem); padding-bottom: clamp(2rem, 4vw, 3rem); }
  .prn-brands__label { text-align: center; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--prn-muted); }
  .prn-brands__row { display: grid; grid-template-columns: repeat(3, 1fr); gap: clamp(1rem, 3vw, 2rem); margin-top: 1.5rem; }
  .prn-brands__item {
    display: flex;
    align-items: center;
    justify-content: center;
    height: clamp(5rem, 11vw, 7.5rem);
    padding: 1rem 1.5rem;
    border: 1px solid var(--prn-line);
    border-radius: 1rem;
    background: #fff;
  }
  .prn-brands__item img { max-height: 3.2rem; max-width: 80%; width: auto; object-fit: contain; }

  /* ── overview cards ────────────────────────────────────────────────── */
  .prn-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: clamp(1rem, 2.4vw, 2rem); margin-top: clamp(2.5rem, 5vw, 4rem); }
  .prn-card {
    display: flex;
    flex-direction: column;
    padding: clamp(1.5rem, 2.6vw, 2.4rem);
    border: 1px solid var(--prn-line);
    border-radius: 1.25rem;
    background: #fff;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
  }
  .prn-card:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -34px rgba(10, 14, 22, 0.4); border-color: transparent; }
  .prn-card__icon {
    display: grid;
    place-items: center;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1rem;
    background: #fdecea;
    color: var(--prn-accent-strong);
  }
  .prn-card__icon svg { width: 1.7rem; height: 1.7rem; }
  .prn-card h3 { margin-top: 1.5rem; font-size: 1.6rem; font-weight: 500; letter-spacing: -0.01em; }
  .prn-card p { margin-top: 0.9rem; line-height: 1.7; color: var(--prn-muted); flex: 1; }
  .prn-card__more { display: inline-flex; align-items: center; gap: 0.4rem; margin-top: 1.4rem; font-weight: 600; color: var(--prn-accent-strong); }
  .prn-card__more svg { width: 1rem; height: 1rem; transition: transform 0.25s; }
  .prn-card:hover .prn-card__more svg { transform: translateX(4px); }

  /* ── detail sections ───────────────────────────────────────────────── */
  .prn-split { display: grid; grid-template-columns: 1fr 1fr; gap: clamp(2rem, 6vw, 5.5rem); align-items: center; }
  .prn-split--flip .prn-split__media { order: 2; }
  .prn-split__media { position: relative; }
  .prn-split__media::before {
    content: "";
    position: absolute;
    inset: 6% -4% -6% 4%;
    z-index: 0;
    border-radius: 2rem;
    background: var(--prn-soft);
  }
  .prn-section--soft .prn-split__media::before { background: #e9ebef; }
  .prn-split--flip .prn-split__media::before { inset: 6% 4% -6% -4%; }
  .prn-split__photo {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 5 / 4;
    object-fit: cover;
    border-radius: 1.5rem;
    box-shadow: 0 40px 70px -40px rgba(10, 14, 22, 0.6);
  }
  .prn-split__photo--contain { object-fit: contain; padding: 1.5rem; background: #fff; }
  .prn-split h2 { margin-top: 1.1rem; font-size: clamp(1.9rem, 3.6vw, 2.8rem); font-weight: 500; line-height: 1.12; letter-spacing: -0.02em; }
  .prn-split__lead { margin-top: 1.2rem; font-size: 1.08rem; line-height: 1.8; color: var(--prn-muted); }
  .prn-points { margin-top: 1.8rem; display: grid; gap: 0.85rem; }
  .prn-points li { display: flex; align-items: flex-start; gap: 0.8rem; font-size: 1.03rem; line-height: 1.5; }
  .prn-points svg {
    flex-shrink: 0;
    width: 1.45rem;
    height: 1.45rem;
    padding: 0.28rem;
    margin-top: 0.05rem;
    border-radius: 9999px;
    background: var(--prn-accent-strong);
    color: #fff;
  }
  .prn-split .prn__btn { margin-top: 2.2rem; }

  /* ── CTA ───────────────────────────────────────────────────────────── */
  .prn-cta { padding-block: clamp(3rem, 6vw, 5rem) clamp(4rem, 8vw, 7rem); }
  .prn-cta__box {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: clamp(2.5rem, 6vw, 5rem) clamp(1.5rem, 5vw, 4rem);
    border-radius: clamp(1.25rem, 3vw, 2rem);
    text-align: center;
    color: #fff;
    background:
      radial-gradient(40rem 22rem at 85% 0%, rgba(224, 69, 52, 0.45), transparent 65%),
      radial-gradient(36rem 20rem at 0% 100%, rgba(224, 69, 52, 0.18), transparent 60%),
      linear-gradient(140deg, #0d1118 0%, #1a1f29 100%);
  }
  .prn-cta__box::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background-image:
      repeating-linear-gradient(to right, rgba(255, 255, 255, 0.045) 0 1px, transparent 1px 64px),
      repeating-linear-gradient(to bottom, rgba(255, 255, 255, 0.045) 0 1px, transparent 1px 64px);
    -webkit-mask-image: radial-gradient(34rem 18rem at 50% 50%, #000 20%, transparent 80%);
    mask-image: radial-gradient(34rem 18rem at 50% 50%, #000 20%, transparent 80%);
  }
  .prn-cta h2 { font-size: clamp(2rem, 4.6vw, 3.6rem); font-weight: 500; letter-spacing: -0.02em; line-height: 1.1; }
  .prn-cta p { max-width: 36rem; margin: 1.2rem auto 0; font-size: 1.1rem; line-height: 1.75; color: rgba(255, 255, 255, 0.8); }
  .prn-cta__actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.9rem; margin-top: 2.2rem; }
  .prn-cta__contact { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.6rem 2rem; margin-top: 1.8rem; font-size: 0.98rem; }
  .prn-cta__contact a { display: inline-flex; align-items: center; gap: 0.5rem; color: rgba(255, 255, 255, 0.85); text-decoration: none; }
  .prn-cta__contact a:hover { color: #fff; text-decoration: underline; }
  .prn-cta__contact svg { width: 1.1rem; height: 1.1rem; }

  /* ── responsive ────────────────────────────────────────────────────── */
  @media (max-width: 960px) {
    .prn-cards { grid-template-columns: 1fr; }
    .prn-split { grid-template-columns: 1fr; }
    .prn-split--flip .prn-split__media { order: 0; }
    .prn-split__media::before { inset: 5% 2% -4% 2%; }
    .prn-split--flip .prn-split__media::before { inset: 5% 2% -4% 2%; }
    .prn-hero::before {
      background: linear-gradient(0deg, rgba(9, 12, 18, 0.9) 0%, rgba(9, 12, 18, 0.7) 55%, rgba(9, 12, 18, 0.45) 100%);
    }
    .prn-hero__bg { object-position: 62% center; }
  }
  @media (max-width: 600px) {
    .prn-hero { padding-bottom: 8.5rem; }
    .prn-hero__actions .prn__btn, .prn-cta__actions .prn__btn { width: 100%; }
    .prn-brands__row { grid-template-columns: 1fr; }
    .prn-brands__item { height: 5rem; }
    .prn-stats__item { padding-inline: 0.4rem; }
    .prn-split .prn__btn { width: 100%; }
  }
  @media (prefers-reduced-motion: reduce) {
    .prn *, .prn *::before, .prn *::after { transition: none !important; }
    .prn-card:hover, .prn__btn:hover { transform: none; }
  }
</style>

<div class="prn">

  {{-- ── Hero ───────────────────────────────────────────────────────── --}}
  <section class="prn-hero" aria-labelledby="prn-title">
    <img class="prn-hero__bg" src="{{ $heroImage }}" alt="" fetchpriority="high" decoding="async">

    <div class="prn__shell">
      <div class="prn-hero__content">
        <span class="prn__eyebrow">{{ $s['hero_eyebrow'] ?? 'Уреди за печатење' }}</span>

        <h1 id="prn-title">
          {{ $s['hero_heading_1'] ?? 'Продажба,' }}
          {{ $s['hero_heading_2'] ?? 'Изнајмување' }}
          <span>{{ $s['hero_heading_3'] ?? 'Принтери' }}</span>
        </h1>

        <p class="prn-hero__lead">
          {{ preg_replace('/\s*\R\s*/u', ' ', $s['hero_description'] ?? "Нудиме професионални решенија за продажба, изнајмување\nи сервисирање на printer уреди за компании, институции\nи деловни корисници. Брза поддршка, квалитетна опрема\nи сигурна техничка услуга на едно место.") }}
        </p>

        <div class="prn-hero__actions">
          <a href="{{ $contactUrl }}" class="prn__btn prn__btn--primary">
            Побарај понуда
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
          </a>
          <a href="#uslugi" class="prn__btn prn__btn--ghost">Наши услуги</a>
        </div>
      </div>
    </div>

    <div class="prn-stats">
      <div class="prn__shell">
        <div class="prn-stats__grid">
          <div class="prn-stats__item">
            <div class="prn-stats__value">{{ $s['stat_1_value'] ?? '15+' }}</div>
            <div class="prn-stats__label">{{ $s['stat_1_label'] ?? 'Години Искуство' }}</div>
          </div>
          <div class="prn-stats__item">
            <div class="prn-stats__value">{{ $s['stat_2_value'] ?? '24/7' }}</div>
            <div class="prn-stats__label">{{ $s['stat_2_label'] ?? 'Техничка Поддршка' }}</div>
          </div>
          <div class="prn-stats__item">
            <div class="prn-stats__value">{{ $s['stat_3_value'] ?? '100+' }}</div>
            <div class="prn-stats__label">{{ $s['stat_3_label'] ?? 'Задоволни Клиенти' }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Brands ─────────────────────────────────────────────────────── --}}
  <section class="prn-brands" aria-label="Брендови">
    <div class="prn__shell">
      <p class="prn-brands__label">Работиме со водечки брендови</p>
      <div class="prn-brands__row">
        <div class="prn-brands__item">
          <img src="{{ $s['logo_1_image'] ?? asset('assets/images/logo/Canon_ide_p-tcLS_0.svg') }}" alt="{{ $s['logo_1_alt'] ?? 'Canon Logo' }}" loading="lazy">
        </div>
        <div class="prn-brands__item">
          <img src="{{ $s['logo_2_image'] ?? asset('assets/images/logo/Ricoh_Australia_Logo_0.svg') }}" alt="{{ $s['logo_2_alt'] ?? 'Ricoh Logo' }}" loading="lazy">
        </div>
        <div class="prn-brands__item">
          <img src="{{ $s['logo_3_image'] ?? asset('assets/images/logo/HP_Logo_0.svg') }}" alt="{{ $s['logo_3_alt'] ?? 'Hp Logo' }}" loading="lazy">
        </div>
      </div>
    </div>
  </section>

  {{-- ── Services overview ──────────────────────────────────────────── --}}
  <section class="prn-section" id="uslugi" aria-labelledby="prn-services-title">
    <div class="prn__shell">
      <div class="prn-head">
        <span class="prn__eyebrow">{{ $s['services_eyebrow'] ?? 'Наши Услуги' }}</span>
        <h2 id="prn-services-title">{{ $s['services_heading'] ?? 'Комплетни Решенија за Вашиот Бизнис' }}</h2>
        <p>{{ preg_replace('/\s*\R\s*/u', ' ', $s['services_paragraph'] ?? "Обезбедуваме професионални услуги за продажба,\nизнајмување и одржување на системи\nспоред потребите на вашиот бизнис.") }}</p>
      </div>

      <div class="prn-cards">
        @foreach ($offers as $offer)
          <a class="prn-card" href="#{{ $offer['anchor'] }}">
            <span class="prn-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $offer['icon'] }}" /></svg>
            </span>
            <h3>{{ $s['svc_card_' . $offer['card'] . '_title'] ?? $offer['title'] }}</h3>
            <p>{{ $s['svc_card_' . $offer['card'] . '_description'] ?? $offer['text'] }}</p>
            <span class="prn-card__more">
              Дознај повеќе
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </span>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ── Rental · Sale · Service ────────────────────────────────────── --}}
  @foreach (['rent', 'sale', 'service'] as $i => $key)
    @php $offer = $offers[$key]; @endphp
    <section class="prn-section {{ $i % 2 === 0 ? 'prn-section--soft' : '' }}" id="{{ $offer['anchor'] }}" aria-labelledby="prn-{{ $key }}-title">
      <div class="prn__shell">
        <div class="prn-split {{ $i % 2 === 1 ? 'prn-split--flip' : '' }}">
          <div class="prn-split__media">
            <img class="prn-split__photo {{ !empty($offer['contain']) ? 'prn-split__photo--contain' : '' }}" src="{{ $offer['image'] }}" alt="{{ $offer['alt'] }}" loading="lazy" decoding="async">
          </div>

          <div>
            <span class="prn__eyebrow">{{ $offer['title'] }}</span>
            <h2 id="prn-{{ $key }}-title">{{ $offer['title'] }} на принтери</h2>
            <p class="prn-split__lead">{{ $offer['lead'] }}</p>

            <ul class="prn-points">
              @foreach ($offer['points'] as $point)
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                  <span>{{ $point }}</span>
                </li>
              @endforeach
            </ul>

            <a href="{{ $contactUrl }}" class="prn__btn prn__btn--dark">Побарај понуда</a>
          </div>
        </div>
      </div>
    </section>
  @endforeach

  {{-- ── Call to action ─────────────────────────────────────────────── --}}
  <section class="prn-cta" id="contact" aria-labelledby="prn-cta-title">
    <div class="prn__shell">
      <div class="prn-cta__box">
        <h2 id="prn-cta-title">Побарај понуда</h2>
        <p>Кажете ни што ви треба: продажба, изнајмување или сервис на принтери. Ќе ви се јавиме со понуда прилагодена на вашиот бизнис.</p>

        <div class="prn-cta__actions">
          <a href="{{ $contactUrl }}" class="prn__btn prn__btn--primary">
            Испрати барање
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
          </a>
        </div>

        <div class="prn-cta__contact">
          <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
            {{ $phone }}
          </a>
          <a href="mailto:{{ $email }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            {{ $email }}
          </a>
        </div>
      </div>
    </div>
  </section>

</div>
@endsection
