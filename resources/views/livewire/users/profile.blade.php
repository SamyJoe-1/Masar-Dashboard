<div>
    <div class="dashboard-card profile-container">
        <form wire:submit.prevent="updateProfile">
            <!-- Avatar Section -->
            <div class="profile-avatar-section">
                <div class="avatar-container">
                    @if($current_avatar)
                        <img wire:ignore src="{{ $current_avatar }}" alt="{{ __('words.profile_avatar') }}" class="profile-avatar">
                    @else
                        <div class="profile-avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="file" id="avatar_file" wire:model="avatar_file" class="form-input @error('avatar_file') error @enderror" accept="image/*">
                    @error('avatar_file')
                    <span class="text-danger error-message">
                        {{ $message }}
                    </span>
                    @enderror
                    <small class="form-help">{{ __('words.avatar_upload_help') }}</small>
                </div>
            </div>

            <hr class="section-divider">

            <div class="d-flex justify-content-between" style="gap: 20px;flex-wrap: wrap">
                <div style="flex: 1 1 auto">
                    <!-- Basic Information Section -->
                    <h3 class="section-title">
                        {{ __('words.basic_information') }}
                    </h3>

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

                    <div class="form-group">
                        <label for="position" class="form-label">
                            {{ __('words.current_position') }}
                        </label>
                        <input type="text" id="position" wire:model="position" class="quiet-textarea form-input @error('position') error @enderror" placeholder="{{ __('words.enter_current_position') }}">
                        @error('position')
                            <span class="text-danger error-message">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="bio" class="form-label">
                            {{ __('words.bio') }}
                        </label>
                        <textarea id="bio" wire:model="bio" class="quiet-textarea @error('bio') error @enderror" placeholder="{{ __('words.enter_bio') }}" rows="8" style="width: 100%"></textarea>
                        @error('bio')
                        <span class="text-danger error-message">
                            {{ $message }}
                        </span>
                        @enderror
                        <small class="form-help" style="display: block">{{ __('words.bio_help') }}</small>
                    </div>

                    <!-- Nationality Field -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('words.Nationality') }}:
                        </label>
                        <div class="flex space-x-4 radio-group" style="display: inline;margin: 0 10px">
                            <label class="flex items-center">
                                <input type="radio" wire:model="nationality" value="1"
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                {{ __('words.Omani') }}
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model="nationality" value="0"
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                {{ __('words.Non-Omani') }}
                            </label>
                        </div>
                        @error('nationality')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
                <div style="flex: 1 1 auto">
                    <!-- Professional Information Section -->
                    <h3 class="section-title">
                        {{ __('words.professional_information') }}
                    </h3>

                    <div class="form-group">
                        <label for="education" class="form-label">
                            {{ __('words.education') }}
                        </label>
                        <textarea id="education" wire:model="education" class="quiet-textarea form-input @error('education') error @enderror" placeholder="{{ __('words.enter_education') }}" rows="3"></textarea>
                        @error('education')
                        <span class="text-danger error-message">
                    {{ $message }}
                </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="college" class="form-label">
                            {{ __('words.college_university') }}
                        </label>
                        <input type="text" id="college" wire:model="college" class="quiet-textarea form-input @error('college') error @enderror" placeholder="{{ __('words.enter_college') }}">
                        @error('college')
                        <span class="text-danger error-message">
                    {{ $message }}
                </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_job" class="form-label">
                            {{ __('words.last_job') }}
                        </label>
                        <input type="text" id="last_job" wire:model="last_job" class="quiet-textarea form-input @error('last_job') error @enderror" placeholder="{{ __('words.enter_last_job') }}">
                        @error('last_job')
                        <span class="text-danger error-message">
                    {{ $message }}
                </span>
                        @enderror
                    </div>

                    <!-- Suggested Roles Section -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('words.suggested_roles') }}
                        </label>
                        <div style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                <tr>
                                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd; width: 80%;">{{ __("words.Role") }}</th>
                                    <th style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd; width: 20%;">{{ __("words.Action") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($suggested_roles as $index => $role)
                                    <tr>
                                        <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                            <input type="text" wire:model="suggested_roles.{{ $index }}"
                                                   class="quiet-textarea form-input @error('suggested_roles.'.$index) error @enderror"
                                                   placeholder="Enter role" style="width: 100%; margin: 0;">
                                            @error('suggested_roles.'.$index)
                                            <span class="text-danger error-message" style="font-size: 12px;">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">
                                            <button type="button" wire:click="removeSuggestedRole({{ $index }})"
                                                    class="quiet-btn btn-danger" style="padding: 4px 8px; font-size: 12px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" wire:click="addSuggestedRole" class="quiet-btn btn-secondary" style="margin-top: 10px;">
                            <i class="fas fa-plus btn-icon"></i>
                            {{ __("words.Add New Role") }}
                        </button>
                    </div>

                    <!-- CV Section -->
                    <div class="form-group">
                        <label for="cv_file" class="form-label">
                            {{ __('words.cv_resume') }}
                        </label>
                        <div class="cv-upload-section">
                            <input type="file" id="cv_file" wire:model="cv_file" class="form-input @error('cv_file') error @enderror" accept=".pdf,.doc,.docx">
                            @if($current_cv)
                                <a style="text-decoration: none" href="{{ $current_cv }}" class="quiet-btn btn-secondary cv-download-btn" download>
                                    <i class="fas fa-download btn-icon"></i>
                                    {{ __('words.download_cv') }}
                                </a>

                            @endif
                        </div>
                        @error('cv_file')
                        <span class="text-danger error-message">
                            {{ $message }}
                        </span>
                        @enderror
                        <small class="form-help">{{ __('words.cv_upload_help') }}</small>
                    </div>
                </div>
            </div>

            <hr class="section-divider">

            <!-- Password Change Section -->
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

            <div class="form-actions" style="display: flex;justify-content: center">
                <button type="submit" class="quiet-btn2" wire:loading.attr="disabled" wire:target="updateProfile">
                    <span wire:loading.remove wire:target="updateProfile">
                        <i class="fas fa-save btn-icon"></i>
                        {{ __('words.update_profile') }}
                    </span>
                    <span wire:loading wire:target="updateProfile">
                        <i class="fas fa-spinner fa-spin btn-icon"></i>
                        {{ __('words.updating') }}...
                    </span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <x-alerts.livewireSweetAlert></x-alerts.livewireSweetAlert>
    @endpush
</div>
