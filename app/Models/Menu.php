<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class Menu extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['permissions'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function getPermissionsAttribute()
    {
        return Permission::where('name','like',$this->slug . '.%')
            ->get(['id', 'name']);
    }

}
