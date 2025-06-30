<?php
use Livewire\Attributes\Rule;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Layout('components.layouts.beranda')] #[Title('Edit Profile')] class extends Component {
    use Toast, WithFileUploads;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|digits_between:10,13|regex:/^[0-9]+$/')]
    public ?string $no_hp = null;

    #[Rule('nullable|string')]
    public ?string $bio = null;

    #[Rule('nullable|integer|exists:roles,id')]
    public ?int $role_id = null;

    #[Rule('nullable|image|max:1024')]
    public $photo;

    #[Rule('required_with:new_password|string')]
    public string $current_password = '';

    #[Rule('nullable|min:8|same:new_password_confirmation')]
    public string $new_password = '';

    #[Rule('nullable')]
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->fill($user->only('name', 'email', 'no_hp', 'bio', 'role_id'));
    }

    public function with(): array
    {
        return [
            'roles' => Role::all(),
        ];
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate();

        $user->update($validated);

        if ($this->photo) {
            if ($user->avatar) {
                $old = public_path(str_replace('/storage', 'storage', $user->avatar));
                if (file_exists($old)) {
                    unlink($old);
                }
            }

            $path = $this->photo->store('users', 'public');
            $user->update(['avatar' => "/storage/$path"]);
        }

        $this->success('Barang updated with success.', redirectTo: '/editProfile');
        $this->success('Profil berhasil diperbarui.');
    }

    public function changePassword(): void
    {
        $this->validate();

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->success('Password berhasil diubah.');
    }
};
?>

<div class="min-h-screen bg-[#FCF9F4] flex flex-col items-center py-10 ">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full max-w-5xl px-4">
        {{-- Kolom 1 - Edit Profil --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <x-form wire:submit="updateProfile" card>
                <x-slot name="title">Edit Profil</x-slot>

                <x-file label="Avatar" wire:model="photo" crop-after-change>
                    <img src="{{ Auth::user()->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>

                <x-input label="Nama" wire:model.defer="name" />
                <x-input label="Email" wire:model.defer="email" />
                <x-input label="No HP" wire:model.defer="no_hp" oninput="this.value = this.value.replace(/\D/g, '')" />
                <x-select label="Peran" wire:model.defer="role_id" :options="$roles" option-label="name"
                    option-value="id" placeholder="Pilih Peran" disabled />
                <x-textarea label="Bio" wire:model.defer="bio" />

                <x-slot name="actions">
                    <x-button label="Simpan Profil" spinner="updateProfile" class="btn-primary" type="submit" />
                </x-slot>
            </x-form>
        </div>

        {{-- Kolom 2 - Ganti Password --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <x-form wire:submit="changePassword">
                <x-slot name="title">Ubah Password</x-slot>

                <x-password label="Password Saat Ini" type="password" wire:model.defer="current_password" right />
                <x-password label="Password Baru" type="password" wire:model.defer="new_password" right />
                <x-password label="Konfirmasi Password Baru" type="password"
                    wire:model.defer="new_password_confirmation" right />

                <x-slot name="actions">
                    <x-button label="Ubah Password" spinner="changePassword" type="submit" class="btn-primary" />
                </x-slot>
            </x-form>
        </div>
    </div>
</div>
