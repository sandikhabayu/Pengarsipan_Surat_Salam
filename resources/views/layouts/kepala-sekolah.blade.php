<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARSIPKU - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-center border-b border-gray-700">
                <h1 class="text-2xl font-bold tracking-wide">ARSIPKU</h1>
                <div class="mt-4 flex flex-col items-center">
                    @if (auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                            class="w-16 h-16 rounded-full mb-2 object-cover border-4 border-white" alt="Profile Photo">
                    @else
                        <div
                            class="w-16 h-16 bg-white rounded-full mb-2 flex items-center justify-center border-4 border-white">
                            <span class="text-gray-800 text-2xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <span class="text-sm font-medium mt-1">{{ auth()->user()->name }}</span>
                    <a href="{{ route('kepala-sekolah.profile.index') }}"
                        class="bg-[#17AD90] px-4 py-1 rounded mt-1 text-md text-white font-semibold hover:bg-[#136958]">
                        Profil
                    </a>
                </div>

            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('kepala-sekolah.dashboard') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('kepala-sekolah.dashboard') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h8v8H3V3zm0 10h8v8H3v-8zm10-10h8v8h-8V3zm0 10h8v8h-8v-8z" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('kepala-sekolah.arsip.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('kepala-sekolah.arsip.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    📁 Arsip
                </a>
                <a href="{{ route('kepala-sekolah.laporan.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('kepala-sekolah.laporan.*') ? 'bg-[#17AD90] text-white transition' : 'hover:bg-[#17AD90] transition' }}">
                    📊 Laporan
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-red-600">
                        🚪 Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main -->
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
