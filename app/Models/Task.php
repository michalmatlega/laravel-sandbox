<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'long_description'
    ];

    //protected $guarded = ['password'];    // The attributes that aren't mass assignable

    public function toggleComplete(): void {
        $this->completed = !$this->completed;
        $this->save();
    }
}
