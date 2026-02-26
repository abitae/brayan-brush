<x-mary-main with-nav full-width>
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100">
        <x-mary-menu activate-by-route>
            <x-mary-menu-separator />
            <x-mary-menu-item title="DASHBOARD" icon="o-rectangle-group" link="{{ route('dashboard') }}" />


            <x-mary-menu-item title="TUTORIALES" icon="o-rectangle-group" link="{{ route('tutorial.video') }}" />

            @can('caja.index')
                <x-mary-menu-separator />
                <x-mary-menu-item title="CAJA" icon="o-banknotes" link="{{ route('caja.index') }}" />
            @endcan

            @can('config.ruta')
                <x-mary-menu-item title="RUTAS" icon="s-paper-airplane" link="{{ route('config.configuration') }}" />
            @endcan

            @can('menu.encomienda')
                <x-mary-menu-separator />
                <x-mary-menu-sub title="ENCOMIENDAS" icon="s-truck">
                    @can('package.register')
                        <x-mary-menu-item title="Registrar" icon="o-cursor-arrow-rays" link="{{ route('package.register') }}" />
                    @endcan
                    @can('package.send')
                        <x-mary-menu-item title="Enviar" icon="c-arrow-up-tray" link="{{ route('package.send') }}" />
                    @endcan
                    @can('package.receive')
                        <x-mary-menu-item title="Recibir" icon="c-arrow-down-tray" link="{{ route('package.receive') }}" />
                    @endcan
                    @can('package.manifiesto')
                        <x-mary-menu-item title="Descarga manifiestos" icon="s-inbox-arrow-down"
                            link="{{ route('package.manifiesto') }}" />
                    @endcan
                </x-mary-menu-sub>
            @endcan

            @can('menu.entrega')
                <x-mary-menu-sub title="ENTREGAR" icon="s-truck">
                    @can('package.deliver')
                        <x-mary-menu-item title="Agencia" icon="o-cursor-arrow-ripple" link="{{ route('package.deliver') }}" />
                    @endcan
                    @can('package.home')
                        <x-mary-menu-item title="Domicilio" icon="o-cursor-arrow-ripple" link="{{ route('package.home') }}" />
                    @endcan
                    @can('package.return')
                        <x-mary-menu-item title="Retorno" icon="o-cursor-arrow-ripple" link="{{ route('package.return') }}" />
                    @endcan
                    @can('package.record')
                        <x-mary-menu-item title="Historial" icon="m-table-cells" link="{{ route('package.record') }}" />
                    @endcan
                    @can('package.customer')
                        <x-mary-menu-item title="Clientes" icon="o-user-group" link="{{ route('package.customer') }}" />
                    @endcan
                </x-mary-menu-sub>
            @endcan

            @can('menu.facturacion')
                <x-mary-menu-separator />
                <x-mary-menu-sub title="FACTURACION" icon="s-banknotes">
                    @can('facturacion.create-invoice')
                        <x-mary-menu-item title="Emitir Factura" icon="o-ticket"
                            link="{{ route('facturacion.create-invoice') }}" />
                        <x-mary-menu-item title="Emitir Boleta" icon="o-ticket"
                            link="{{ route('facturacion.create-invoice') }}" />
                    @endcan
                    @can('facturacion.create-note')
                        <x-mary-menu-item title="Emitir Nota Credito" icon="o-ticket"
                            link="{{ route('facturacion.create-note') }}" />
                    @endcan
                </x-mary-menu-sub>
            @endcan

            @can('menu.reporte')
                <x-mary-menu-separator />
                <x-mary-menu-sub title="REPORTES" icon="o-cog-6-tooth">
                    @can('report.encomienda')
                        <x-mary-menu-item title="Encomiendas" icon="o-home" link="{{ route('report.encomiendas') }}" />
                    @endcan
                    @can('facturacion.invoice')
                        <x-mary-menu-item title="Boletas y facturas" icon="c-ticket"
                            link="{{ route('facturacion.invoice') }}" />
                    @endcan
                    @can('facturacion.ticket')
                        <x-mary-menu-item title="Ticket Envio" icon="c-ticket" link="{{ route('facturacion.ticket') }}" />
                    @endcan
                    @can('facturacion.despache')
                        <x-mary-menu-item title="Guias Transportista" icon="s-ticket"
                            link="{{ route('facturacion.despache') }}" />
                    @endcan
                    @can('facturacion.note')
                        <x-mary-menu-item title="Notas Credito" icon="s-ticket" link="{{ route('facturacion.note') }}" />
                    @endcan
                </x-mary-menu-sub>
            @endcan

            <x-mary-menu-separator />
            <x-mary-menu-sub title="CUENTAS POR COBRAR" icon="o-cog-6-tooth">
                <x-mary-menu-item title="Cobrar encomiendas" icon="o-home" link="{{ route('cobrar.encomiendas') }}" />
            </x-mary-menu-sub>

            @can('menu.configuracion')
                <x-mary-menu-separator />
                <x-mary-menu-sub title="CONFIGURACION" icon="o-cog-6-tooth">
                    @can('config.company')
                        <x-mary-menu-item title="Company" icon="o-home" link="{{ route('config.company') }}" />
                    @endcan
                    @can('config.sucursal')
                        <x-mary-menu-item title="Sucursales" icon="o-home-modern" link="{{ route('config.sucursal') }}" />
                    @endcan
                    @can('config.user')
                        <x-mary-menu-item title="Usuarios" icon="o-user" link="{{ route('config.user') }}" />
                    @endcan
                    @can('config.role')
                        <x-mary-menu-item title="Roles" icon="o-users" link="{{ route('config.role') }}" />
                    @endcan
                    @can('config.vehiculo')
                        <x-mary-menu-item title="Vehiculos" icon="m-truck" link="{{ route('config.vehiculo') }}" />
                    @endcan
                    @can('config.transportista')
                        <x-mary-menu-item title="Choferes" icon="o-user-circle" link="{{ route('config.transportista') }}" />
                    @endcan
                </x-mary-menu-sub>
            @endcan

            <x-mary-menu-separator />
            @can('message.frontend')
                <x-mary-menu-item title="Messages" icon="o-envelope" link="{{ route('message.frontend') }}" />
            @endcan
            @can('reclamaciones.frontend')
                <x-mary-menu-item title="Reclamaciones" icon="o-envelope"
                    link="{{ route('reclamaciones.frontend') }}" />
            @endcan
            <x-mary-menu-separator />
        </x-mary-menu>
    </x-slot:sidebar>
    <x-slot:content>
        {{ $slot }}
    </x-slot:content>
</x-mary-main>
