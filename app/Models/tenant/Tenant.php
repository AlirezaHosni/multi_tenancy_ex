<?php

namespace App\Models\tenant;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use function bcrypt;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function booted()
    {
        Static::creating(function ($tenant){
            $tenant->password = bcrypt($tenant->password);
        });
    }
}
