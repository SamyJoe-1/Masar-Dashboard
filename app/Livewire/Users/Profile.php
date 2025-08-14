<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Component
{
    public $name;
    public $email;
    public $old_password;
    public $new_password;
    public $confirm_new_password;

    public $message = '';
    public $messageType = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'old_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'required_with:old_password', 'confirmed:confirm_new_password', Password::min(8)],
            'confirm_new_password' => 'nullable|required_with:new_password|same:new_password',
        ];
    }

    public function updateProfile()
    {
        $this->validate();

        $user = Auth::user();

        // Check old password if trying to change password
        if ($this->old_password) {
            if (!Hash::check($this->old_password, $user->password)) {
                $this->addError('old_password', __('words.current_password_incorrect'));
                return;
            }
        }

        // Update user data
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Add new password if provided
        if ($this->new_password) {
            $updateData['password'] = Hash::make($this->new_password);
        }

        $user->update($updateData);

        // Clear password fields
        $this->old_password = '';
        $this->new_password = '';
        $this->confirm_new_password = '';

        $this->message = __('words.profile_updated_success');
        $this->messageType = 'success';

        // Clear message after 3 seconds
        $this->dispatch('clear-message');
    }

    public function render()
    {
        return view('livewire.users.profile');
    }
}
