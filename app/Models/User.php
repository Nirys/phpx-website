<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasTenants, FilamentUser
{
	use HasFactory;
	use Notifiable;
	use HasSnowflakes;
	use SoftDeletes;
	use HasRoles;

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected function casts(): array
	{
		return [
			'is_potential_speaker' => 'boolean',
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	public function current_group(): BelongsTo
	{
		return $this->belongsTo(Group::class, 'current_group_id');
	}

	public function groups(): BelongsToMany
	{
		return $this->belongsToMany(Group::class, 'group_memberships')
			->as('group_membership')
			->withPivot('id', 'is_subscribed')
			->withTimestamps()
			->using(GroupMembership::class);
	}

	public function meetups(): BelongsToMany
	{
		return $this->belongsToMany(Meetup::class, 'rsvps')
			->as('meetups')
			->withTimestamps()
			->using(Rsvp::class);
	}

	public function getTenants(Panel $panel): Collection
	{
		return $this->groups;
	}

	public function canAccessTenant(Model $tenant): bool
	{
		return $this->groups()->whereKey($tenant)->exists();
	}

	public function canAccessPanel(Panel $panel): bool
	{
		if (app('phpx')->isGlobalSite()) {
			$result = $this->can('view global dashboard');
			return $result;
		} else {
			return $this->can('view dashboard');
		}
	}
}
