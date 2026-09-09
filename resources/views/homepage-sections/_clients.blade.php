<section class="home-clients" aria-labelledby="home-clients-title">
  <div class="home-clients__inner">

    <!-- Header -->
    <div class="home-clients__header">

      <span class="home-clients__eyebrow">
        Доверба и соработка
      </span>


      <h2 id="home-clients-title">
        Наши Клиенти
      </h2>

      <p>
        Компании кои ни веруваат за професионални ИТ решенија, мрежна инфраструктура,
        видео надзор и печатарски услуги.
      </p>
    </div>

    @php
    $clientLogos = [
    ['./../assets/images/clients/erak-group-300x69.png', 'Erak Group'],
    ['./../assets/images/clients/logo_arka-300x300.jpg', 'Arka'],
    ['./../assets/images/clients/logo_delight.jpg', 'Delight'],
    ['./../assets/images/clients/logo_biomedika.jpg', 'Biomedika'],
    ['./../assets/images/clients/logo_digital.jpg', 'Digital'],
    ['./../assets/images/clients/logo_doma.jpg', 'Doma'],
    ['./../assets/images/clients/logo_eurofast.jpg', 'Eurofast'],
    ['./../assets/images/clients/logo_fresh_farm.jpg', 'Fresh Farm'],
    ['./../assets/images/clients/logo_majorel.jpg', 'Majorel'],
    ['./../assets/images/clients/logo_pivara.jpg', 'Pivara'],
    ['./../assets/images/clients/logo_premium.jpg', 'Premium'],
    ['./../assets/images/clients/logo_tehnoguma.jpg', 'Tehnoguma'],
    ['./../assets/images/clients/logo_total_comunikacii.jpg', 'Total Komunikacii'],
    ['./../assets/images/clients/logo_trend.jpg', 'Trend'],
    ['./../assets/images/clients/Vato-Logo.png', 'Vato'],
    ];
    @endphp

    <div class="client-marquee" aria-label="Логоа на наши клиенти">
      <div class="client-marquee__track">
        @for ($i = 0; $i < 2; $i++) <div class="client-marquee__group" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
          @foreach ($clientLogos as [$src, $alt])
          <div class="client-marquee__item">
            <img src="{{ asset($src) }}" alt="{{ $alt }}" loading="lazy">
          </div>
          @endforeach
      </div>
      @endfor
    </div>
  </div>

  </div>
</section>
