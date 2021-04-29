<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link {{ (request()->is($activeRoute)) ? 'active' : '' }}"
       href="{{ $route }}">{{ $title }}</a>
</li>
