<div>
    <div class="dashboard-card profile-container">
        @if($message)
            <div class="alert alert-{{ $messageType === 'success' ? 'success' : 'danger' }}">
                {{ $message }}
            </div>
        @endif

        <form wire:submit.prevent="updateProfile">
            <div class="form-group">
                <label for="name" class="form-label form-label-required">
                    {{ __('words.full_name') }}
                </label>
                <input type="text" id="name" wire:model="name" class="quiet-textarea form-input @error('name') error @enderror" placeholder="{{ __('words.enter_full_name') }}" required>
                @error('name')
                <span class="text-danger error-message">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label form-label-required">
                    {{ __('words.email_address') }}
                </label>
                <input type="email" id="email" wire:model="email" class="quiet-textarea form-input @error('email') error @enderror" placeholder="{{ __('words.enter_email_address') }}" required>
                @error('email')
                <span class="text-danger error-message">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <hr class="section-divider">

            <h3 class="section-title">
                {{ __('words.change_password') }}
            </h3>
            <p class="section-description">
                {{ __('words.password_change_description') }}
            </p>

            <div class="form-group">
                <label for="old_password" class="form-label">
                    {{ __('words.current_password') }}
                </label>
                <input type="password" id="old_password" wire:model.live.debounce.500ms="old_password" class="quiet-textarea form-input @error('old_password') error @enderror" placeholder="{{ __('words.enter_current_password') }}">
                @error('old_password')
                <span class="text-danger error-message">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">
                    {{ __('words.new_password') }}
                </label>
                <input type="password" id="new_password" wire:model.live.debounce.500ms="new_password" class="quiet-textarea form-input @error('new_password') error @enderror" placeholder="{{ __('words.enter_new_password') }}">
                @error('new_password')
                <span class="text-danger error-message">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-group form-group-last">
                <label for="confirm_new_password" class="form-label">
                    {{ __('words.confirm_new_password') }}
                </label>
                <input type="password" id="confirm_new_password" wire:model.live.debounce.500ms="confirm_new_password" class="quiet-textarea form-input @error('confirm_new_password') error @enderror" placeholder="{{ __('words.confirm_new_password_placeholder') }}">
                @error('confirm_new_password')
                <span class="text-danger error-message">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="quiet-btn btn-primary">
                    <i class="fas fa-save btn-icon"></i>
                    {{ __('words.update_profile') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('clear-message', () => {
                setTimeout(() => {
                    @this.set('message', '');
                }, 3000);
            });
        });
    </script>
</div>
