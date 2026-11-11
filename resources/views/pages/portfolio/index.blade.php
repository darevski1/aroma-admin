@extends('layouts.app')

@include('partials.page-seo', ['seoPage' => $page ?? null, 'seoTitle' => 'Портфолио на ИТ решенија | Aroma', 'seoDescription' => 'Разгледајте дел од решенијата и услугите на Aroma во софтвер, веб-развој, ИТ
инфраструктура, безбедност и индустриска автоматизација.'])

@section('content')
@php
$s = $page->settings ?? [];
$categoryLabels = [
'all' => 'Сите решенија',
'software' => 'Софтвер и апликации',
'web' => 'Веб-развој',
'infrastructure' => 'Инфраструктура',
'security' => 'Безбедност',
'industrial' => 'Индустриски решенија',
];

// The project list mixes available project information with service capabilities;
// project work is labelled as such only where the site contains verified details.
$portfolioItems = [
[
'title' => 'CRM за Црвен крст',
'category' => 'Софтвер и апликации', 'filter' => 'software', 'kind' => 'Проект',
'description' => 'CRM решение за поддршка на организирањето информации и секојдневните работни процеси.',
'tags' => ['CRM', 'Деловен софтвер'], 'icon' => 'code', 'tone' => 'coral',
],
[
'title' => 'Мобилна апликација за рециклирање',
'category' => 'Софтвер и апликации', 'filter' => 'software', 'kind' => 'Проект',
'description' => 'Мобилно дигитално решение поврзано со рециклирање и поодговорно управување со отпадот.',
'tags' => ['Мобилна апликација', 'Рециклирање'], 'icon' => 'leaf', 'tone' => 'green',
],
[
'title' => 'Апликација за коучинг',
'category' => 'Софтвер и апликации', 'filter' => 'software', 'kind' => 'Проект',
'description' => 'Апликациско решение за поддршка на коучинг процеси и комуникација со корисниците.',
'tags' => ['Апликација', 'Коучинг'], 'icon' => 'people', 'tone' => 'violet',
],
[
'title' => 'Софтвер по мерка за бизниси',
'category' => 'Софтвер и апликации', 'filter' => 'software', 'kind' => 'Способност',
'description' => 'Деловни алатки и апликации обликувани околу конкретни процеси, корисници и потреби.',
'tags' => ['Софтвер по мерка', 'Автоматизација'], 'icon' => 'layers', 'tone' => 'coral',
],
[
'title' => 'Веб-страници и веб-апликации',
'category' => 'Веб-развој', 'filter' => 'web', 'kind' => 'Способност',
'description' => 'Дизајн и изработка на деловни веб-страници и веб-апликации со фокус на јасно корисничко искуство.',
'tags' => ['PHP', 'ASP.NET', 'JavaScript'], 'icon' => 'browser', 'tone' => 'blue',
],
[
'title' => 'Деловни ИТ системи',
'category' => 'Веб-развој', 'filter' => 'web', 'kind' => 'Способност',
'description' => 'Веб-решенија и деловни системи што ги поврзуваат информациите и го поддржуваат секојдневното
работење.',
'tags' => ['Веб-развој', 'JavaScript'], 'icon' => 'brackets', 'tone' => 'blue',
],
[
'title' => 'Деловни мрежи и Wi-Fi',
'category' => 'Мрежи и ИТ инфраструктура', 'filter' => 'infrastructure', 'kind' => 'Услуга',
'description' => 'Проектирање, инсталација и одржување на деловни мрежи, каблирање и безжична поврзаност.',
'tags' => ['LAN', 'Wi-Fi', 'MikroTik'], 'icon' => 'network', 'tone' => 'blue',
],
[
'title' => 'Сервери, резервни копии и NAS',
'category' => 'Мрежи и ИТ инфраструктура', 'filter' => 'infrastructure', 'kind' => 'Услуга',
'description' => 'Поставување и администрација на серверски системи, складирање податоци и backup решенија.',
'tags' => ['Windows Server', 'Linux', 'NAS', 'Backup'], 'icon' => 'server', 'tone' => 'violet',
],
[
'title' => 'VPN и безбеден далечински пристап',
'category' => 'Мрежи и ИТ инфраструктура', 'filter' => 'infrastructure', 'kind' => 'Услуга',
'description' => 'Безбедно поврзување до деловната мрежа и ресурсите од оддалечени локации.',
'tags' => ['VPN', 'OpenVPN'], 'icon' => 'shield', 'tone' => 'green',
],
[
'title' => 'Видеонадзор и CCTV',
'category' => 'Безбедност и надзор', 'filter' => 'security', 'kind' => 'Услуга',
'description' => 'Инсталација и конфигурација на IP камери и системи за видеонадзор со складирање снимки.',
'tags' => ['CCTV', 'IP камери', 'NVR'], 'icon' => 'camera', 'tone' => 'coral',
],
[
'title' => 'Аларми и контрола на пристап',
'category' => 'Безбедност и надзор', 'filter' => 'security', 'kind' => 'Услуга',
'description' => 'Решенија за детекција на пожар, алармни системи и контрола на пристап до објекти.',
'tags' => ['Детекција на пожар', 'Аларми', 'Пристап'], 'icon' => 'access', 'tone' => 'amber',
],
[
'title' => 'CenoBots — автономно индустриско чистење',
'category' => 'Индустриска автоматизација', 'filter' => 'industrial', 'kind' => 'CenoBots решение',
'description' => 'Автономни CenoBots роботи за чистење комерцијални и индустриски простори, со локална поддршка, сервис
и обука.',
'tags' => ['CenoBots', 'Роботика', 'Автоматизација'], 'icon' => 'robot', 'tone' => 'blue',
'image' => 'assets/images/cenobots/opt/hero-scene-1600.webp', 'alt' => 'CenoBots автономен робот за чистење во
комерцијален објект', 'href' => '/services/cenobots',
],
];

$projectItems = $s['projects'] ?? [];
$customProjects = collect($projectItems)->filter(fn ($project) => !empty($project['title']))->map(function ($project) {
return [
'title' => $project['title'],
'category' => $project['category'] ?? 'ИТ решенија',
'filter' => $project['filter'] ?? 'infrastructure',
'kind' => 'Реализација',
'description' => $project['description'] ?? '',
'tags' => $project['tags'] ?? [],
'icon' => $project['icon'] ?? 'layers',
'tone' => 'coral',
'image' => $project['image'] ?? null,
'alt' => $project['alt'] ?? $project['title'],
'href' => $project['href'] ?? null,
];
});
$portfolioItems = $customProjects->isNotEmpty() ? $customProjects->values()->all() : $portfolioItems;
@endphp

<main class="aroma-portfolio" id="portfolio-top">
  <style>
    @include('pages.portfolio.portfolio-styles')
  </style>
  <section class="aroma-portfolio__hero" aria-labelledby="portfolio-title">
    <div class="aroma-portfolio__hero-grid" aria-hidden="true"></div>
    <div class="aroma-portfolio__shell aroma-portfolio__hero-layout">
      <div class="aroma-portfolio__intro">
        <span class="aroma-portfolio__eyebrow"><i></i>{{ $s['hero_eyebrow'] ?? 'ПОРТФОЛИО · АРОМА' }}</span>
        <h1 id="portfolio-title">{{ $s['hero_heading'] ?? 'Технологија што го движи вашиот бизнис.' }}</h1>
        <p>{{ $s['hero_description'] ?? 'Од апликации и веб-платформи до поврзана инфраструктура и индустриска роботика
          — истражете ги решенијата и услугите на Aroma.' }}</p>
        <div class="aroma-portfolio__hero-actions">
          <a class="aroma-portfolio__button aroma-portfolio__button--primary" href="#portfolio-projects">Истражете го
            портфолиото <span aria-hidden="true">↘</span></a>
          <a class="aroma-portfolio__text-link" href="{{ route('kontakt') }}">Разговарајте со нас <span
              aria-hidden="true">↗</span></a>
        </div>
      </div>

      <div class="aroma-portfolio__hero-art" aria-hidden="true">
        <div class="aroma-portfolio__orbit aroma-portfolio__orbit--one"></div>
        <div class="aroma-portfolio__orbit aroma-portfolio__orbit--two"></div>
        <div class="aroma-portfolio__art-core"><span>AROMA</span><b>TECH<br>IN MOTION</b><i>✳</i></div>
        <div class="aroma-portfolio__art-node aroma-portfolio__art-node--app"><span>⌘</span><b>Апликации</b></div>
        <div class="aroma-portfolio__art-node aroma-portfolio__art-node--network"><span>⌁</span><b>Поврзување</b></div>
        <div class="aroma-portfolio__art-node aroma-portfolio__art-node--secure"><span>◈</span><b>Безбедност</b></div>
        <div class="aroma-portfolio__art-node aroma-portfolio__art-node--robot"><span>⟡</span><b>Автоматизација</b>
        </div>
        <span class="aroma-portfolio__art-caption">ДИГИТАЛНО · ПОВРЗАНО · ПРАКТИЧНО</span>
      </div>
    </div>
    <div class="aroma-portfolio__hero-foot aroma-portfolio__shell"><span>РЕШЕНИЈА ЗА ВАШИОТ БИЗНИС</span><span>01 —
        05</span></div>
  </section>

  <section class="aroma-portfolio__catalog" id="portfolio-projects" aria-labelledby="portfolio-catalog-title">
    <div class="aroma-portfolio__shell">
      <div class="aroma-portfolio__catalog-heading">
        <div>
          <h2 id="portfolio-catalog-title">Наши конкретни проекти </h2>
        </div>

      </div>

      <div class="aroma-portfolio__filters" role="group" aria-label="Филтрирајте по категорија">
        @foreach($categoryLabels as $key => $label)
        <button class="aroma-portfolio__filter{{ $key === 'all' ? ' is-active' : '' }}" type="button"
          data-portfolio-filter="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}">{{ $label
          }}</button>
        @endforeach
      </div>
      <div class="aroma-portfolio__category-intro" aria-label="Категории на портфолио">
        <div><span>01</span><b>Дигитални производи</b><small>Софтвер · апликации · веб</small></div>
        <div><span>02</span><b>Поврзана инфраструктура</b><small>Мрежи · сервери · заштита</small></div>
        <div><span>03</span><b>Индустриска роботика</b><small>Автономно чистење · CenoBots</small></div>
      </div>

      <div class="aroma-portfolio__grid" aria-live="polite">
        @foreach($portfolioItems as $item)
        <article class="aroma-portfolio__card" data-portfolio-category="{{ $item['filter'] }}">
          @php $projectUrl = $item['href'] ?? null; @endphp
          @if($projectUrl)<a class="aroma-portfolio__card-link" href="{{ $projectUrl }}"
            aria-label="Дознајте повеќе: {{ $item['title'] }}">@endif
            <div class="aroma-portfolio__visual aroma-portfolio__visual--{{ $item['tone'] }}">
              @if(!empty($item['image']))
              <img src="{{ asset($item['image']) }}" alt="{{ $item['alt'] ?? $item['title'] }}" loading="lazy">
              @else
              <div class="aroma-portfolio__visual-grid" aria-hidden="true"></div>
              <span class="aroma-portfolio__visual-index">АРОМА / {{ str_pad((string) $loop->iteration, 2, '0',
                STR_PAD_LEFT) }}</span>
              <svg class="aroma-portfolio__illustration" viewBox="0 0 240 160" fill="none" aria-hidden="true">
                @switch($item['icon'])
                @case('code')
                @case('brackets')
                <rect x="48" y="30" width="144" height="100" rx="12" />
                <path d="M48 55h144M65 43h2m10 0h2m10 0h2M93 88l-14 12 14 12m54-24 14 12-14 12m-18-30-13 36" />
                <path class="aroma-portfolio__illustration-accent" d="M58 68h39" />
                <circle class="aroma-portfolio__illustration-fill" cx="160" cy="42" r="3" />
                @break
                @case('leaf')
                <path d="M182 36C112 37 61 55 59 101c-1 22 17 34 37 27 40-15 68-58 86-92Z" />
                <path d="M72 122c21-28 48-47 83-67M117 87l-5-25m-13 40-27-3" />
                <circle class="aroma-portfolio__illustration-fill" cx="62" cy="117" r="6" />
                @break
                @case('people')
                <circle cx="120" cy="65" r="21" />
                <circle cx="76" cy="77" r="15" />
                <circle cx="164" cy="77" r="15" />
                <path d="M77 126c2-21 18-34 43-34s41 13 43 34m-109-2c2-17 13-27 29-29m90 29c-2-17-13-27-29-29" />
                <path class="aroma-portfolio__illustration-accent" d="M108 64h24m-12-12v25" />
                @break
                @case('network')
                <path d="m120 78-56-35m56 35 56-35m-56 35-56 36m56-36 56 36" />
                <rect x="100" y="58" width="40" height="40" rx="12" />
                <rect x="43" y="25" width="28" height="22" rx="7" />
                <rect x="169" y="25" width="28" height="22" rx="7" />
                <rect x="43" y="104" width="28" height="22" rx="7" />
                <rect x="169" y="104" width="28" height="22" rx="7" />
                <path class="aroma-portfolio__illustration-accent" d="M111 70h18m-18 9h18m-18 9h11" />
                @break
                @case('server')
                <rect x="65" y="26" width="110" height="34" rx="8" />
                <rect x="65" y="68" width="110" height="34" rx="8" />
                <rect x="65" y="110" width="110" height="24" rx="7" />
                <circle class="aroma-portfolio__illustration-fill" cx="82" cy="43" r="4" />
                <circle class="aroma-portfolio__illustration-fill" cx="82" cy="85" r="4" />
                <path d="M97 43h55m-55 42h55m-55 36h55" />
                @break
                @case('shield')
                <path d="M120 27 171 46v34c0 29-21 49-51 62-30-13-51-33-51-62V46l51-19Z" />
                <path class="aroma-portfolio__illustration-accent" d="m97 82 16 16 31-34" />
                <circle cx="120" cy="78" r="37" />
                @break
                @case('camera')
                <path d="M53 58h101l35 20-35 20H53z" />
                <circle cx="94" cy="78" r="19" />
                <circle cx="94" cy="78" r="8" />
                <path d="M68 99v19h88m-55-42h-8" />
                <path class="aroma-portfolio__illustration-accent" d="M154 68v20" />
                @break
                @case('access')
                <rect x="69" y="25" width="102" height="110" rx="13" />
                <rect x="85" y="41" width="70" height="40" rx="6" />
                <circle cx="120" cy="105" r="8" />
                <path class="aroma-portfolio__illustration-accent" d="M105 60h30m-15-12v24m-10 47h20" />
                @break
                @case('robot')
                @case('layers')
                @default
                <path d="m120 25 70 36-70 36-70-36 70-36Z" />
                <path d="m50 81 70 36 70-36m-140 24 70 36 70-36" />
                <path class="aroma-portfolio__illustration-accent" d="m91 61 20 11 38-20" />
                @endswitch
              </svg>
              <span class="aroma-portfolio__visual-caption">{{ $item['category'] }}</span>
              @endif
              @if(!empty($item['image']))<span class="aroma-portfolio__visual-caption">{{ $item['category']
                }}</span>@endif
            </div>
            <div class="aroma-portfolio__card-body">
              <div class="aroma-portfolio__card-meta"><span>{{ $item['kind'] }}</span><span>{{ str_pad((string)
                  $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
              <h3>{{ $item['title'] }}</h3>
              <p>{{ $item['description'] }}</p>
              @if(!empty($item['tags']))<div class="aroma-portfolio__tags">@foreach($item['tags'] as $tag)<span>{{ $tag
                  }}</span>@endforeach</div>@endif
              @if($projectUrl)<span class="aroma-portfolio__card-more">Истражете го решението <b
                  aria-hidden="true">↗</b></span>@endif
            </div>
            @if($projectUrl)
          </a>@endif
        </article>
        @endforeach
      </div>
      <p class="aroma-portfolio__empty" hidden>Нема ставки во оваа категорија.</p>
    </div>
  </section>

  <section class="aroma-portfolio__note" aria-label="За нашите решенија">
    <div class="aroma-portfolio__shell aroma-portfolio__note-inner"><span class="aroma-portfolio__note-mark"
        aria-hidden="true">✳</span>
      <p>Секое решение започнува со разбирање на реалните потреби — и се развива во јасен, одржлив следен чекор.</p><a
        href="{{ route('kontakt') }}">Разговарајте со Aroma <span aria-hidden="true">↗</span></a>
    </div>
  </section>
</main>

<script>
  (() => {
    const page = document.querySelector('.aroma-portfolio');
    if (!page || page.dataset.filtersReady) return;
    page.dataset.filtersReady = 'true';

    const buttons = page.querySelectorAll('[data-portfolio-filter]');
    const cards = page.querySelectorAll('[data-portfolio-category]');
    const empty = page.querySelector('.aroma-portfolio__empty');

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const filter = button.dataset.portfolioFilter;
        let visibleCount = 0;

        buttons.forEach((item) => {
          const active = item === button;
          item.classList.toggle('is-active', active);
          item.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        cards.forEach((card) => {
          const visible = filter === 'all' || card.dataset.portfolioCategory === filter;
          card.hidden = !visible;
          if (visible) card.removeAttribute('aria-hidden');
          else card.setAttribute('aria-hidden', 'true');
          if (visible) visibleCount += 1;
        });

        if (empty) empty.hidden = visibleCount > 0;
      });
    });
  })();
</script>
@endsection