<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

abstract class Controller
{
    public function generateSlug($model, $title, $id = null)
    {
        $slug = Str::slug($title);

        $originalSlug = $slug;
        $count = 1;

        while (
            $model::withTrashed()
            ->where('slug', $slug)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', '!=', $id);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function generateCode($model, $prefix = 'ROL')
    {
        $last = $model::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
