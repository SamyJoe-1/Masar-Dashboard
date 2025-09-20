<?php

namespace App\Livewire\Users;

use Dflydev\DotAccessData\Data;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\File;

class Profile extends Component
{
    use WithFileUploads;

    // User basic info
    public $name;
    public $email;
    public $old_password;
    public $new_password;
    public $confirm_new_password;

    // Profile info
    public $education;
    public $college;
    public $position;
    public $last_job;
    public $bio;
    public $nationality; // Added nationality field

    // Files
    public $avatar_file;
    public $cv_file;
    public $current_avatar;
    public $current_cv;

    // Suggested roles
    public $suggested_roles = [];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        // Load profile data if exists
        if ($user->profile) {
            $this->education = $user->profile->education;
            $this->college = $user->profile->college;
            $this->position = $user->profile->position;
            $this->last_job = $user->profile->last_job;
            $this->bio = $user->profile->bio;
            $this->nationality = $user->profile->nationality; // Load nationality
            $this->current_avatar = !empty($user->profile->avatar) ? @$user->profile->getAvatar():null;
            $this->current_cv = !empty($user->profile->cv) ? @$user->profile->getCV():null;

            // Load suggested roles as simple array
            $this->suggested_roles = $user->profile->suggested_roles ? json_decode($user->profile->suggested_roles, true) : [''];
        } else {
            $this->suggested_roles = [''];
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'old_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'required_with:old_password', 'confirmed:confirm_new_password', Password::min(8)],
            'confirm_new_password' => 'nullable|required_with:new_password|same:new_password',
            'education' => 'nullable|string|max:500',
            'college' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'last_job' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'nationality' => 'nullable|in:1,0', // Added nationality validation
            'avatar_file' => 'nullable|image|max:2048', // 2MB max
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'suggested_roles.*' => 'nullable|string|max:255',
        ];
    }

    public function addSuggestedRole()
    {
        $this->suggested_roles[] = '';
    }

    public function removeSuggestedRole($index)
    {
        unset($this->suggested_roles[$index]);
        $this->suggested_roles = array_values($this->suggested_roles);

        if (empty($this->suggested_roles)) {
            $this->suggested_roles = [''];
        }
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

        // Update user basic data
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Add new password if provided
        if ($this->new_password) {
            $updateData['password'] = Hash::make($this->new_password);
        }

        $user->update($updateData);

        // Handle profile data
        $profileData = [
            'education' => $this->education,
            'college' => $this->college,
            'position' => $this->position,
            'last_job' => $this->last_job,
            'bio' => $this->bio,
            'nationality' => $this->nationality, // Added nationality to profile data
            'suggested_roles' => json_encode(array_filter($this->suggested_roles, function($role) {
                return !empty(trim($role));
            })),
        ];

        // Handle avatar upload
        if ($this->avatar_file) {
            $avatarFile = $this->uploadFile($this->avatar_file, 'avatars');
            if ($avatarFile) {
                $profileData['avatar'] = $avatarFile->id;
            }
        }

        // Handle CV upload
        if ($this->cv_file) {
            $cvFile = $this->uploadFile($this->cv_file, 'cvs');
            if ($cvFile) {
                $profileData['cv'] = $cvFile->id;
            }
        }

        // Create or update profile
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $profileData['user_id'] = $user->id;
            \App\Models\Profile::create($profileData);
        }

        // Clear password fields and files
        $this->old_password = '';
        $this->new_password = '';
        $this->confirm_new_password = '';
        $this->avatar_file = null;
        $this->cv_file = null;

        // Refresh profile data
        $user->refresh();
        $this->current_avatar = $user->profile->avatar ?? null;
        $this->current_cv = $user->profile->cv ?? null;

        // Dispatch SweetAlert success event
        $this->dispatch('swal:success', [
            'title' => __('words.success'),
            'text' => __('words.profile_updated_success'),
            'icon' => 'success'
        ]);
    }

    private function uploadFile($file, $folder)
    {
        try {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($folder, $filename, 'public');

            return File::create([
                'name' => $filename,
                'path' => $path,
                'fullpath' => '/storage/' . $path,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]);
        } catch (\Exception $e) {
            // Dispatch SweetAlert error event
            $this->dispatch('swal:error', [
                'title' => __('words.error'),
                'text' => __('words.file_upload_error'),
                'icon' => 'error'
            ]);
            return null;
        }
    }

    public function downloadCV()
    {
        $user = Auth::user();
        if ($user->profile && $user->profile->cv) {
            $file = $user->profile->getCV();
            return response()->download($file, $file->filename);
        }

        // Dispatch SweetAlert error event
        $this->dispatch('swal:error', [
            'title' => __('words.error'),
            'text' => __('words.cv_not_found'),
            'icon' => 'error'
        ]);
    }

    public function render()
    {
        return view('livewire.users.profile');
    }
}
