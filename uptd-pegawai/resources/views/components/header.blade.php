<div>
    <header class="pc-header">
        <div class="header-wrapper">
            <div class="ms-auto d-flex align-items-center gap-3">
                <!-- User Profile Direct Link -->
                <ul class="list-unstyled mb-0 d-flex align-items-center gap-2">
                    <li class="pc-h-item header-user-profile">
                        <a class="pc-head-link arrow-none me-0 d-flex align-items-center"
                            href="{{ route('profile') }}">
                            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('template/dist/assets/images/user/avatar-2.jpg') }}"
                                alt="user-image" class="user-avtar me-2">
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Logout Button di sebelah kanan user -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0 m-0" style="text-decoration: none;">
                        <i class="ti ti-power"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>
</div>
