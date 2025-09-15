<?
namespace App\Modules\Connection\Constants;

enum TableType: string
{
    case DIMENSION = 'dimension';
    case SUB_DIMENSION = 'sub-dimension';
    case FACT = 'fact';
}