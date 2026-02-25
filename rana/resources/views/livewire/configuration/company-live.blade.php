<div class="p-6 w-full">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-zinc-100 overflow-hidden mb-6 p-4">
        <div class="px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <flux:icon name="building-office" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-zinc-900">Gestión de Compañía</h1>
                            <p class="text-zinc-500 text-xs">Administra la información de tu empresa</p>
                        </div>
                    </div>
                </div>
                @if(!$company)
                    <div class="flex gap-2">
                        <flux:button wire:click="openCompanyModal()" icon="plus" variant="primary" size="xs" class="flex items-center gap-2">
                            Registrar Compañía
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="mb-4">
            <div class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 flex items-start gap-2">
                <div class="flex-shrink-0">
                    <flux:icon name="check-circle" class="w-4 h-4 text-green-600" />
                </div>
                <div class="flex-1">
                    <p class="text-xs font-medium text-green-800">{{ session('message') }}</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="text-green-400 hover:text-green-600"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Card Única de Compañía -->
    <div class="bg-white rounded-lg shadow-sm border border-zinc-100 overflow-hidden">
        @if($company)
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <flux:icon name="building-office" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-zinc-900 mb-1">{{ $company->razonSocial }}</h2>
                        <div class="text-zinc-500 text-xs">RUC: {{ $company->ruc }}</div>
                    </div>
                    @if($company->logo_path)
                        <div class="flex-shrink-0">
                            <img src="{{ Storage::url($company->logo_path) }}" alt="Logo de la empresa"
                                 class="w-12 h-12 object-contain rounded-lg border border-gray-200">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="space-y-2">
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Dirección</div>
                            <div class="text-sm text-zinc-900">{{ $company->address }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Correo Electrónico</div>
                            <div class="text-sm text-zinc-900">{{ $company->email ?? 'No especificado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Teléfono</div>
                            <div class="text-sm text-zinc-900">{{ $company->telephone ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Ubigeo</div>
                            <div class="text-sm text-zinc-900">{{ $company->ubigeo ?? 'No especificado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Cuenta Banco</div>
                            <div class="text-sm text-zinc-900">{{ $company->ctaBanco ?? 'No especificado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">PIN</div>
                            <div class="text-sm text-zinc-900">{{ $company->pin ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Nro. MTC</div>
                            <div class="text-sm text-zinc-900">{{ $company->nroMtc ?? 'No especificado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Usuario SOL</div>
                            <div class="text-sm text-zinc-900">{{ $company->sol_user ?? 'No especificado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Certificado Digital</div>
                            @if($company->cert_path)
                                <div class="flex items-center gap-2">
                                    <flux:icon name="document-text" class="w-3 h-3 text-green-600" />
                                    <span class="text-xs text-zinc-900">{{ basename($company->cert_path) }}</span>
                                </div>
                            @else
                                <div class="text-sm text-zinc-900">No especificado</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:button wire:click="openCompanyModal({{ $company->id }})" icon="pencil" variant="primary" size="xs">
                        Editar Información
                    </flux:button>
                </div>
            </div>
        @else
            <div class="p-6 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                    <flux:icon name="building-office" class="w-6 h-6 text-zinc-400" />
                </div>
                <h3 class="text-base font-medium text-zinc-900 mb-1">No hay compañía registrada</h3>
                <p class="text-zinc-500 text-sm mb-3">Registra la información de tu empresa para comenzar</p>
                <flux:button wire:click="openCompanyModal()" icon="plus" variant="primary" size="xs">
                    Registrar Compañía
                </flux:button>
            </div>
        @endif
    </div>

    <!-- Modal de Compañía -->
    <flux:modal wire:model="showCompanyModal" variant="flyout" max-width="2xl">
        <div class="px-4 pt-4 pb-2 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-center gap-2 mb-1">
                <div class="p-1.5 bg-blue-100 rounded-lg">
                    <flux:icon name="building-office" class="w-5 h-5 text-blue-600" />
                </div>
                <h2 class="text-lg font-bold text-zinc-900">
                    {{ $editingCompany ? 'Editar Compañía' : 'Nueva Compañía' }}
                </h2>
            </div>
        </div>
        <div class="p-6 max-h-[80vh] overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="ruc" label="RUC" required size="xs" />
                <flux:input wire:model.defer="razonSocial" label="Razón Social" required size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="address" label="Dirección" required size="xs" />
                <flux:input wire:model.defer="email" label="Correo Electrónico" type="email" size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="telephone" label="Teléfono" size="xs" />
                <flux:input wire:model.defer="ubigeo" label="Ubigeo" size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="ctaBanco" label="Cuenta Banco" size="xs" />
                <flux:input wire:model.defer="pin" label="PIN" size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="nroMtc" label="Nro. MTC" size="xs" />
                <flux:input wire:model.defer="sol_user" label="Usuario SOL" size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="sol_pass" label="Contraseña SOL" size="xs" />
                <flux:input wire:model.defer="client_id" label="Client ID" size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="client_secret" label="Client Secret" size="xs" />
                <div class="flex items-center gap-2 mt-1">
                    <flux:checkbox wire:model.defer="production" label="Producción" size="xs" />
                </div>
            </div>

            <!-- Separador visual -->
            <div class="border-t border-gray-200 my-4"></div>

            <!-- Campos de archivos al final -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-gray-700">Logo de la Empresa</label>

                    <!-- Previsualización de imagen actual -->
                    @if($editingCompany && $editingCompany->logo_path)
                        <div class="flex items-center gap-2 mb-1">
                            <img src="{{ Storage::url($editingCompany->logo_path) }}" alt="Logo actual"
                                 class="w-10 h-10 object-contain rounded border border-gray-200">
                            <flux:button wire:click="removeLogo" icon="trash" variant="outline" size="xs"
                                        class="text-red-600 hover:text-red-700">
                                Eliminar
                            </flux:button>
                        </div>
                    @endif

                    <!-- Input de archivo -->
                    <div class="relative">
                        <input type="file" wire:model="logo_path"
                               class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2
                                      file:rounded file:border-0 file:text-xs file:font-medium
                                      file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                      border border-gray-300 rounded cursor-pointer">
                        <div wire:loading wire:target="logo_path" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                        </div>
                    </div>

                    <!-- Previsualización de nueva imagen -->
                    @if($logo_path && $logo_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        <div class="mt-1">
                            <img src="{{ $logo_path->temporaryUrl() }}" alt="Nueva imagen"
                                 class="w-10 h-10 object-contain rounded border border-gray-200">
                        </div>
                    @endif

                    <!-- Mensajes de error -->
                    @error('logo_path')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="text-xs text-gray-500">JPG, PNG, GIF, SVG. Máx. 2MB.</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-gray-700">Certificado Digital</label>

                    <!-- Previsualización de certificado actual -->
                    @if($editingCompany && $editingCompany->cert_path)
                        <div class="flex items-center gap-2 mb-1">
                            <div class="flex items-center gap-1.5 p-1.5 bg-gray-100 rounded">
                                <flux:icon name="document-text" class="w-3 h-3 text-gray-600" />
                                <span class="text-xs text-gray-700">{{ basename($editingCompany->cert_path) }}</span>
                            </div>
                            <flux:button wire:click="removeCert" icon="trash" variant="outline" size="xs"
                                        class="text-red-600 hover:text-red-700">
                                Eliminar
                            </flux:button>
                        </div>
                    @endif

                    <!-- Input de archivo para certificado -->
                    <div class="relative">
                        <input type="file" wire:model="cert_path"
                               class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2
                                      file:rounded file:border-0 file:text-xs file:font-medium
                                      file:bg-green-50 file:text-green-700 hover:file:bg-green-100
                                      border border-gray-300 rounded cursor-pointer">
                        <div wire:loading wire:target="cert_path" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-600"></div>
                        </div>
                    </div>

                    <!-- Previsualización de nuevo certificado -->
                    @if($cert_path && $cert_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        <div class="mt-1">
                            <div class="flex items-center gap-1.5 p-1.5 bg-green-100 rounded">
                                <flux:icon name="document-text" class="w-3 h-3 text-green-600" />
                                <span class="text-xs text-green-700">{{ $cert_path->getClientOriginalName() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Mensajes de error para certificado -->
                    @error('cert_path')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="text-xs text-gray-500">P12, PEM, CER, CRT, KEY. Máx. 5MB.</p>
                </div>
            </div>
        </div>
        <div class="px-4 pb-4 pt-2 border-t bg-gray-50 flex justify-end gap-2">
            <flux:button wire:click="closeCompanyModal" variant="outline" size="xs">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveCompany" variant="primary" size="xs" icon="check">
                {{ $editingCompany ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>
</div>
