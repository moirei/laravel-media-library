<?php

namespace MOIREI\MediaLibrary\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use MOIREI\MediaLibrary\Traits\UsesUuid;
use MOIREI\MediaLibrary\Traits\ArrayCastsMustNotReturnNull;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

class SharedContent extends Model implements AuthorizableContract, AuthenticatableContract
{
  use UsesUuid,
    SoftDeletes,
    ArrayCastsMustNotReturnNull,
    Authenticatable,
    Authorizable;

  const ACCESS_TYPE_SECRET = 'secret';
  const ACCESS_TYPE_TOKEN = 'token';

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'shared_contents';

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [
    'created_at',
    'updated_at',
    'downloads',
    'upload_size',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'expires_at' => 'datetime',
    'access_emails' => 'array',
    'access_keys' => 'array',
    'allowed_models' => 'array',
    'denied_models' => 'array',
    'allowed_upload_types' => 'array',
    'public' => 'boolean',
    'can_remove' => 'boolean',
    'can_upload' => 'boolean',
    'meta' => AsCollection::class,
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'access_emails',
    'access_keys',
  ];

  /**
   * Make a new shareable from file or folder
   *
   * @param Folder|File $item
   * @return SharedContent
   */
  public static function make(Folder | File $item): SharedContent
  {
    $shareable = new self();
    $shareable->from($item);

    return $shareable;
  }

  /**
   * Set shared item
   *
   * @param Folder|File $item
   * @return SharedContent
   */
  public function from(Folder | File $item): SharedContent
  {
    $this->attributes['shareable_type'] = $item->getMorphClass();
    $this->attributes[$item->getKeyType() === 'string'
      ? 'shareable_uuid'
      : 'shareable_id'] = $item->getKey();
    $this->name = $item->name;

    return $this;
  }

  /**
   * Get the shareable url
   *
   * @return string
   */
  public function url(): string
  {
    $route_name = config('media-library.route.name', '');
    return route($route_name . 'share', ['shared' => $this->id]);
  }

  /**
   * Hash an access key
   *
   * @param string $key
   * @return string
   */
  public static function hashKey(string $key): string
  {
    return Hash::make($key);
  }

  /**
   * Get the parent shareable model (folder or file).
   */
  public function shareable()
  {
    return $this->morphTo(
      __FUNCTION__,
      'shareable_type',
      is_null($this->shareable_uuid) ? 'shareable_id' : 'shareable_uuid'
    );
  }

  /**
   * Shareable url.
   *
   * @return string
   */
  public function getUrlAttribute()
  {
    return $this->url();
  }

  /**
   * Check if shareable is expired.
   *
   * @return bool
   */
  public function getExpiredAttribute()
  {
    return $this->expires_at ? $this->expires_at->isPast() : false;
  }

  /**
   * Set an access control to the shareable
   * Overrides existing keys
   *
   * @param string|array access type or list of access codes/secrets
   * @param array|null access type or list of access codes/secrets
   * @return this
   */
  public function access()
  {
    $args = func_get_args();
    $arg1 = $args[0];
    $arg2 = isset($args[1]) ? $args[1] : null;
    if (is_array($arg1)) {
      $this->access_keys = $arg1;
    } elseif ($arg2) {
      $this->access_type = $arg1;
      $this->access_keys = is_array($arg2) ? $arg2 : [$arg2];
    } elseif (is_string($arg1)) {
      $this->access_keys = [$arg1];
    } else {
      throw new Exception("Invalid access configuration");
    }

    $this->public = false;

    return $this;
  }

  /**
   * Set an access code type
   *
   * @param string $type
   * @return this
   */
  public function accessType(string $type)
  {
    $this->access_keys = $type;

    return $this;
  }

  /**
   * Set privacy
   *
   * @param bool $public
   * @return this
   */
  public function public(bool $public = true)
  {
    $this->public = $public;

    return $this;
  }

