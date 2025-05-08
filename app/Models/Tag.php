<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_tag');
    }
}