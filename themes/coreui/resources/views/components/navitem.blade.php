<li class="c-sidebar-nav-item {{ !isset($single) ? 'c-sidebar-nav-dropdown' : '' }}">
    <a class="c-sidebar-nav-link {{ !isset($single) ? 'c-sidebar-nav-dropdown-toggle' : '' }} @isset($activeRoute) {{ request()->is($activeRoute) ? 'active' : '' }} @endisset"
       href="{{ $route ?? ''  }}">{{ $title }}</a>

    @if(!isset($single))
        <ul class="c-sidebar-nav-dropdown-items">
            {{ $slot }}
        </ul>
    @endif
</li>
