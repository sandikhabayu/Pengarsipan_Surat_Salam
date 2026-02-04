<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
    <title>Petugas - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-center border-b border-gray-700">
                <h1 class="text-xl font-bold tracking-wide">ARSIPKU</h1>
                <div class="mt-4 flex flex-col items-center">
                    @if (auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                            class="w-16 h-16 rounded-full mb-2 object-cover" alt="Profile Photo">
                    @else
                        <div class="w-16 h-16 bg-white rounded-full mb-2 flex items-center justify-center">
                            <span class="text-gray-800 text-2xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                    <a href="{{ route('petugas.profile.index') }}"
                        class="bg-[#17AD90] px-4 py-1 rounded mt-1 text-md text-white font-semibold hover:bg-[#136958]">
                        Profil
                    </a>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('petugas.dashboard') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('petugas.dashboard') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3H11V11H3V3Z" />
                        <path d="M3 13H11V21H3V13Z" />
                        <path d="M13 3H21V11H13V3Z" />
                        <path d="M13 13H21V21H13V13Z" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('petugas.surat-masuk.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('petugas.surat-masuk.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    📩 Surat Masuk
                </a>
                <a href="{{ route('petugas.surat-keluar.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('petugas.surat-keluar.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    ✉️ Surat Keluar
                </a>
                <a href="{{ route('petugas.arsip.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('petugas.arsip.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    📁 Arsip
                </a>
                <a href="{{ route('petugas.laporan.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('petugas.laporan.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    📊 Laporan
                </a>
                <a href="{{ route('petugas.surat-template.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('petugas.surat-template.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    ✉️ Buat Surat
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-red-600">
                        🚪 Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 bg-gray-100 p-8 ml-64 relative">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">@yield('title')</h2>
                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>

            @if (session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>

</html>
