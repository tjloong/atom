<?php

namespace Jiannius\Atom\Models;

use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Jiannius\Atom\Traits\Model as AtomModel;
use Jiannius\Atom\Traits\HasRole;
use Jiannius\Atom\Traits\HasTeam;
use Jiannius\Atom\Notifications\ActivateAccountNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use AtomModel;
    use HasRole;
    use HasTeam;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role_id' => 'integer',
    ];

    protected $with = [
        'role',
        'abilities',
    ];

    /**
     * Scope for fussy search
     * 
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q
                    ->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }
    }

    /**
     * Get is user pending invitation attribute
     * 
     * @return boolean
     */
    public function getIsPendingActivateAttribute()
    {
        return $this->password ? false : true;
    }

    /**
     * Invite user to activate account
     * 
     * @return void
     */
    public function invite()
    {
        if ($this->is_pending_activate) {
            $this->notify(new ActivateAccountNotification());
        }
    }
}
