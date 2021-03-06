<?php

namespace App\Nrna\Models;

use Illuminate\Database\Eloquent\Model;

class RssCategory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rss_categories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'image', 'status'];

    /**
     * Get all feeds of the categories.
     */
    public function feeds()
    {
        return $this->hasManyThrough(RssNewsFeeds::class, Rss::class, 'category_id')->orderBy('post_date','desc');
    }

    public function getImageUrlAttribute()
    {
        return asset('uploads/'.$this->image);
    }

    public function getApiItemsAttribute()
    {
        return ['id' => $this->id, 'title' => $this->title, 'image_url' => $this->image_url];
    }
}
