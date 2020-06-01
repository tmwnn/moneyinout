<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Operation
 *
 * @property int $id
 * @property string $date Дата
 * @property int $summ Сумма
 * @property string $comment Комментарий
 * @property string $search Поисковая строка
 * @property int $type Тип
 * @property int $category_id id категории
 * @property int $user_id id пользователя
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereNameEng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Operation extends Model
{
    public $fillable = ['id', 'date', 'summ', 'comment', 'category_id', 'search', 'type', 'user_id'];

    public function category() {
        return $this->hasOne('App\Models\Category','id', 'category_id');
    }
    public function user() {
        return $this->hasOne('App\Models\User','id', 'user_id');
    }
}
