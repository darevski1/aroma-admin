@php
$brandLogos = [
['src' => './../assets/images/logo/HP_Logo_0.svg', 'alt' => 'HP'],
['src' => './../assets/images/logo/Dell_Logo_Alternative_0.svg', 'alt' => 'Dell'],
['src' => './../assets/images/logo/Lenovo_idDXuX8rvi_0.svg', 'alt' => 'Lenovo'],
['src' => './../assets/images/logo/Ricoh_Australia_Logo_0.svg', 'alt' => 'Ricoh'],
['src' => './../assets/images/logo/Canon_ide_p-tcLS_0.svg', 'alt' => 'Canon'],
['src' => './../assets/images/logo/MikroTik_idAi6_gbXf_1.svg', 'alt' => 'MikroTik'],
['src' => './../assets/images/logo/Fortinet_idZhbQEBqi_0.svg', 'alt' => 'Fortinet'],
];
@endphp

<section class="brand-marquee" aria-label="Компании и брендови со кои работиме">
  <div class="brand-marquee__track">
    @for ($i = 0; $i < 2; $i++) <div class="brand-marquee__group" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
      @foreach ($brandLogos as $logo)
      <div class="brand-marquee__item">
        <div class="logo-box">
          <img src="{{ asset($logo['src']) }}" alt="{{ $logo['alt'] }}" loading="lazy" />
        </div>
      </div>
      @endforeach
  </div>
  @endfor
  </div>
</section>