  /**
   * Set the acess email(s)
   * Does not override
   *
   * @param array|string $value
   * @return this
   */
  public function email()
  {
    $access_emails = $this->access_emails ?? [];
    $this->access_emails = array_merge(
      $access_emails,
      is_array(func_get_arg(0)) ?  func_get_arg(0) : func_get_args()
    );

    return $this;
  }

  /**
   * Set the uploadable control
   *
   * @param bool $value
   * @return this
   */
  public function canUpload($value = true)
  {
    $this->can_upload = $value;

    return $this;
  }

  /**
   * Set the removable control
   *
   * @param bool $value
   * @return this
   */
  public function canRemove(bool $value = true)
  {
    $this->can_remove = $value;

    return $this;
  }

  /**
   * Limit the number of downloads
   *
   * @param bool $value
   * @return this
   */
  public function downloads(int $value)
  {
    $this->can_download = true;
    $this->max_downloads = $value;

    return $this;
  }

  /**
   * Limit the total uploadable file size.
   *
   * @param bool $value
   * @return this
   */
  public function uploadSize(int $value)
  {
    $this->max_upload_size = $value;
    $this->can_upload = true;

    return $this;
  }

  /**
   * Restrict the upload file types
   * Accepts array or an infinit string params
   *
   * @param array|string $value
   * @return this
   */
  public function uploadTypes()
  {
    $this->allowed_upload_types = is_array(func_get_arg(0)) ?  func_get_arg(0) : func_get_args();
    $this->can_upload = true;

    return $this;
  }

  /**
   * Models that are allowed to access this shareable
   *
   * @return self
   */
  public function allow()
  {
    $allowed = is_array(func_get_arg(0)) ?  func_get_arg(0) : func_get_args();
    $models = array();
    foreach ($allowed as $allowed_model) {
      if ($allowed_model instanceof Model) {
        array_push($models, [
          'shareable_type' => $allowed_model->getMorphClass(),
          'shareable_id' => $allowed_model->getKey(),
        ]);
      }
    }
    $this->allowed_models = $models;

    return $this;
  }

  /**
   * Models that are NOT allowed to access this shareable
   *
   * @param array $denied
   * @return self
   */
  public function deny(array $denied)
  {
    $models = array();
    foreach ($denied as $denied_model) {
      if ($denied_model instanceof Model) {
        array_push($models, [
          'shareable_type' => $denied_model->getMorphClass(),
          'shareable_id' => $denied_model->getKey(),
        ]);
      }
    }
    $this->denied_models = $models;

    return $this;
  }

  /**
   * Get the allows models that may access this shareable
   *
   * @return array
   */
  public function getAllowedModels()
  {
    $models = [];
    foreach ($this->allowed_models as $model) {
      $class = '\\' . $model['shareable_type'];
      array_push(
        $models,
        $class::find($model['shareable_id'])
      );
    }
    return $models;
  }

  /**
   * Get the allows models that may access this shareable
   *
   * @return array
   */
  public function getDeniedModels()
  {
    $models = [];
    foreach ($this->denied_models as $model) {
      $class = '\\' . $model['shareable_type'];
      array_push(
        $models,
        $class::find($model['shareable_id'])
      );
    }
    return $models;
  }

  /**
   * Check if model is in allowed.
   *
   * @return bool
   */
  public function canAccess(Model $model): bool
  {
    if ($this->allowed_models) {
      foreach ($this->allowed_models as $allowed_model) {
        if (
          $allowed_model['shareable_type'] === $model->getMorphClass() &&
          $allowed_model['shareable_id'] === $model->getKey()
        ) return true;
      }
      return false;
    }
    if ($this->denied_models) {
      foreach ($this->denied_models as $denied_model) {
        if (
          $denied_model['shareable_type'] === $model->getMorphClass() &&
          $denied_model['shareable_id'] === $model->getKey()
        ) return false;
      }
    }
    return true;
  }

  /**
   * Prune the stale (expired) shared content from the system.
   *
   * @return void
   */
  public function pruneStale()
  {
    $query = self::where('expires_at', '<=', now());
    $query->chunk(100, function ($shared) {
      $shared->each->forceDelete();
    });
  }
}
