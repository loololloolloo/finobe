<?php
namespace App\Blog;
use Illuminate\Database\Eloquent\Model;
final class Tag extends Model {
    protected $connection='finobe';
    protected $table='blog_tags';
    protected $guarded=['id'];
}
