<ul class="site-navbar__dropdown-list py-2 text-sm text-gray-700">
  @foreach ($children as $child)
    @php $childUrl = $child->getLinkUrl(); @endphp
    <li>
      <a href="{{ $childUrl }}" class="site-navbar__dropdown-link block" @if (request()->url() === $childUrl) aria-current="page" @endif>
        {{ $child->title }}
      </a>
      @if ($child->visibleChildren->isNotEmpty())
        @include('livewire.navbar-children', ['children' => $child->visibleChildren])
      @endif
    </li>
  @endforeach
</ul>
