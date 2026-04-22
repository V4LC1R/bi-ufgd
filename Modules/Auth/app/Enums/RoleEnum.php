<?php

namespace Modules\Auth\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case ANALYST = 'analyst';
    case VIEWER = 'viewer';
}