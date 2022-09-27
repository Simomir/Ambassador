<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string|null $transaction_id
 * @property int $user_id
 * @property string $code
 * @property string $ambassador_email
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $address
 * @property string|null $city
 * @property string|null $country
 * @property string|null $zip
 * @property int $complete
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddress($value)
 * @method static Builder|Order whereAmbassadorEmail($value)
 * @method static Builder|Order whereCity($value)
 * @method static Builder|Order whereCode($value)
 * @method static Builder|Order whereComplete($value)
 * @method static Builder|Order whereCountry($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereEmail($value)
 * @method static Builder|Order whereFirstName($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereLastName($value)
 * @method static Builder|Order whereTransactionId($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUserId($value)
 * @method static Builder|Order whereZip($value)
 * @mixin Eloquent
 * @method static OrderFactory factory(...$parameters)
 * @property-read mixed $name
 * @property-read mixed $admin_revenue
 * @property-read Collection|OrderItem[] $orderItems
 * @property-read int|null $order_items_count
 */
class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAdminRevenueAttribute(): float
    {
        return round($this->orderItems->sum(fn(OrderItem $item) => $item->admin_revenue), 2);
    }
}
