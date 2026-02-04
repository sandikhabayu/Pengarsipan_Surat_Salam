<x-guest-layout>
    <div class="min-h-screen bg-cover bg-center flex flex-col items-center justify-center px-4"
        style="background-image: url('{{ asset('images/background.png') }}');">

        <!-- Judul Halaman -->
        <div class="text-center mb-6 text-black">
            <h1 class="text-3xl md:text-4xl font-bold leading-tight">
                Selamat datang di Sistem Pengarsipan<br>Desa Salam
            </h1>
        </div>

        <!-- Card Login -->
        <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md text-center">
            <!-- Ikon Surat & Judul Login -->
            <div class="mb-6">
                <div class="text-4xl mb-2">✉️</div>
                <h2 class="text-xl md:3xl font-bold">LOGIN</h2>
            </div>

            <!-- Status Session -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4 text-left">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" type="email" name="email"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-green-400"
                        placeholder="example@gmail.com" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div class="mb-4 text-left">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" type="password" name="password"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-green-400"
                        placeholder="********" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Checkbox dan Lupa Password -->
                <div class="flex items-center justify-between mb-4">
                    <label for="remember_me" class="flex items-center text-sm text-gray-700">
                        <input id="remember_me" type="checkbox"
                            class="mr-2 rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                            name="remember">
                        Saya bukan robot
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-green-600 hover:underline" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Tombol Login -->
                <x-primary-button>
                    {{ __('LOGIN') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-guest-layout>
