<?php

namespace Jiannius\Atom\Models;

use Illuminate\Database\Eloquent\Model;
use Jiannius\Atom\Traits\Model as AtomModel;

class Ability extends Model
{
	use AtomModel;
	
	protected $fillable = [
		'module',
		'name',
	];

	/**
	 * Check enabled for user
	 * 
	 * @return boolean
	 */
	public function isEnabledForUser($user)
	{
		return $user->can($this->module . '.' . $this->name);
	}
}
