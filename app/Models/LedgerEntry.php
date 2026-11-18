<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

/** Append-only: update dan delete diblokir di level model. */
class LedgerEntry extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['*'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Ledger bersifat append-only.'));
        static::deleting(fn () => throw new LogicException('Ledger bersifat append-only.'));
    }
}
