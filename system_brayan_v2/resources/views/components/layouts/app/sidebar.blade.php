<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light" icon="sun"></flux:radio>
                <flux:radio value="dark" icon="moon"></flux:radio>
            </flux:radio.group>
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo-completo class="w-36 max-w-xs h-auto mx-auto" />
        </a>

        <flux:navlist variant="outline">
            <!-- Dashboard -->
            <flux:navlist.group :heading="__('Principal')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                <flux:navlist.item icon="banknotes" :href="route('caja')" :current="request()->routeIs('caja')"
                    wire:navigate>{{ __('Caja') }}</flux:navlist.item>
            </flux:navlist.group>

            <!-- Encomiendas -->
            <flux:navlist.group expandable :expanded="request()->routeIs('package.encomienda*') || request()->routeIs('package.encomienda.send') || request()->routeIs('package.encomienda.receive') || request()->routeIs('package.encomienda.deliver') || request()->routeIs('package.encomienda.home') || request()->routeIs('package.encomienda.return') || request()->routeIs('package.encomienda.ruta')" :heading="__('Encomiendas')" class="grid">
                <flux:navlist.item icon="cube" :href="route('package.encomienda')"
                    :current="request()->routeIs('package.encomienda')" wire:navigate>{{ __('Crear Encomienda') }}
                </flux:navlist.item>
                <flux:navlist.item icon="paper-airplane" :href="route('package.encomienda.send')"
                    :current="request()->routeIs('package.encomienda.send')" wire:navigate>
                    {{ __('Enviar Encomiendas') }}</flux:navlist.item>
                <flux:navlist.item icon="inbox" :href="route('package.encomienda.receive')"
                    :current="request()->routeIs('package.encomienda.receive')" wire:navigate>
                    {{ __('Recibir Encomiendas') }}</flux:navlist.item>
                <flux:navlist.item icon="gift" :href="route('package.encomienda.deliver')"
                    :current="request()->routeIs('package.encomienda.deliver')" wire:navigate>
                    {{ __('Entregar Encomiendas') }}</flux:navlist.item>
                <flux:navlist.item icon="gift" :href="route('package.encomienda.home')"
                    :current="request()->routeIs('package.encomienda.home')" wire:navigate>
                    {{ __('Entregar domicilio') }}</flux:navlist.item>
                <flux:navlist.item icon="arrow-uturn-left" :href="route('package.encomienda.return')"
                    :current="request()->routeIs('package.encomienda.return')" wire:navigate>
                    {{ __('Retornar Encomiendas') }}</flux:navlist.item>
                <flux:navlist.item icon="map" :href="route('package.encomienda.ruta')"
                    :current="request()->routeIs('package.encomienda.ruta')" wire:navigate>{{ __('Rutas de Envío') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-duplicate" :href="route('package.manifiesto')"
                    :current="request()->routeIs('package.manifiesto')" wire:navigate>{{ __('Manifiestos') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <!-- Facturación -->
            <flux:navlist.group expandable :expanded="request()->routeIs('facturacion.*')" :heading="__('Facturación')" class="grid">
                <flux:navlist.item icon="document-text" :href="route('facturacion.invoice')"
                    :current="request()->routeIs('facturacion.invoice*')" wire:navigate>{{ __('Facturas/Boletas') }}
                </flux:navlist.item>
                <flux:navlist.item icon="ticket" :href="route('facturacion.ticket')"
                    :current="request()->routeIs('facturacion.ticket')" wire:navigate>{{ __('Tickets') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-duplicate" :href="route('facturacion.note')"
                    :current="request()->routeIs('facturacion.note*')" wire:navigate>{{ __('Notas') }}
                </flux:navlist.item>
                <flux:navlist.item icon="truck" :href="route('facturacion.despatche')"
                    :current="request()->routeIs('facturacion.despatche')" wire:navigate>{{ __('Guías de Remisión') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <!-- Reportes -->
            <flux:navlist.group expandable :expanded="request()->routeIs('report.*')" :heading="__('Reportes')" class="grid">
                <flux:navlist.item icon="chart-bar" :href="route('report.encomiendas')"
                    :current="request()->routeIs('report.encomiendas')" wire:navigate>{{ __('Encomiendas') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-chart-bar" :href="route('report.facturacion')"
                    :current="request()->routeIs('report.facturacion')" wire:navigate>{{ __('Facturación') }}
                </flux:navlist.item>
                <flux:navlist.item icon="users" :href="route('report.customers')"
                    :current="request()->routeIs('report.customers')" wire:navigate>{{ __('Clientes') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <!-- Configuración -->
            <flux:navlist.group expandable :expanded="request()->routeIs('configuration.*')" :heading="__('Configuración')" class="grid">
                <flux:navlist.item icon="building-office" :href="route('configuration.sucursal')"
                    :current="request()->routeIs('configuration.sucursal')" wire:navigate>{{ __('Sucursales') }}
                </flux:navlist.item>
                <flux:navlist.item icon="building-office-2" :href="route('configuration.company')"
                    :current="request()->routeIs('configuration.company')" wire:navigate>{{ __('Empresa') }}
                </flux:navlist.item>
                <flux:navlist.item icon="truck" :href="route('configuration.transportista')"
                    :current="request()->routeIs('configuration.transportista')" wire:navigate>
                    {{ __('Transportistas') }}</flux:navlist.item>
                <flux:navlist.item icon="truck" :href="route('configuration.vehiculo')"
                    :current="request()->routeIs('configuration.vehiculo')" wire:navigate>{{ __('Vehículos') }}
                </flux:navlist.item>
                <flux:navlist.item icon="users" :href="route('configuration.customer')"
                    :current="request()->routeIs('configuration.customer')" wire:navigate>{{ __('Clientes') }}
                </flux:navlist.item>
                <flux:navlist.item icon="wallet" :href="route('configuration.tipo-caja')"
                    :current="request()->routeIs('configuration.tipo-caja')" wire:navigate>{{ __('Tipos de Caja') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />



        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    <!-- Toast Notifications (Top Center, Dark Mode Support) -->
    <div x-data="{
        toasts: [],
        addToast(data) {
            const id = Date.now();
            const type = data?.type || data?.[0]?.type || 'info';
            const title = data?.title || data?.[0]?.title || 'Notificación';
            const message = data?.message || data?.[0]?.message || '';
            this.toasts.push({ id, type, title, message });
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }" @toast.window="addToast($event.detail)"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-50 space-y-2 flex flex-col items-center w-full max-w-full pointer-events-none"
        style="max-width: 100vw;">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                :class="{
                    'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/90 dark:border-green-700 dark:text-green-100': toast
                        .type === 'success',
                    'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/90 dark:border-red-700 dark:text-red-100': toast
                        .type === 'error',
                    'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/90 dark:border-blue-700 dark:text-blue-100': toast
                        .type === 'info',
                    'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/90 dark:border-yellow-700 dark:text-yellow-100': toast
                        .type === 'warning'
                }"
                class="min-w-[300px] max-w-md rounded-lg border p-4 shadow-lg dark:shadow-xl dark:shadow-black/20 mx-auto pointer-events-auto backdrop-blur-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 pt-0.5">
                        <template x-if="toast.type === 'success'">
                            <flux:icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-300" />
                        </template>
                        <template x-if="toast.type === 'error'">
                            <flux:icon name="x-circle" class="w-5 h-5 text-red-600 dark:text-red-300" />
                        </template>
                        <template x-if="toast.type === 'info'">
                            <flux:icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-300" />
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <flux:icon name="exclamation-triangle"
                                class="w-5 h-5 text-yellow-600 dark:text-yellow-300" />
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-sm dark:text-white" x-text="toast.title"></h4>
                        <p class="text-sm mt-1 opacity-90 dark:opacity-80" x-text="toast.message"></p>
                    </div>
                    <button @click="removeToast(toast.id)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-150"
                        type="button" aria-label="Cerrar notificación">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </template>
    </div>

    @fluxScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('toast', (data) => {
                // Disparar evento personalizado para Alpine.js
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: Array.isArray(data) ? data[0] : data
                }));
            });
        });
    </script>
</body>

</html>
