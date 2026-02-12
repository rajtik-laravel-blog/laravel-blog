<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, Searchable;

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the human readable created at date.
     */
    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the formatted created at date.
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at->translatedFormat('j. F Y H:i:s');
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'created_at_human',
        'created_at_formatted',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }
}
