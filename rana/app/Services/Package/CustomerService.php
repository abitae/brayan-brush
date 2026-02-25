<?php

namespace App\Services\Package;

use App\Models\Package\Customer;

class CustomerService
{
    public function getCustomers($search = '', $filterStatus = '')
    {
        return Customer::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                      ->orWhere('code', 'like', "%$search%")
                      ->orWhere('type_code', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
            })
            ->when($filterStatus !== '', function ($query) use ($filterStatus) {
                $query->where('isActive', $filterStatus);
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function crearCustomer($data)
    {
        return Customer::create($data);
    }

    public function actualizarCustomer($customerId, $data)
    {
        $customer = Customer::findOrFail($customerId);
        $customer->update($data);
        return $customer;
    }

    public function eliminarCustomer($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $customer->delete();
        return true;
    }
}
