<?php
namespace App\Blog;
use Illuminate\Database\Eloquent\Model;
final class Author extends Model {
    protected $connection='finobe';
    protected $table='blog_authors';
    protected $guarded=['id'];
}
