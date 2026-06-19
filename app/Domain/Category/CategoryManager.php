<?php

namespace App\Domain\Category;

use App\Models\Category;
use App\Models\User;

class CategoryManager
{
    public function store(User $user, string $name, ?string $color): Category
    {
        return Category::create([
            'user_id' => $user->id,
            'name' => $name,
            'color' => $color,
        ]);
    }

    public function update(Category $category, string $name, ?string $color): void
    {
        $category->update([
            'name' => $name,
            'color' => $color,
        ]);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
