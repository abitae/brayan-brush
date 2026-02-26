<?php

namespace App\Services\Configuration;

use App\Models\Configuration\Company;

class CompanyService
{
    public function getCompanies()
    {
        return Company::orderBy('razonSocial');
    }

    public function crearCompany($data)
    {
        return Company::create($data);
    }

    public function actualizarCompany($companyId, $data)
    {
        $company = Company::findOrFail($companyId);
        $company->update($data);
        return $company;
    }

    public function eliminarCompany($companyId)
    {
        $company = Company::findOrFail($companyId);
        $company->delete();
        return true;
    }
}
