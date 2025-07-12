<?php
namespace App\Enums;

enum ResponseTypes: string {
    case SUCCESS        = 'SUCCESS';
    case ERROR          = 'ERROR';
    case VALIDATE_ERROR = 'VALIDATE_ERROR';
    case SERVER_ERROR   = 'SERVER_ERROR';
}
