@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('page-title', 'Đổi mật khẩu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-key me-2"></i>
                    Đổi mật khẩu
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự</div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Strength Indicator -->
                    <div class="mb-3">
                        <div class="password-strength">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="password-strength-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="password-strength-text" class="form-text"></small>
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Yêu cầu mật khẩu:</h6>
                        <ul class="mb-0" id="password-requirements">
                            <li id="req-length" class="text-muted">
                                <i class="fas fa-times me-1"></i>Ít nhất 8 ký tự
                            </li>
                            <li id="req-uppercase" class="text-muted">
                                <i class="fas fa-times me-1"></i>Có ít nhất 1 chữ hoa
                            </li>
                            <li id="req-lowercase" class="text-muted">
                                <i class="fas fa-times me-1"></i>Có ít nhất 1 chữ thường
                            </li>
                            <li id="req-number" class="text-muted">
                                <i class="fas fa-times me-1"></i>Có ít nhất 1 số
                            </li>
                            <li id="req-special" class="text-muted">
                                <i class="fas fa-times me-1"></i>Có ít nhất 1 ký tự đặc biệt
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <i class="fas fa-save me-2"></i>Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const currentPasswordInput = document.getElementById('current_password');
    const submitBtn = document.getElementById('submit-btn');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    function checkPasswordStrength(password) {
        let strength = 0;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Update requirement indicators
        Object.keys(requirements).forEach(req => {
            const element = document.getElementById('req-' + req);
            const icon = element.querySelector('i');
            
            if (requirements[req]) {
                element.classList.remove('text-muted');
                element.classList.add('text-success');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
                strength++;
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        });

        // Update strength bar
        const percentage = (strength / 5) * 100;
        strengthBar.style.width = percentage + '%';
        
        if (strength <= 2) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Yếu';
            strengthText.className = 'form-text text-danger';
        } else if (strength <= 3) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Trung bình';
            strengthText.className = 'form-text text-warning';
        } else if (strength <= 4) {
            strengthBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Khá';
            strengthText.className = 'form-text text-info';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Mạnh';
            strengthText.className = 'form-text text-success';
        }

        return strength >= 3; // Minimum acceptable strength
    }

    function validateForm() {
        const currentPassword = currentPasswordInput.value;
        const newPassword = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        const hasCurrentPassword = currentPassword.length > 0;
        const hasValidNewPassword = checkPasswordStrength(newPassword);
        const passwordsMatch = newPassword === confirmPassword && newPassword.length > 0;
        
        submitBtn.disabled = !(hasCurrentPassword && hasValidNewPassword && passwordsMatch);
        
        // Show password match indicator
        if (confirmPassword.length > 0) {
            if (passwordsMatch) {
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            } else {
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.add('is-invalid');
            }
        } else {
            confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
        }
    }

    passwordInput.addEventListener('input', validateForm);
    confirmPasswordInput.addEventListener('input', validateForm);
    currentPasswordInput.addEventListener('input', validateForm);
});
</script>
@endsection