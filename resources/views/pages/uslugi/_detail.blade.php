@extends('layouts.app')

@section('title', $service['title'] . ' | Aroma')
@section('meta_description', $service['description'])

@section('content')
<main class="service-detail services-detail-pages" id="top">
  <section class="service-detail__hero service-detail__hero--{{ $service['theme'] }}" aria-labelledby="service-title">
    <div class="service-detail__shell service-detail__hero-grid">
      <div class="service-detail__copy">

        <span class="service-detail__eyebrow">{{ $service['eyebrow'] }}</span>
        <h1 id="service-title">{{ $service['headline'] }}</h1>
        <p>{{ $service['description'] }}</p>
        <div class="service-detail__actions">
          <a class="service-detail__button" href="/kontakt">{{ $service['cta'] }} <span aria-hidden="true">↗</span></a>
          <a class="service-detail__text-link" href="#service-overview">Истражете го решението <span
              aria-hidden="true">↓</span></a>
        </div>
        <div class="service-detail__assurance"><span aria-hidden="true">✳</span> Решение според потребите и околината на
          вашиот бизнис</div>
      </div>
      <div class="service-detail__art" aria-hidden="true">
        <div class="service-detail__art-glow"></div>
        <div class="service-detail__art-card">
          <div class="service-detail__art-top"><span><i></i><i></i><i></i></span><b>{{ $service['art_label'] }}</b><em>●
              СИСТЕМ ONLINE</em></div>
          @if ($service['theme'] === 'software')
          <div class="service-detail__code"><span>01</span><b>&lt;/&gt;</b><strong>Дигитално
              решение</strong><small>Направено околу вашиот процес</small></div>
          <div class="service-detail__code"><span>02</span><b>↗</b><strong>Поврзани работни
              текови</strong><small>Помалку рачни задачи</small></div>
          <div class="service-detail__art-meter"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><span>ЕДНОСТАВНО.
              ФУНКЦИОНАЛНО.</span></div>
          @elseif ($service['theme'] === 'network')
          <svg class="service-detail__network" viewBox="0 0 440 280" fill="none">
            <path
              d="M220 140 90 65m130 75 130-75m-130 75L90 215m130-75 130 75M90 65h260M90 215h260M90 65v150m260-150v150" />
            <circle cx="220" cy="140" r="42" />
            <rect x="59" y="34" width="62" height="62" rx="18" />
            <rect x="319" y="34" width="62" height="62" rx="18" />
            <rect x="59" y="184" width="62" height="62" rx="18" />
            <rect x="319" y="184" width="62" height="62" rx="18" />
            <path d="M205 132h30m-30 16h30m-30 16h18" />
          </svg>
          <div class="service-detail__art-caption"><b>NETWORK / READY</b><span>LAN · Wi-Fi · VPN</span></div>
          @elseif ($service['theme'] === 'server')
          <div class="service-detail__rack">
            <div><span>CORE SERVER</span><i>●</i><b>•••</b></div>
            <div><span>STORAGE ARRAY</span><i>●</i><b>•••</b></div>
            <div><span>BACKUP NODE</span><i>●</i><b>•••</b></div>
            <div class="service-detail__rack-status">СИСТЕМИ В РАБОТА <strong>99.9%</strong></div>
          </div>
          @elseif ($service['theme'] === 'cctv')
          <div class="service-detail__camera-graphic">
            <div class="service-detail__camera-lens"><span></span></div>
            <div class="service-detail__camera-body"></div>
            <div class="service-detail__camera-arm"></div>
            <div class="service-detail__camera-base"></div>
          </div>
          <div class="service-detail__camera-footer"><b>LIVE VIEW</b><span>● ЗАШТИТЕН ОБЈЕКТ</span></div>
          @else
          <div class="service-detail__printer-graphic">
            <div class="service-detail__printer-paper"></div>
            <div class="service-detail__printer-top"><span></span><i></i></div>
            <div class="service-detail__printer-tray"></div>
            <div class="service-detail__printer-light">● READY TO WORK</div>
          </div>
          <div class="service-detail__print-lines"><i></i><i></i><i></i><span>PRINT · SCAN · SUPPORT</span></div>
          @endif
        </div>
        <div class="service-detail__art-float"><span>{{ $service['art_icon'] }}</span>
          <div><b>{{ $service['art_note'] }}</b><small>{{ $service['art_subnote'] }}</small></div><strong>✓</strong>
        </div>
      </div>
    </div>
  </section>

  <section class="service-detail__overview" id="service-overview" aria-labelledby="service-overview-title">
    <div class="service-detail__shell service-detail__overview-grid">
      <div><span class="service-detail__eyebrow">ПРИСТАПОТ</span>
        <h2 id="service-overview-title">{{ $service['overview_title'] }}</h2>
      </div>
      <div>
        <p>{{ $service['overview'] }}</p>
        <div class="service-detail__tags">@foreach ($service['tags'] as $tag)<span>{{ $tag }}</span>@endforeach</div>
      </div>
    </div>
  </section>

  <section class="service-detail__capabilities" aria-labelledby="service-capabilities-title">
    <div class="service-detail__shell">
      <div class="service-detail__section-heading"><span class="service-detail__eyebrow">ОД ПЛАН ДО ПОДДРШКА</span>
        <h2 id="service-capabilities-title">Што опфаќа услугата</h2>
        <p>Практични способности и придобивки, избрани според реалните потреби на вашиот тим.</p>
      </div>
      <div class="service-detail__cards">
        @foreach ($service['capabilities'] as $item)
        <article class="service-detail__capability"><span class="service-detail__number">{{ str_pad((string)
            $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
          <div class="service-detail__cap-icon" aria-hidden="true">{{ $item['icon'] }}</div>
          <h3>{{ $item['title'] }}</h3>
          <p>{{ $item['text'] }}</p>
        </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="service-detail__offer" aria-labelledby="service-offer-title">
    <div class="service-detail__shell service-detail__offer-grid">
      <div class="service-detail__offer-intro"><span class="service-detail__eyebrow">{{ $service['offer_eyebrow']
          }}</span>
        <h2 id="service-offer-title">{{ $service['offer_title'] }}</h2>
        <p>{{ $service['offer_intro'] }}</p>
      </div>
      <ul>@foreach ($service['offers'] as $offer)<li><span aria-hidden="true">↗</span>{{ $offer }}</li>@endforeach</ul>
    </div>
  </section>


  <section class="service-detail__contact" aria-labelledby="service-contact-title">
    <div class="service-detail__shell">
      <div class="service-detail__contact-box">
        <div><span class="service-detail__eyebrow">СЛЕДЕН ЧЕКОР</span>
          <h2 id="service-contact-title">{{ $service['contact_title'] }}</h2>
          <p>{{ $service['contact_text'] }}</p>
        </div><a href="/kontakt" class="service-detail__button">Побарајте понуда <span aria-hidden="true">↗</span></a>
      </div>

      <a href="/uslugi#service-categories" class="service-detail__overview-link"><span aria-hidden="true">←</span> Назад
        кон сите услуги</a>
    </div>
  </section>
</main>
@endsection