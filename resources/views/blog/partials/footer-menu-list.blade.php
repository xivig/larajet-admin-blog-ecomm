@php
    $rootItems = $menu?->rootItems ?? collect();
    if (!empty($limit) && $limit > 0) {
        $rootItems = $rootItems->take((int) $limit);
    }
@endphp

<ul class="{{ $listClass ?? 'list-unstyled footer-menu lh-35 mb-0' }}">
    @if ($menu)
        @forelse ($rootItems as $item)
            @php
                $itemUrl = $item->isPagesPlaceholder() ? url('/') : $item->resolvedUrl();
                $target = $item->target ?? '_self';
            @endphp
            <li>
                <a href="{{ $itemUrl }}" target="{{ $target }}" @if($target === '_blank') rel="noopener noreferrer" @endif>
                    {{ $item->title }}
                </a>
            </li>
            @foreach ($item->children as $child)
                @php $childTarget = $child->target ?? '_self'; @endphp
                <li class="ps-2">
                    <a href="{{ $child->resolvedUrl() }}" target="{{ $childTarget }}" @if($childTarget === '_blank') rel="noopener noreferrer" @endif>
                        &rsaquo; {{ $child->title }}
                    </a>
                </li>
            @endforeach
        @empty
            <li class="text-muted small">No links configured</li>
        @endforelse
    @else
        @foreach ($fallbackLinks ?? [] as $link)
            <li><a href="{{ $link['url'] }}">{{ $link['title'] }}</a></li>
        @endforeach
    @endif
</ul>
