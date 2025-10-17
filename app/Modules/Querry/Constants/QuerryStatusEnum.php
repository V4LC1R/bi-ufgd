<?php
namespace App\Modules\Querry\Constants;

enum QuerryStatusEnum: string
{
    case PENDING = 'pending'; //esperando o building

    case BUILD = 'build'; // esperando a execucao

    case SUCCESS = 'success'; // Executado
    case FAIL = 'fail'; // falha
    case INVALID = 'invalid'; // invalido por edicao
}