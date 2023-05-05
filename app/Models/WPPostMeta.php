<?php

namespace App\Models;

class WPPostMeta extends Model
{
    protected $table = 'wp_postmeta';
    protected $fillable = [
    	'meta_id',
    	'post_id',
    	'meta_key',
    	'meta_value'
    ];

    public function setUpdatedAt($value) {}
}
