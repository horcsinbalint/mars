<?php

namespace App\Models;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Model to keep track of the users' print balance.
 *
 * Changes are logged in print_account_history table. See PrintAccountObserver.
 *
 * @property mixed $user_id
 * @property int $balance
 * @property int|null $last_modified_by
 * @property string|null $modified_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FreePages[] $freePages
 * @property-read int|null $free_pages_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PrintAccountFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount whereLastModifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount whereModifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintAccount whereUserId($value)
 * @mixin \Eloquent
 */
class PrintAccount extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    public static $COST;

    protected $fillable = [
        'user_id',
        'balance',
        'last_modified_by',
        'modified_at',
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'balance' => 0,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function freePages()
    {
        return $this->hasMany(FreePages::class, 'user_id', 'user_id');
    }

    /**
     * The free pages which are currently available. Sorts the free pages by their deadline.
     * @return Collection
     */
    public function getAvailableFreePagesAttribute()
    {
        return $this->freePages()->where('deadline', '>', now())->orderBy('deadline')->get();
    }

    /**
     * Returns wether the user has enough free pages to print a document.
     * A free page is enough to print either a one sided or a two sided page.
     * @param int $pages
     * @param int $copies
     * @param bool $twoSided
     * @return bool
     */
    public function hasEnoughFreePages(int $pages, int $copies, bool $twoSided)
    {
        return $this->getAvailableFreePagesAttribute()->sum('amount') >
            $this::getFreePagesNeeeded($pages, $copies, $twoSided);
    }

    /**
     * Returns wether the user has enough balance to print a document.
     * @param int $pages
     * @param int $copies
     * @param bool $twoSided
     * @return bool
     */
    public function hasEnoughBalance(int $pages, int $copies, bool $twoSided)
    {
        return $this->balance >= $this::getBalanceNeeded($pages, $twoSided, $copies);
    }

    /**
     * Returns an array with the number of one-sided and two-sided pages needed to print the given number of pages.
     * @param int $pages
     * @param bool $twoSided
     * @return array
     */
    public static function getPageTypesNeeded(int $pages, bool $twoSided)
    {
        $oneSidedPages = 0;
        $twoSidedPages = 0;
        if (!$twoSided) {
            $oneSidedPages = $pages;
        } else {
            $oneSidedPages = $pages % 2;
            $twoSidedPages = floor($pages / 2);
        }

        return [
            'one_sided' => $oneSidedPages,
            'two_sided' => $twoSidedPages,
        ];
    }

    /**
     * Returns the number of free pages needed to print with given configuration.
     * @param int $pages 
     * @param mixed $copies 
     * @param mixed $twoSided 
     * @return int|float 
     */
    public static function getFreePagesNeeeded(int $pages, $copies, $twoSided)
    {
        $pageTypesNeeded = self::getPageTypesNeeded($pages, $twoSided);

        return ($pageTypesNeeded['one_sided'] + $pageTypesNeeded['two_sided']) * $copies;
    }

    /**
     * Returns the amount of money needed to print with given configuration.
     * @param int $pages 
     * @param int $copies 
     * @param bool $twoSided 
     * @return mixed 
     * @throws BindingResolutionException 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     */
    public static function getBalanceNeeded(int $pages, int $copies, bool $twoSided)
    {
        $pageTypesNeeded = self::getPageTypesNeeded($pages, $twoSided);

        return $pageTypesNeeded['one_sided'] * config('print.one_sided_cost') * $copies +
            $pageTypesNeeded['two_sided'] * config('print.two_sided_cost') * $copies;
    }
}
