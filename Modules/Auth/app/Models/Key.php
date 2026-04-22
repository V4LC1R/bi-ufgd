<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Key extends Model
{
    use HasRoles;

    protected $table = 'auth.keys';

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'key',
        'is_active',
        'last_used_at',
        'expires_at',
        'type'
    ];

    public function isReportKey()
    {
        return $this->type === 'report';
    }

    public function isRBAC()
    {
        return $this->type === 'rbac';
    }

}
