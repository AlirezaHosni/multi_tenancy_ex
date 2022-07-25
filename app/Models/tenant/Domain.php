<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Domain extends \Stancl\Tenancy\Database\Models\Domain
{
    use HasFactory;

    protected $fillable = ['domain', 'tenant_id'];
}
