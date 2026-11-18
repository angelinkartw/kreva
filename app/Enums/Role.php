<?php

namespace App\Enums;

enum Role: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';
    case Admin = 'admin';
    case Support = 'support';
}
