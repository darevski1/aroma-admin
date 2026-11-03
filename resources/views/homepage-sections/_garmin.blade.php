<section class="garmin-showcase bgpacebot bg" aria-labelledby="garmin-showcase-title" data-garmin-carousel>
  @php
    $garminProducts = [
      [
        'name' => 'Garmin fēnix® 8',
        'image' => asset('./assets/images/garmin/fenix.webp'),
        'price' => '61.980',
        'description' => 'Издржлив мултиспорт часовник за тренинг, навигација и авантури на отворено.',
        'url' => 'https://exploregarmin.mk/product/fenix-8/',
      ],
      [
        'name' => 'Garmin Venu® 4',
        'image' => asset('./assets/images/garmin/venu.avif'),
        'price' => '34.080',
        'description' => 'AMOLED паметен часовник со функции за здравје, фитнес и секојдневна енергија.',
        'url' => 'https://exploregarmin.mk/product/venu-4/',
      ],
      [
        'name' => 'Garmin Forerunner® 970',
        'image' => asset('./assets/images/garmin/forerunner.webp'),
        'price' => '46.480',
        'description' => 'Премиум часовник за трчање со напредни анализи и персонализирани насоки.',
        'url' => 'https://exploregarmin.mk/product/forerunner-970/',
      ],
      [
        'name' => 'Garmin Forerunner® 570',
        'image' => asset('./assets/images/garmin/forerunner.webp'),
        'price' => '34.080',
        'description' => 'AMOLED часовник за трчање со следење на фитнесот и повици од зглобот.',
        'url' => 'https://exploregarmin.mk/product/forerunner-570/',
      ],
      [
        'name' => 'Garmin vívoactive® 6',
        'image' => asset('./assets/images/garmin/vivoactive.webp'),
        'price' => '20.480',
        'description' => 'Лесен AMOLED фитнес часовник за активност, здравје и секојдневно следење.',
        'url' => 'https://exploregarmin.mk/product/vivoactive-6/',
      ],
      [
        'name' => 'Garmin Instinct® 3',
        'image' => asset('./assets/images/garmin/fenix2.webp'),
        'price' => '24.780',
        'description' => 'Од робусната серија Instinct 3; каталогот прикажува цена од за ова семејство модели.',
        'url' => 'https://exploregarmin.mk/product/instinct-3/',
      ],
      [
        'name' => 'Garmin Forerunner® 165',
        'image' => asset('./assets/images/garmin/forerunner.webp'),
        'price' => '17.280',
        'description' => 'GPS часовник за трчање со AMOLED екран и адаптивни тренинг-планови.',
        'url' => 'https://exploregarmin.mk/product/forerunner-165/',
      ],
    ];
  @endphp

  <div class="garmin-showcase__inner">
    <div class="garmin-showcase__header">
      <span class="garmin-showcase__eyebrow">Explore Garmin</span>
      <img src="{{ asset('./assets/images/logo/garmin.svg') }}" alt="Garmin" class="garmin-showcase__logo" />
      <h2 id="garmin-showcase-title" class="garmin-showcase__title">
        Часовник за секое темпо, патека и секојдневие.
      </h2>
      <a href="https://exploregarmin.mk/shop/" target="_blank" rel="noopener noreferrer" class="garmin-showcase__brand-link"
        aria-label="Разгледај ги сите производи на Explore Garmin">
        <img src="{{ asset('./assets/images/explore_garmin.png') }}" alt="Explore Garmin" />
      </a>
    </div>

    <div class="garmin-showcase__slider-wrap">
      <div class="garmin-showcase__toolbar">
        <p class="garmin-showcase__count" data-garmin-count aria-live="polite">01 <span>/ 07</span></p>
        <div class="garmin-showcase__nav">
          <button class="garmin-showcase__arrow garmin-showcase__arrow--prev" type="button"
            aria-label="Претходни Garmin производ" data-garmin-prev>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
          </button>
          <button class="garmin-showcase__arrow garmin-showcase__arrow--next" type="button"
            aria-label="Следен Garmin производ" data-garmin-next>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6" /></svg>
          </button>
        </div>
      </div>

      <div class="garmin-showcase__track" data-garmin-track role="region" aria-label="Garmin часовници" tabindex="0">
        @foreach ($garminProducts as $index => $product)
          <article class="garmin-card" data-garmin-slide aria-label="{{ $index + 1 }} од {{ count($garminProducts) }}">
            <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="garmin-card__image-link" tabindex="-1" aria-hidden="true">
              <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="garmin-card__image"
                loading="{{ $index < 3 ? 'eager' : 'lazy' }}" decoding="async" width="988" height="988" />
            </a>
            <div class="garmin-card__body">
              <h3>{{ $product['name'] }}</h3>
              <p class="garmin-card__description">{{ $product['description'] }}</p>
              <p class="garmin-card__price">{{ $product['price'] }} <span>ден</span></p>
              <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="garmin-card__cta">
                Дознај повеќе <span aria-hidden="true">↗</span>
              </a>
            </div>
          </article>
        @endforeach
      </div>
      <div class="garmin-showcase__progress" aria-hidden="true"><span data-garmin-progress></span></div>
    </div>
  </div>
</section>
