<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Company;
use App\Services\Configuration\CompanyService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CompanyLive extends Component
{
    use WithFileUploads;

    protected CompanyService $companyService;

    public $showCompanyModal = false;
    public $showDeleteModal = false;
    public $editingCompany = null;
    public $companyToDelete = null;

    // Campos del formulario
    public $ruc = '';
    public $razonSocial = '';
    public $address = '';
    public $email = '';
    public $telephone = '';
    public $ubigeo = '';
    public $ctaBanco = '';
    public $pin = '';
    public $nroMtc = '';
    public $logo_path = null; // Cambiado a null para archivos
    public $sol_user = '';
    public $sol_pass = '';
    public $cert_path = null; // Cambiado a null para archivos
    public $client_id = '';
    public $client_secret = '';
    public $production = false;

    protected $rules = [
        'ruc' => 'required|string|max:20',
        'razonSocial' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'telephone' => 'nullable|string|max:20',
        'ubigeo' => 'nullable|string|max:20',
        'ctaBanco' => 'nullable|string|max:50',
        'pin' => 'nullable|string|max:20',
        'nroMtc' => 'nullable|string|max:20',
        'logo_path' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg', // Validación para imágenes
        'sol_user' => 'nullable|string|max:50',
        'sol_pass' => 'nullable|string|max:50',
        'cert_path' => 'nullable|file|max:5120|mimes:p12,pem,cer,crt,key,txt', // Validación para certificados
        'client_id' => 'nullable|string|max:100',
        'client_secret' => 'nullable|string|max:100',
        'production' => 'boolean',
    ];

    protected $messages = [
        'logo_path.image' => 'El archivo debe ser una imagen válida.',
        'logo_path.max' => 'La imagen no debe superar los 2MB.',
        'logo_path.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o svg.',
        'cert_path.file' => 'El archivo debe ser un certificado válido.',
        'cert_path.max' => 'El certificado no debe superar los 5MB.',
        'cert_path.mimes' => 'El certificado debe ser de tipo: p12, pem, cer, crt, key o txt.',
    ];

    public function boot(CompanyService $companyService): void
    {
        $this->companyService = $companyService;
    }

    public function render()
    {
        $company = $this->companyService->getCompanies()->first();
        return view('livewire.configuration.company-live', [
            'company' => $company,
        ]);
    }

    public function openCompanyModal($companyId = null): void
    {
        if ($companyId) {
            $this->editingCompany = Company::find($companyId);
            foreach ($this->editingCompany->getAttributes() as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            $this->resetCompanyForm();
        }
        $this->showCompanyModal = true;
    }

    public function saveCompany(): void
    {
        $data = $this->validate($this->rules);

        try {
            // Manejar la subida de la imagen del logo
            if ($this->logo_path && $this->logo_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Eliminar imagen anterior si existe
                if ($this->editingCompany && $this->editingCompany->logo_path) {
                    Storage::disk('public')->delete($this->editingCompany->logo_path);
                }

                // Guardar nueva imagen
                $logoPath = $this->logo_path->store('logos', 'public');
                $data['logo_path'] = $logoPath;
            } else {
                // Si no hay nueva imagen, mantener la existente
                unset($data['logo_path']);
            }

            // Manejar la subida del certificado
            if ($this->cert_path && $this->cert_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Eliminar certificado anterior si existe
                if ($this->editingCompany && $this->editingCompany->cert_path) {
                    Storage::disk('public')->delete($this->editingCompany->cert_path);
                }

                // Guardar nuevo certificado
                $certPath = $this->cert_path->store('certs', 'public');
                $data['cert_path'] = $certPath;
            } else {
                // Si no hay nuevo certificado, mantener el existente
                unset($data['cert_path']);
            }

            if ($this->editingCompany) {
                $this->companyService->actualizarCompany($this->editingCompany->id, $data);
            } else {
                $this->companyService->crearCompany($data);
            }

            $this->closeCompanyModal();
            session()->flash('message', 'Compañía guardada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', 'Error: ' . $e->getMessage());
        }
    }

    public function closeCompanyModal(): void
    {
        $this->showCompanyModal = false;
        $this->resetCompanyForm();
        $this->editingCompany = null;
        // Limpiar imagen temporal si existe
        if ($this->logo_path && $this->logo_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $this->logo_path = null;
        }
        // Limpiar certificado temporal si existe
        if ($this->cert_path && $this->cert_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $this->cert_path = null;
        }
    }

    public function resetCompanyForm(): void
    {
        $this->reset([
            'ruc', 'razonSocial', 'address', 'email', 'telephone', 'ubigeo', 'ctaBanco', 'pin', 'nroMtc',
            'logo_path', 'sol_user', 'sol_pass', 'cert_path', 'client_id', 'client_secret', 'production'
        ]);
        $this->production = false;
    }

    public function confirmDelete($companyId): void
    {
        $this->companyToDelete = $companyId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->companyToDelete = null;
    }

    public function deleteCompany(): void
    {
        try {
            // Eliminar imagen del logo si existe
            $company = Company::find($this->companyToDelete);
            if ($company && $company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }

            // Eliminar certificado si existe
            if ($company && $company->cert_path) {
                Storage::disk('public')->delete($company->cert_path);
            }

            $this->companyService->eliminarCompany($this->companyToDelete);
            $this->showDeleteModal = false;
            $this->companyToDelete = null;
            session()->flash('message', 'Compañía eliminada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', 'Error: ' . $e->getMessage());
        }
    }

    public function removeLogo(): void
    {
        if ($this->editingCompany && $this->editingCompany->logo_path) {
            Storage::disk('public')->delete($this->editingCompany->logo_path);
            $this->editingCompany->update(['logo_path' => null]);
            $this->logo_path = null;
        } else {
            // Si no hay compañía editando, solo limpiar el campo temporal
            $this->logo_path = null;
        }
    }

    public function removeCert(): void
    {
        if ($this->editingCompany && $this->editingCompany->cert_path) {
            Storage::disk('public')->delete($this->editingCompany->cert_path);
            $this->editingCompany->update(['cert_path' => null]);
            $this->cert_path = null;
        } else {
            // Si no hay compañía editando, solo limpiar el campo temporal
            $this->cert_path = null;
        }
    }
}
