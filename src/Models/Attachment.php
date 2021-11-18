<?php

namespace MOIREI\MediaLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use MOIREI\MediaLibrary\Api;

class Attachment extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'pending' => 'boolean',
    ];

    /**
     * Associate an eloquent model with the attachment.
     *
     * @param Model $model
     * @return this
     */
    public function attach(Model $model)
    {
        $data = [];
        $data['attachable_type'] = $model->getMorphClass();
        $data[$model->getKeyType() === 'string'
            ? 'attachable_uuid'
            : 'attachable_id'] = $model->getKey();

        $this->update($data);

        return $this;
    }

    /**
     * Purge the attachment.
     *
     * @return this
     */
    public function purge()
    {
        $path = Api::joinPaths($this->location, $this->attachment);
        Storage::disk($this->disk)->delete($path);
        $this->delete();
        return $this;
    }

    /**
     * Persist the pending attachment.
     *
     * @return void
     */
    public function persist()
    {
        $this->update(['pending' => false]);
        return $this;
    }

    /**
     * Prune the stale attachments from the system.
     *
     * @param Carbon|int $age
     * @return void
     */
    static public function pruneStale(Carbon | int $age = 1)
    {
        if (is_int($age)) {
            $age = now()->subDays($age);
        }

        $query = self::where('pending', true)
            ->where('created_at', '<=', $age)
            ->orderBy('id', 'desc');

        $query->chunk(100, function ($attachments) {
            $attachments->each->purge();
        });
    }
}
