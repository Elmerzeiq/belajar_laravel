@extends('layouts.mantis')

@section('content')
<div class="container mt-4" style="max-width:700px;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex align-items-center" style="min-height:140px;">
            <!-- Avatar Upload -->
            <div class="flex-shrink-0 position-relative" style="width:80px;">
                <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="avatar-input" name="avatar" class="d-none" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('template/dist/assets/images/user/avatar-2.jpg') }}"
                         alt="Avatar"
                         class="rounded-circle border"
                         style="width:80px; height:80px; object-fit:cover; background:#eaf4ff; cursor:pointer;"
                         onclick="document.getElementById('avatar-input').click();"
                         title="Klik untuk mengubah foto profil">
                    <div class="text-center mt-2" style="font-size:0.88rem; color:#888;">Klik foto untuk ubah</div>
                </form>
            </div>
            <!-- Info User -->
            <div class="ms-4">
                <div class="fw-bold" style="font-size:1.15rem;">{{ $user->name }}</div>
                <div class="text-muted" style="font-size:0.97rem;">Email: {{ $user->email }}</div>
                <div class="text-muted" style="font-size:0.97rem;">
                    Role:
                    @if(method_exists($user, 'getRoleNames'))
                        {{ $user->getRoleNames()->implode(', ') }}
                    @else
                        {{ $user->role ?? '-' }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Form Ubah Password -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-semibold fs-5">
            <i class="bi bi-key me-2 text-primary"></i> Ubah Password
        </div>
        <div class="card-body">
            <form action="{{ route('profile.password') }}" method="POST" style="max-width:430px;">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control rounded" id="password-field" required minlength="6" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary" tabindex="-1" onclick="togglePassword('password-field', this)">
                            <span class="bi bi-eye"></span>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control rounded" id="password-confirm-field" required minlength="6" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary" tabindex="-1" onclick="togglePassword('password-confirm-field', this)">
                            <span class="bi bi-eye"></span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary rounded w-100 fw-semibold">
                    <i class="bi bi-arrow-repeat me-1"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === "password") {
        input.type = "text";
        btn.querySelector('span').classList.remove('bi-eye');
        btn.querySelector('span').classList.add('bi-eye-slash');
    } else {
        input.type = "password";
        btn.querySelector('span').classList.remove('bi-eye-slash');
        btn.querySelector('span').classList.add('bi-eye');
    }
}
</script>
@endpush
