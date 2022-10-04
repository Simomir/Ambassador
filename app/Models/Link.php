<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @method static Builder|Link newModelQuery()
 * @method static Builder|Link newQuery()
 * @method static Builder|Link query()
 * @method static Builder|Link whereCode($value)
 * @method static Builder|Link whereId($value)
 * @method static Builder|Link whereUserId($value)
 * @mixin Eloquent
 * @method static LinkFactory factory(...$parameters)
 * @property-read User $user
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 */
class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, LinkProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'code', 'code')->where('complete', 1);
    }
}
