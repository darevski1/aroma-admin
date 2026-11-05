@extends('layouts.app')


@include('partials.page-seo', ['seoPage' => $page ?? null, 'seoTitle' => 'ITPlus | Мрежни инсталации', 'seoDescription' => 'Мрежни инсталации и одржување на мрежи од ITPlus: проектирање, структурно каблирање, конфигурација, Firewall, VPN, Wi-Fi и мониторинг на компјутерски мрежи.'])
@section('content')
@php
    $s = $page->settings ?? [];
    $contactUrl = route('kontakt');
    $phone = '+389 71 354 822';
    $email = 'info@itplus.mk';

    $services = [
        [
            'id' => 'proektiranje',
            'letter' => 'A',
            'title' => 'Проектирање и поставување компјутерски мрежи',
            'text' => 'Од анализа на постојната инфраструктура до сертифицирано структурно каблирање.',
            'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
            'items' => [
                'Анализа на постојната мрежна инфраструктура',
                'Проектирање LAN и WAN мрежи',
                'Поставување структурно каблирање',
                'UTP, FTP и оптички кабли',
                'Rack кабинети и patch панели',
                'Организација и означување на каблите',
                'Инсталација на мрежни приклучници',
                'Тестирање и сертификација на каблирање',
            ],
        ],
        [
            'id' => 'oprema',
            'letter' => 'B',
            'title' => 'Конфигурација и одржување на мрежна опрема',
            'text' => 'Switch уреди, VLAN сегментација и мрежа што работи без застои.',
            'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
            'items' => [
                'Инсталација и конфигурација на Switch уреди',
                'Managed и Unmanaged Switches',
                'VLAN конфигурација и сегментација',
                'Layer 2 и Layer 3 мрежна инфраструктура',
                'Network monitoring',
                'Оптимизација на мрежниот сообраќај',
                'Дијагностика и отстранување проблеми',
            ],
        ],
        [
            'id' => 'firewall',
            'letter' => 'C',
            'title' => 'Firewall и мрежна безбедност',
            'text' => 'Заштитете ја локалната мрежа и контролирајте кој и што има пристап.',
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'items' => [
                'Инсталација и конфигурација на Firewall',
                'Конфигурација на безбедносни правила',
                'Контрола и филтрирање на мрежен сообраќај',
                'VPN пристап за далечинска работа',
                'Site-to-Site VPN',
                'Заштита на локалната мрежа',
                'Сегментација на мрежа и изолација на уреди',
                'Контрола на пристап и безбедносен мониторинг',
            ],
        ],
        [
            'id' => 'ruteri',
            'letter' => 'D',
            'title' => 'Рутери и интернет конекција',
            'text' => 'Стабилна и брза интернет конекција со правилно рутирање.',
            'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
            'items' => [
                'Инсталација и конфигурација на рутери',
                'MikroTik, корпоративни и други мрежни решенија',
                'WAN и LAN конфигурација',
                'Static и Dynamic Routing',
                'DHCP и DNS конфигурација',
                'NAT и Port Forwarding',
                'Load Balancing и Failover',
                'Оптимизација на интернет конекцијата',
            ],
        ],
        [
            'id' => 'wifi',
            'letter' => 'E',
            'title' => 'Wi-Fi и Access Point решенија',
            'text' => 'Професионална безжична мрежа со целосна покриеност на просторот.',
            'icon' => 'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0',
            'items' => [
                'Професионална Wi-Fi инфраструктура',
                'Инсталација и конфигурација на Access Points',
                'Корпоративни Wi-Fi мрежи',
                'Wi-Fi покриеност во канцеларии, хотели и деловни објекти',
                'Анализа на Wi-Fi сигнал и интерференции',
                'Wi-Fi Roaming',
                'Guest Wi-Fi мрежи',
                'Централизирано управување со Access Points',
            ],
        ],
        [
            'id' => 'vpn',
            'letter' => 'F',
            'title' => 'VPN и далечински пристап',
            'text' => 'Безбеден пристап до корпоративната мрежа од каде било.',
            'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
            'items' => [
                'Безбеден пристап до корпоративната мрежа од далечина',
                'VPN сервери и VPN клиенти',
                'Поврзување на повеќе деловни локации',
                'Remote Access VPN',
                'Управување со кориснички пристапи',
                'Безбедна комуникација меѓу филијали',
            ],
        ],
        [
            'id' => 'monitoring',
            'letter' => 'G',
            'title' => 'Мониторинг и редовно одржување',
            'text' => 'Проблемите ги откриваме пред да ја запрат вашата работа.',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'wide' => true,
            'items' => [
                'Континуиран мониторинг на мрежната инфраструктура',
                'Следење на достапноста на мрежните уреди',
                'Детекција на мрежни проблеми',
                'Превентивно одржување',
                'Firmware и софтверски надградби',
                'Анализа на перформанси',
                'Техничка поддршка и интервенции',
            ],
        ],
    ];

    $layers = [
        ['01', 'Структурно каблирање', 'UTP, FTP и оптика, rack кабинети и patch панели.'],
        ['02', 'Мрежна опрема', 'Switch и рутер уреди, VLAN и рутирање.'],
        ['03', 'Безбедност', 'Firewall, VPN и контрола на пристап.'],
        ['04', 'Безжична мрежа', 'Access Points, roaming и Guest Wi-Fi.'],
        ['05', 'Мониторинг', 'Следење, превенција и техничка поддршка.'],
    ];

    $steps = [
        ['Анализа', 'Ја проверуваме постојната мрежна инфраструктура и вашите потреби.'],
        ['Проектирање', 'Правиме план за LAN, WAN, безбедност и безжична покриеност.'],
        ['Инсталација', 'Каблирање, поставување и конфигурација на опремата, со тестирање.'],
        ['Одржување', 'Мониторинг, надградби и техничка поддршка по потреба.'],
    ];
