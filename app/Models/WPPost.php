<?php

namespace App\Models;

class WPPost extends Model
{
    protected $table = 'wp_posts';
    protected $fillable = [
    	'post_author',
    	'post_date',
    	'post_date_gmt',
    	'post_content', 
    	'post_title', 
    	'post_excerpt', 
    	'post_status',
    	'comment_status',
    	'ping_status',
    	'post_password',  
    	'post_name',  
    	'to_ping', 
    	'pinged', 
    	'post_modified',
    	'post_modified_gmt',
    	'post_content_filtered', 
    	'post_parent',
    	'guid',  
    	'menu_order',
    	'post_type',
    	'post_mime_type',
    	'comment_count'
    ];

    public function setUpdatedAt($value) {}
}
