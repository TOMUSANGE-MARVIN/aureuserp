<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;

class Invitation extends Model
{
    use HasFactory;

    protected $table = 'user_invitations';

    protected $fillable = [
        'email',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
