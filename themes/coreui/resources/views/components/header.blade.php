<header class="c-header c-header-light c-header-fixed c-header-with-subheader">


    <ul class="c-header-nav ml-auto mr-4">

        <li class="c-header-nav-item dropdown"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="c-avatar"><img class="c-avatar-img" src="{{  'https://eu.ui-avatars.com/api/?name=' . str_replace(' ', '+', auth()->user()->name) }}" alt="user@email.com"></div>
            </a>
            <div class="dropdown-menu dropdown-menu-right pt-0">
                <a class="dropdown-item" href="{{ route('getlogout') }}">
                    <svg class="c-icon mr-2">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                    </svg> Logout</a>
            </div>
        </li>
    </ul>
</header>
