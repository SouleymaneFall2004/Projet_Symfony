<?php

namespace App\Enum;

enum EtatDette: string
{
    case ENCOURS = 'ENCOURS';
    case VALIDER = 'VALIDER';
    case ANNULER = 'ANNULER';
}