@endphp

<style>
  /* ── Network page · scope: .net* ──────────────────────────────────── */
  .net {
    --net-accent: #e04534;
    --net-accent-strong: #ce3b2a;
    --net-glow: #38bdf8;
    --net-ink: #111827;
    --net-muted: #5b6573;
    --net-line: #e5e8ee;
    --net-soft: #f4f6f9;
    --net-navy: #0a101c;
    color: var(--net-ink);
    overflow-x: clip;
  }
  .net *, .net *::before, .net *::after { box-sizing: border-box; }
  .net :where(h1, h2, h3, p, ul, ol) { margin: 0; padding: 0; }
  .net :where(h1, h2, h3) { text-wrap: balance; }
  .net :where(ul, ol) { list-style: none; }
  .net a:focus-visible, .net summary:focus-visible { outline: 3px solid var(--net-accent); outline-offset: 3px; }

  .net__shell { width: 100%; max-width: 80rem; margin-inline: auto; padding-inline: clamp(1.25rem, 4vw, 3rem); }

  .net__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.45rem 1rem;
    border-radius: 9999px;
    font-size: 0.9rem;
    font-weight: 500;
    letter-spacing: 0.02em;
    background: var(--net-soft);
  }
  .net__eyebrow::before { content: ""; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: var(--net-accent); }

  .net__btn {
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
    transition: background-color 0.25s, color 0.25s, border-color 0.25s, transform 0.25s;
  }
  .net__btn svg { width: 1.15rem; height: 1.15rem; flex-shrink: 0; }
  .net__btn--primary { background: var(--net-accent-strong); color: #fff; box-shadow: 0 10px 28px -12px rgba(224, 69, 52, 0.9); }
  .net__btn--primary:hover { background: #b9301f; transform: translateY(-2px); }
  .net__btn--ghost { background: rgba(255, 255, 255, 0.08); color: #fff; border-color: rgba(255, 255, 255, 0.55); }
  .net__btn--ghost:hover { background: #fff; color: var(--net-ink); }

  /* ── hero ──────────────────────────────────────────────────────────── */
  .net-hero {
    position: relative;
    isolation: isolate;
    display: flex;
    align-items: center;
    min-height: min(46rem, 88vh);
    padding-block: clamp(4rem, 8vw, 7rem);
    color: #fff;
    background: var(--net-navy);
  }
  .net-hero__bg { position: absolute; inset: 0; z-index: -3; width: 100%; height: 100%; object-fit: cover; object-position: 65% center; }
  .net-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -2;
    background:
      linear-gradient(95deg, rgba(8, 12, 22, 0.96) 0%, rgba(8, 12, 22, 0.86) 45%, rgba(8, 12, 22, 0.35) 100%),
      radial-gradient(40rem 26rem at 88% 12%, rgba(56, 189, 248, 0.22), transparent 65%);
  }
  .net-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background-image: radial-gradient(rgba(255, 255, 255, 0.16) 1px, transparent 1.4px);
    background-size: 30px 30px;
    -webkit-mask-image: linear-gradient(90deg, #000 0%, transparent 70%);
    mask-image: linear-gradient(90deg, #000 0%, transparent 70%);
  }
  .net-hero__content { max-width: 44rem; }
  .net-hero .net__eyebrow { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(6px); }
  .net-hero h1 {
    margin-top: 1.4rem;
    font-size: clamp(2.3rem, 5.4vw, 4.4rem);
    font-weight: 500;
    line-height: 1.08;
    letter-spacing: -0.03em;
  }
  .net-hero h1 span { color: #ff6b5a; }
  .net-hero__lead { margin-top: 1.5rem; max-width: 37rem; font-size: clamp(1.02rem, 1.4vw, 1.2rem); line-height: 1.75; color: rgba(255, 255, 255, 0.84); }
  .net-hero__actions { display: flex; flex-wrap: wrap; gap: 0.9rem; margin-top: 2.2rem; }
  .net-hero__tags { display: flex; flex-wrap: wrap; gap: 0.6rem; margin-top: 2.6rem; }
  .net-hero__tags li {
    padding: 0.4rem 0.9rem;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    color: rgba(255, 255, 255, 0.85);
    background: rgba(255, 255, 255, 0.05);
  }

  /* ── sections ──────────────────────────────────────────────────────── */
  .net-section { padding-block: clamp(4rem, 8vw, 7rem); }
  .net-section--soft { background: var(--net-soft); }
  .net-head { max-width: 46rem; margin-inline: auto; text-align: center; }
  .net-head h2 { margin-top: 1.1rem; font-size: clamp(1.9rem, 3.8vw, 3rem); font-weight: 500; line-height: 1.15; letter-spacing: -0.02em; }
  .net-head p { margin-top: 1.1rem; font-size: 1.08rem; line-height: 1.75; color: var(--net-muted); }

  /* ── service cards ─────────────────────────────────────────────────── */
  .net-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: clamp(1rem, 2vw, 1.6rem); margin-top: clamp(2.5rem, 5vw, 4rem); }
  .net-card {
    display: flex;
    flex-direction: column;
    padding: clamp(1.4rem, 2.4vw, 2.1rem);
    border: 1px solid var(--net-line);
    border-radius: 1.25rem;
    background: #fff;
    scroll-margin-top: 6rem;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
  }
  .net-card:hover { transform: translateY(-5px); box-shadow: 0 30px 60px -36px rgba(10, 16, 28, 0.45); border-color: transparent; }
  .net-card__top { display: flex; align-items: center; justify-content: space-between; }
  .net-card__icon { display: grid; place-items: center; width: 3.4rem; height: 3.4rem; border-radius: 1rem; background: #fdecea; color: var(--net-accent-strong); }
  .net-card__icon svg { width: 1.65rem; height: 1.65rem; }
  .net-card__letter { font-size: 2.6rem; font-weight: 300; line-height: 1; color: #d6dae2; letter-spacing: -0.04em; }
  .net-card h3 { margin-top: 1.3rem; font-size: 1.28rem; font-weight: 600; line-height: 1.3; letter-spacing: -0.01em; }
  .net-card__text { margin-top: 0.7rem; line-height: 1.65; color: var(--net-muted); }
  .net-list { margin-top: 1.3rem; padding-top: 1.3rem; border-top: 1px solid var(--net-line); display: grid; gap: 0.65rem; flex: 1; align-content: start; }
  .net-list li { position: relative; padding-left: 1.5rem; font-size: 0.96rem; line-height: 1.5; }
  .net-list li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0.42rem;
    width: 0.62rem;
    height: 0.36rem;
    border-left: 2px solid var(--net-accent-strong);
    border-bottom: 2px solid var(--net-accent-strong);
    transform: rotate(-45deg);
  }
  .net-card__link { display: inline-flex; align-items: center; gap: 0.4rem; margin-top: 1.5rem; font-weight: 600; color: var(--net-accent-strong); text-decoration: none; }
  .net-card__link svg { width: 1rem; height: 1rem; transition: transform 0.25s; }
  .net-card__link:hover svg { transform: translateX(4px); }
  .net-card--wide { grid-column: 1 / -1; }
  .net-card--wide .net-list { grid-template-columns: repeat(3, 1fr); column-gap: 2rem; flex: 0; }

  /* ── infrastructure visual ─────────────────────────────────────────── */
  .net-infra { position: relative; isolation: isolate; color: #fff; background: var(--net-navy); overflow: hidden; }
  .net-infra::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background:
      radial-gradient(36rem 24rem at 100% 0%, rgba(56, 189, 248, 0.16), transparent 65%),
      radial-gradient(30rem 20rem at 0% 100%, rgba(224, 69, 52, 0.16), transparent 65%);
  }
  .net-infra__grid { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: clamp(2rem, 6vw, 5rem); align-items: center; }
  .net-infra .net__eyebrow { background: rgba(255, 255, 255, 0.1); }
  .net-infra h2 { margin-top: 1.1rem; font-size: clamp(1.9rem, 3.6vw, 2.9rem); font-weight: 500; line-height: 1.12; letter-spacing: -0.02em; }
  .net-infra__lead { margin-top: 1.2rem; font-size: 1.06rem; line-height: 1.75; color: rgba(255, 255, 255, 0.78); }
  .net-layers { margin-top: 2rem; display: grid; gap: 0.2rem; }
  .net-layers li { display: flex; gap: 1.1rem; padding: 0.95rem 0; border-top: 1px solid rgba(255, 255, 255, 0.12); }
  .net-layers li:last-child { border-bottom: 1px solid rgba(255, 255, 255, 0.12); }
  .net-layers__n { flex-shrink: 0; width: 2rem; font-size: 0.95rem; font-weight: 600; color: #ff6b5a; letter-spacing: 0.05em; padding-top: 0.15rem; }
  .net-layers__t { font-weight: 600; }
  .net-layers__d { margin-top: 0.15rem; font-size: 0.93rem; color: rgba(255, 255, 255, 0.65); }

  .net-diagram {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 1.5rem;
    background: linear-gradient(160deg, rgba(255, 255, 255, 0.07), rgba(255, 255, 255, 0.02));
    padding: clamp(0.6rem, 2vw, 1.4rem);
    box-shadow: 0 40px 80px -50px #000;
  }
  .net-diagram svg { display: block; width: 100%; height: auto; }
  .net-diagram text { font-family: inherit; fill: #fff; font-size: 17px; font-weight: 500; text-anchor: middle; }
  .net-diagram .sub { fill: rgba(255, 255, 255, 0.6); font-size: 14px; font-weight: 400; }
  .net-diagram .node { fill: #131b2c; stroke: rgba(255, 255, 255, 0.22); stroke-width: 1.5; }
  .net-diagram .node--fw { stroke: #ff6b5a; }
  .net-diagram .link path { stroke: rgba(56, 189, 248, 0.55); stroke-width: 2; fill: none; }
  .net-diagram .vpn { stroke: #ff6b5a; stroke-width: 2; stroke-dasharray: 7 7; fill: none; }
  .net-diagram .flow path { stroke-dasharray: 2 10; stroke-linecap: round; stroke: #bfeaff; stroke-width: 3; fill: none; animation: net-flow 1.6s linear infinite; }
  .net-diagram .tag { fill: #ff6b5a; font-size: 13px; font-weight: 600; letter-spacing: 0.1em; }
  @keyframes net-flow { to { stroke-dashoffset: -24; } }

  /* ── process ───────────────────────────────────────────────────────── */
  .net-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: clamp(1rem, 2vw, 1.6rem); margin-top: clamp(2.5rem, 5vw, 4rem); counter-reset: step; }
  .net-step { position: relative; padding: 1.6rem 1.5rem; border: 1px solid var(--net-line); border-radius: 1.25rem; background: #fff; }
  .net-step::before { counter-increment: step; content: counter(step); display: grid; place-items: center; width: 2.4rem; height: 2.4rem; border-radius: 9999px; background: #000; color: #fff; font-weight: 600; }
  .net-step h3 { margin-top: 1.1rem; font-size: 1.2rem; font-weight: 600; }
  .net-step p { margin-top: 0.5rem; line-height: 1.65; color: var(--net-muted); font-size: 0.97rem; }

  /* ── CTA ───────────────────────────────────────────────────────────── */
  .net-cta { padding-block: clamp(3rem, 6vw, 5rem) clamp(4rem, 8vw, 7rem); }
  .net-cta__box {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: clamp(2.5rem, 6vw, 5rem) clamp(1.5rem, 5vw, 4rem);
    border-radius: clamp(1.25rem, 3vw, 2rem);
    text-align: center;
    color: #fff;
    background:
      radial-gradient(40rem 22rem at 85% 0%, rgba(224, 69, 52, 0.45), transparent 65%),
      radial-gradient(36rem 20rem at 0% 100%, rgba(56, 189, 248, 0.2), transparent 60%),
      linear-gradient(140deg, #0a101c 0%, #172033 100%);
  }
  .net-cta__box::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background-image: radial-gradient(rgba(255, 255, 255, 0.14) 1px, transparent 1.4px);
    background-size: 28px 28px;
    -webkit-mask-image: radial-gradient(34rem 18rem at 50% 50%, #000 15%, transparent 78%);
    mask-image: radial-gradient(34rem 18rem at 50% 50%, #000 15%, transparent 78%);
  }
  .net-cta h2 { font-size: clamp(2rem, 4.6vw, 3.6rem); font-weight: 500; letter-spacing: -0.02em; line-height: 1.1; }
  .net-cta p { max-width: 38rem; margin: 1.2rem auto 0; font-size: 1.1rem; line-height: 1.75; color: rgba(255, 255, 255, 0.8); }
  .net-cta__actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.9rem; margin-top: 2.2rem; }
  .net-cta__contact { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.6rem 2rem; margin-top: 1.8rem; font-size: 0.98rem; }
  .net-cta__contact a { display: inline-flex; align-items: center; gap: 0.5rem; color: rgba(255, 255, 255, 0.85); text-decoration: none; }
  .net-cta__contact a:hover { color: #fff; text-decoration: underline; }
  .net-cta__contact svg { width: 1.1rem; height: 1.1rem; }

  /* ── responsive ────────────────────────────────────────────────────── */
  @media (max-width: 1100px) {
    .net-grid { grid-template-columns: repeat(2, 1fr); }
    .net-card--wide .net-list { grid-template-columns: repeat(2, 1fr); }
    .net-steps { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 960px) {
    .net-infra__grid { grid-template-columns: 1fr; }
    .net-hero::before { background: linear-gradient(0deg, rgba(8, 12, 22, 0.94) 0%, rgba(8, 12, 22, 0.8) 60%, rgba(8, 12, 22, 0.55) 100%); }
  }
  @media (max-width: 680px) {
    .net-grid, .net-steps { grid-template-columns: 1fr; }
    .net-card--wide .net-list { grid-template-columns: 1fr; }
    .net-hero__actions .net__btn, .net-cta__actions .net__btn { width: 100%; }
    .net-hero__tags { margin-top: 2rem; }
  }
  @media (prefers-reduced-motion: reduce) {
    .net *, .net *::before, .net *::after { transition: none !important; }
    .net-diagram .flow path { animation: none; }
    .net-card:hover, .net__btn:hover { transform: none; }
  }
</style>

<div class="net">

  {{-- ── Hero ───────────────────────────────────────────────────────── --}}
  <section class="net-hero" aria-labelledby="net-title">
    <img class="net-hero__bg" src="{{ $s['hero_image'] ?? asset('assets/images/hero-print.jpg') }}" alt="" fetchpriority="high" decoding="async">

    <div class="net__shell">
      <div class="net-hero__content">
        <span class="net__eyebrow">{{ $s['hero_eyebrow'] ?? 'Мрежни инсталации и одржување' }}</span>

        <h1 id="net-title">{{ $s['hero_heading'] ?? 'Комплетни мрежни решенија за вашиот бизнис' }}</h1>

        <p class="net-hero__lead">{{ $s['hero_description'] ?? 'Проектирање, инсталација, конфигурација, заштита и одржување на компјутерски мрежи. Обезбедуваме сигурна, брза и стабилна мрежна инфраструктура.' }}</p>

        <div class="net-hero__actions">
          <a href="{{ $contactUrl }}" class="net__btn net__btn--primary">
            Побарај понуда
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
          </a>
          <a href="#kontakt" class="net__btn net__btn--ghost">Контактирај нè</a>
        </div>

        <ul class="net-hero__tags" aria-label="Што опфаќаме">
          <li>LAN / WAN</li>
          <li>Firewall &amp; VPN</li>
          <li>Wi-Fi</li>
          <li>Мониторинг</li>
        </ul>
      </div>
    </div>
  </section>

  {{-- ── Services ───────────────────────────────────────────────────── --}}
  <section class="net-section" id="uslugi" aria-labelledby="net-services-title">
    <div class="net__shell">
      <div class="net-head">
        <span class="net__eyebrow">Комплетни мрежни услуги</span>
        <h2 id="net-services-title">Сè што ви е потребно за сигурна и стабилна мрежа</h2>
        <p>Од првиот кабел до секојдневното одржување, ја градиме и ја чуваме мрежата на која се потпира вашиот бизнис.</p>
      </div>

      <div class="net-grid">
        @foreach ($services as $service)
          <article class="net-card {{ !empty($service['wide']) ? 'net-card--wide' : '' }}" id="{{ $service['id'] }}">
            <div class="net-card__top">
              <span class="net-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $service['icon'] }}" /></svg>
              </span>
              <span class="net-card__letter" aria-hidden="true">{{ $service['letter'] }}</span>
            </div>

            <h3>{{ $service['title'] }}</h3>
            <p class="net-card__text">{{ $service['text'] }}</p>

            <ul class="net-list">
              @foreach ($service['items'] as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ul>

            <a class="net-card__link" href="{{ $contactUrl }}">
              Побарај понуда за оваа услуга
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ── Network infrastructure (visual) ────────────────────────────── --}}
  <section class="net-section net-infra" id="infrastruktura" aria-labelledby="net-infra-title">
    <div class="net__shell">
      <div class="net-infra__grid">
        <div>
          <span class="net__eyebrow">Мрежна инфраструктура</span>
          <h2 id="net-infra-title">Една мрежа, изградена слој по слој</h2>
          <p class="net-infra__lead">Секој слој е проектиран да работи заедно со другите: од каблирањето и мрежната опрема, до безбедноста, безжичниот пристап и мониторингот.</p>

          <ol class="net-layers">
            @foreach ($layers as [$n, $title, $desc])
              <li>
                <span class="net-layers__n">{{ $n }}</span>
                <div>
                  <div class="net-layers__t">{{ $title }}</div>
                  <div class="net-layers__d">{{ $desc }}</div>
                </div>
              </li>
            @endforeach
          </ol>
        </div>

        <figure class="net-diagram" style="margin:0;">
          <svg viewBox="0 0 640 620" role="img" aria-labelledby="net-diagram-title net-diagram-desc" xmlns="http://www.w3.org/2000/svg">
            <title id="net-diagram-title">Шема на корпоративна мрежна инфраструктура</title>
            <desc id="net-diagram-desc">Интернет е поврзан преку рутер и Firewall со главен Switch, кој ги поврзува работните станици, Wi-Fi Access Point и серверот. Филијала е поврзана со Firewall преку VPN.</desc>

            {{-- links --}}
            <g class="link">
              <path d="M320 86 V132" />
              <path d="M320 192 V252" />
              <path d="M320 312 V372" />
              <path d="M320 432 V470 H110 V500" />
              <path d="M320 432 V500" />
              <path d="M320 432 V470 H530 V500" />
            </g>
            <path class="vpn" d="M400 282 H470" />

            {{-- animated traffic --}}
            <g class="flow">
              <path d="M320 86 V132" />
              <path d="M320 192 V252" />
              <path d="M320 312 V372" />
              <path d="M320 432 V470 H110 V500" />
              <path d="M320 432 V500" />
              <path d="M320 432 V470 H530 V500" />
            </g>

            {{-- internet --}}
            <path d="M262 74 a22 22 0 0 1 6-42 a32 32 0 0 1 60-8 a26 26 0 0 1 40 24 a20 20 0 0 1 4 26 z" fill="#131b2c" stroke="rgba(255,255,255,.22)" stroke-width="1.5" />
            <text x="320" y="62">Интернет</text>

            <rect class="node" x="240" y="132" width="160" height="60" rx="14" />
            <text x="320" y="168">Рутер</text>

            <rect class="node node--fw" x="240" y="252" width="160" height="60" rx="14" />
            <text x="320" y="288">Firewall</text>

            <text class="tag" x="435" y="272" text-anchor="middle">VPN</text>
            <rect class="node" x="470" y="252" width="150" height="60" rx="14" />
            <text x="545" y="288">Филијала</text>

            <rect class="node" x="230" y="372" width="180" height="60" rx="14" />
            <text x="320" y="408">Главен Switch</text>

            <rect class="node" x="35" y="500" width="150" height="60" rx="14" />
            <text x="110" y="536">Switch</text>
            <text class="sub" x="110" y="590">Работни станици</text>

            <rect class="node" x="245" y="500" width="150" height="60" rx="14" />
            <text x="320" y="536">Access Point</text>
            <text class="sub" x="320" y="590">Wi-Fi уреди</text>

            <rect class="node" x="455" y="500" width="150" height="60" rx="14" />
            <text x="530" y="536">Сервер</text>
            <text class="sub" x="530" y="590">Апликации и податоци</text>
          </svg>
        </figure>
      </div>
    </div>
  </section>

  {{-- ── Process ────────────────────────────────────────────────────── --}}
  <section class="net-section net-section--soft" id="proces" aria-labelledby="net-process-title">
    <div class="net__shell">
      <div class="net-head">
        <span class="net__eyebrow">Како работиме</span>
        <h2 id="net-process-title">Јасен процес, од анализа до одржување</h2>
      </div>

      <ol class="net-steps">
        @foreach ($steps as [$title, $desc])
          <li class="net-step">
            <h3>{{ $title }}</h3>
            <p>{{ $desc }}</p>
          </li>
        @endforeach
      </ol>
    </div>
  </section>

  {{-- ── Call to action ─────────────────────────────────────────────── --}}
  <section class="net-cta" id="kontakt" aria-labelledby="net-cta-title">
    <div class="net__shell">
      <div class="net-cta__box">
        <h2 id="net-cta-title">Побарај понуда</h2>
        <p>Планирате нова мрежа или имате проблеми со постојната? Кажете ни што ви треба и ќе ви се јавиме со понуда прилагодена на вашиот бизнис.</p>

        <div class="net-cta__actions">
          <a href="{{ $contactUrl }}" class="net__btn net__btn--primary">
            Побарај понуда
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
          </a>
          <a href="{{ $contactUrl }}" class="net__btn net__btn--ghost">Контактирај нè</a>
        </div>

        <div class="net-cta__contact">
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
