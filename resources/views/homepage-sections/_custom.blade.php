{{-- Renders admin-created homepage sections (HomepageSection with content JSON). --}}
@php
    $c = is_array($section->content ?? null) ? $section->content : [];

    $bg = $c['background'] ?? 'light';
    $isDarkBg = in_array($bg, ['dark', 'accent'], true);

    // Background classes reuse the same palettes as the built-in sections.
    $bgClass = match ($bg) {
        'dark'   => 'bg-zinc-900',
        'accent' => 'bg-[#ff6d63]',
        'white'  => 'bg-white',
        default  => 'bg-zinc-100',
    };

    $buttons = array_values(array_filter($c['buttons'] ?? [], fn ($b) => !empty($b['label']) && !empty($b['url'])));
@endphp

<section class="py-16 {{ $bgClass }}">
  <div class="max-w-5xl mx-auto px-6">

    <div class="rounded-3xl p-10 md:p-14 text-center {{ $isDarkBg ? '' : 'bg-white shadow-lg' }}">

      @if (!empty($c['eyebrow']))
        <span class="block text-sm font-medium uppercase tracking-wide {{ $isDarkBg ? 'text-white/70' : 'text-zinc-500' }}">
          {{ $c['eyebrow'] }}
        </span>
      @endif

      @if (!empty($c['heading']))
        <h2 class="{{ !empty($c['eyebrow']) ? 'mt-3' : '' }} text-2xl md:text-4xl {{ $isDarkBg ? 'text-white' : 'text-zinc-900' }} {{ $isDarkBg ? 'font-medium' : 'font-thin' }}">
          {{ $c['heading'] }}
        </h2>
      @endif

      @if (!empty($c['body']))
        <p class="mt-4 {{ $isDarkBg ? 'text-white/80' : 'text-zinc-600' }} text-base md:text-lg max-w-2xl mx-auto">
          {{ $c['body'] }}
        </p>
      @endif

      @if (!empty($c['image']))
        <img src="{{ $c['image'] }}" alt="{{ $c['heading'] ?? $section->name }}"
             class="mt-8 mx-auto max-w-full rounded-2xl" loading="lazy" />
      @endif

      @if (count($buttons))
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
          @foreach ($buttons as $button)
            @if (($button['style'] ?? 'primary') === 'primary')
              <a href="{{ $button['url'] }}"
                class="px-8 py-4 rounded-full font-medium transition {{ $isDarkBg ? 'bg-white text-zinc-900 hover:bg-zinc-100' : 'bg-zinc-900 text-white hover:bg-zinc-800' }}">
                {{ $button['label'] }}
              </a>
            @else
              <a href="{{ $button['url'] }}"
                class="px-8 py-4 rounded-full border font-medium transition {{ $isDarkBg ? 'border-white/40 text-white hover:bg-white/10' : 'border-zinc-300 text-zinc-800 hover:bg-zinc-100' }}">
                {{ $button['label'] }}
              </a>
            @endif
          @endforeach
        </div>
      @endif

    </div>

  </div>
</section>
