<?php

namespace App\Enum;

enum DetteStatus: String
{
    case Paye = 'Paye';
    case Impayee = 'Impayee';
}
