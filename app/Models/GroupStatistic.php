<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',//связываемся с группой
        'count_of_member',
        'number_of_online',
        'middle_age',

        'relation', // массивы
        'number_of_sex',
        'age_categories',

        'distribution_of_subscribers_by_city',
        'number_of_likes_on_top_posts',
        'number_of_commentaries_on_top_posts',
    ];
}
