<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterTenantRequest;
use App\Models\tenant\Tenant;

class RegisteredTenantController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterTenantRequest $request)
    {
        $tenant = Tenant::create($request->validated());
        $tenant->createDomain(['domain' => $request->domain]);

        return redirect(tenant_route($tenant->domains->first()->domain, 'login'));
    }
}
