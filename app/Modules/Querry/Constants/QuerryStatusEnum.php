<?php
namespace App\Modules\Querry\Constants;

enum QuerryStatusEnum: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case RETRY = 'retry';
    case FAIL = 'fail';
}