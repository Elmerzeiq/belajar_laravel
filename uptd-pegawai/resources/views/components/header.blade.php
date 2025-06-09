<div>
  <header class="pc-header">
    <div class="header-wrapper">
      <div class="ms-auto d-flex align-items-center gap-3">
        <!-- User Profile Dropdown -->
        <ul class="list-unstyled mb-0 d-flex align-items-center gap-2">
          <li class="dropdown pc-h-item header-user-profile">
            <a
              class="pc-head-link dropdown-toggle arrow-none me-0 d-flex align-items-center"
              data-bs-toggle="dropdown"
              href="#"
              role="button"
              aria-haspopup="false"
              aria-expanded="false"
            >
              <img src="{{ asset('template/dist/assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar me-2">
              <span>{{ auth()->user()->name }}</span>
            </a>

            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
              <!-- Isi dropdown seperti sebelumnya -->
              <!-- ... -->
            </div>
          </li>
        </ul>

        <!-- Logout Button di sebelah kanan user -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
          @csrf
          <button type="submit" class="btn btn-link text-danger p-0 m-0" style="text-decoration: none;">
            <i class="ti ti-power"></i> Logout
          </button>
        </form>
      </div>
    </div>
  </header>
</div>
