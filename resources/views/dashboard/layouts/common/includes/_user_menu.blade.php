<li class="hdr-itm dropdown user-dropdown ">
    <a class="app-head-link dropdown-toggle no-caret me-0" data-bs-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" aria-expanded="false">
        <span class="avtar"><img src="{{ asset('dashboard/assets/images/user/avatar-2.jpg') }}" alt=""></span>
    </a>
    <div class="dropdown-menu header-dropdown">
        <ul class="p-0">
            <li class="mt-3 mb-0">
                <p class="text-center">
                    {{ucfirst(get_user_data()?->name)}}
                </p>
            </li>
            <li class="dropdown-item">
                <a href="#" class="drp-link">
                    Edit Profile
                </a>
            </li>
            <hr class="dropdown-divider">
            <li class="dropdown-item ">
                <a href="#" class="drp-link">
                    <span>Account Settings</span>
                </a>
            </li>
            <hr class="dropdown-divider">
            <li class="dropdown-item ">
                <a href="#" class="drp-link">
                    <span>Wallet</span>
                </a>
            </li>
            <hr class="dropdown-divider">
            <li class="dropdown-item ">
                <a href="#" class="drp-link">
                    <span>Billing</span>
                </a>
            </li>
            <hr class="dropdown-divider">
            <li class="dropdown-item">
                @php
                $authContext = (new \App\Services\Auth\GuardResolver())->resolve(request());
                $guard = $authContext['guard'];
            
                @endphp

                <form action="{{ route($guard . '.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-transparent border-0 drp-link w-100 text-start">
                        <i data-feather="log-out"></i>
                        <span>{{ trans('dashboard/auth.logout') }}</span>
                    </button>
                </form>
            </li>
            </li>
        </ul>
    </div>
</li>
