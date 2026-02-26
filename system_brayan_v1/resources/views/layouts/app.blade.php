<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'INFINITY-CARGO v3.0') }}</title>
    <link rel="icon" href="img/logo01.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/vanilla-calendar-pro@2.9.6/build/vanilla-calendar.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/vanilla-calendar-pro@2.9.6/build/vanilla-calendar.min.css"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- It will not apply locale yet --}}
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        flatpickr.localize(flatpickr.l10ns.es);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    <x-mary-nav sticky full-width>
        <x-slot:brand>
            {{-- Drawer toggle for "main-drawer" --}}
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-mary-icon name="o-bars-3" class="cursor-pointer" />
            </label>

            {{-- Brand --}}
            <img src="{{ asset('img/logo01.png') }}" alt='Infinity.ut' class='invisible w-auto h-10 md:visible'>

        </x-slot:brand>

        {{-- Right side actions --}}
        <x-slot:actions>
            <x-mary-icon name="s-home" class="text-center text-green-500 text-md"
                label="{{ auth()->user()->sucursal->name }}" />

            <x-mary-theme-toggle darkTheme="business" lightTheme="light" />

            <x-mary-button label="Messages" icon="o-envelope" link="/message" class="btn-ghost btn-sm" responsive />
            <x-mary-button icon="o-bell" class="relative btn-circle" link="/message">
                @php
                $messages = App\Models\Frontend\Message::where('isActive', true)->get()->count();
                @endphp
                <x-mary-badge value="{{ $messages }}" class="absolute badge-error -right-2 -top-2" />
            </x-mary-button>
            <x-mary-dropdown>
                <x-slot:trigger>
                    <x-mary-button icon="o-user" class="relative" label="{{ auth()->user()->name }}" responsive
                        no-wire-navigate />
                </x-slot:trigger>
                <x-mary-menu-item icon="o-user" title="Perfil" :href="route('profile.edit')" />
                @if ($user = auth()->user())
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-mary-menu-item icon="o-power" title="Cerrar" :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();" />
                </form>
                @endif
            </x-mary-dropdown>
        </x-slot:actions>
    </x-mary-nav>
    @include('components.partials.sidebar')
    <x-mary-toast />
    <x-mary-spotlight />
</body>
</html>
