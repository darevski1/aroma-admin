<section class="garmin-showcase bgpacebot bg" aria-labelledby="garmin-showcase-title">
  <div class="garmin-showcase__inner">
    <div class="garmin-showcase__header">
      <span class="garmin-showcase__eyebrow">explore</span>

      <img src="{{ asset('./assets/images/logo/garmin.svg') }}" alt="Garmin" class="garmin-showcase__logo" />

      <h2 id="garmin-showcase-title" class="garmin-showcase__title">
        Паметни часовници и GPS уреди дизајнирани за авантура, спорт и секојдневие.
      </h2>

      <a href="https://exploregarmin.mk/" target="_blank" rel="noopener" class="garmin-showcase__brand-link"
        aria-label="Отвори Explore Garmin">
        <img src="{{ asset('./assets/images/explore_garmin.png') }}" alt="explore garmin" />
      </a>
    </div>

    <div class="garmin-showcase__slider-wrap">
      <div class="garmin-showcase__nav">
        <button class="garmin-showcase__arrow garmin-showcase__arrow--prev" type="button"
          aria-label="Претходен Garmin производ">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15 18l-6-6 6-6" />
          </svg>
        </button>
        <button class="garmin-showcase__arrow garmin-showcase__arrow--next" type="button"
          aria-label="Следен Garmin производ">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 6l6 6-6 6" />
          </svg>
        </button>
      </div>

      <div class="swiper garminSwiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener"
                class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/fenix.webp') }}" alt="Garmin Fenix серија"
                  class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Adventure GPS</p>
                <h3>Garmin Fenix</h3>
                <p>Премиум часовник за авантура и тренинг.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener"
                  class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-forerunner/#products-archive" target="_blank"
                rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/forerunner.webp') }}" alt="Garmin Forerunner серија"
                  class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Running Performance</p>
                <h3>Garmin Forerunner</h3>
                <p>Создаден за трчање, подготовка и прогрес.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-forerunner/#products-archive" target="_blank"
                  rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-venu/#products-archive" target="_blank" rel="noopener"
                class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/venu.avif') }}" alt="Garmin Venu серија"
                  class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Wellness Smartwatch</p>
                <h3>Garmin Venu</h3>
                <p>AMOLED стил, здравје и фитнес во едно.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-venu/#products-archive" target="_blank"
                  rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener"
                class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/fenix2.webp') }}" alt="Garmin Fenix промо модел"
                  class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Premium Outdoor</p>
                <h3>Fenix Collection</h3>
                <p>Издржлива колекција за секој предизвик.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener"
                  class="garmin-card__cta">Истражи модели</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-vivoactive/#products-archive" target="_blank"
                rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/vivoactive.webp') }}" alt="Garmin Vivoactive серија"
                  class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Everyday Fitness</p>
                <h3>Garmin Vivoactive</h3>
                <p>Фитнес, здравје и паметни функции секој ден.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-vivoactive/#products-archive" target="_blank"
                  rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>
        </div>

        <div class="garmin-showcase__pagination swiper-pagination"></div>
      </div>
    </div>
  </div>
</section>
