<li class="c-sidebar-nav-item {{ !isset($single) ? 'c-sidebar-nav-dropdown' : '' }}">
    <a class="c-sidebar-nav-link {{ !isset($single) ? 'c-sidebar-nav-dropdown-toggle' : '' }} {{ (request()->is($activeRoute)) ? 'active' : '' }}"
       href="{{ $route }}">{{ $title }}</a>

    @if(!isset($single))
        <ul class="c-sidebar-nav-dropdown-items">
            {{ $slot }}
        </ul>
    @endif
</li>
