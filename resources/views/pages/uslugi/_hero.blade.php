<section class="services-hero{{ request()->is('/') ? ' services-home' : '' }}" aria-labelledby="services-hero-title">
  <div class="services-shell services-hero__grid">
    <div class="services-hero__content">
      <span class="services-eyebrow">ИТ решенија за деловни потреби</span>
      <h1 id="services-hero-title">Комплетни ИТ решенија за вашиот бизнис</h1>
      <p>Од развој на софтвер и веб-апликации до мрежна инфраструктура, серверски системи и видеонадзор — обезбедуваме технологија што го поврзува, штити и унапредува вашиот бизнис.</p>
      <div class="services-hero__actions">
        <a href="/kontakt" class="services-btn services-btn--primary">Побарајте понуда <span aria-hidden="true">↗</span></a>
        <a href="{{ request()->is('/') ? '/uslugi#service-categories' : '#service-categories' }}" class="services-btn services-btn--ghost">Истражете ги услугите <span aria-hidden="true">↓</span></a>
      </div>
      <div class="services-hero__note"><span aria-hidden="true"></span> Соодветна технологија, усогласена со вашите потреби.</div>
    </div>
    <div class="services-hero__visual" role="img" aria-label="Илустрација на поврзани деловни ИТ системи">
      <div class="services-hero__visual-glow"></div>
      <div class="services-hero__visual-frame">
        <div class="services-hero__visual-head"><span class="services-window-dots" aria-hidden="true"><i></i><i></i><i></i></span><span>ИТ ИНФРАСТРУКТУРА</span><span class="services-status"><i></i> Поврзано</span></div>
        <svg class="services-network-art" viewBox="0 0 600 410" fill="none" aria-hidden="true">
          <path class="services-network-line" d="M300 204 160 120M300 204 440 120M300 204 160 288M300 204 440 288M160 120l280 0M160 288h280M160 120v168M440 120v168"/>
          <circle class="services-network-orbit" cx="300" cy="204" r="112"/><circle class="services-network-orbit services-network-orbit--outer" cx="300" cy="204" r="165"/>
          <g class="services-node services-node--main"><rect x="250" y="154" width="100" height="100" rx="26"/><path d="M282 186h36m-36 16h36m-36 16h21"/></g>
          <g class="services-node"><rect x="112" y="80" width="96" height="80" rx="20"/><path d="M137 109h46m-46 15h46m-46 15h26"/></g>
          <g class="services-node"><rect x="392" y="80" width="96" height="80" rx="20"/><path d="M417 109h46m-46 15h46m-46 15h26"/></g>
          <g class="services-node"><rect x="112" y="250" width="96" height="80" rx="20"/><path d="M142 277h36m-36 15h36m-36 15h20"/></g>
          <g class="services-node"><rect x="392" y="250" width="96" height="80" rx="20"/><circle cx="440" cy="290" r="13"/><path d="m434 290 4 4 8-9"/></g>
          <circle class="services-network-point" cx="300" cy="92" r="5"/><circle class="services-network-point" cx="300" cy="316" r="5"/><circle class="services-network-point" cx="190" cy="204" r="5"/><circle class="services-network-point" cx="410" cy="204" r="5"/>
        </svg>
        <div class="services-hero__visual-foot"><span><b></b> Софтвер</span><span><b></b> Мрежи</span><span><b></b> Системи</span><span><b></b> Безбедност</span></div>
      </div>
      <div class="services-hero__floating-card"><span class="services-mini-icon">⌘</span><div><strong>Системи во синхронизација</strong><small>Една функционална инфраструктура</small></div><span class="services-card-check">✓</span></div>
    </div>
  </div>
  <div class="services-hero__bottom services-shell"><span>УСЛУГИ ЗА ЦЕЛАТА ИТ ОКОЛИНА</span><div><i></i><i></i><i></i><i></i><i></i><i></i></div><span>ITPLUS</span></div>
</section>
