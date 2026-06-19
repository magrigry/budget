<?php

namespace App\Http\Controllers;

use App\Data\CategoryData;
use App\Domain\Category\CategoryManager;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Category::class);

        $categories = request()->user()
            ->categories()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => CategoryData::fromModel($c));

        return Inertia::render('categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Category::class);

        return Inertia::render('categories/Create');
    }

    public function store(StoreCategoryRequest $request, CategoryManager $manager): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $validated = $request->validated();

        $manager->store(
            $request->user(),
            $validated['name'],
            $validated['color'] ?? null,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('categories.flash.created')]);

        return to_route('categories.index');
    }

    public function edit(Category $category): Response
    {
        $this->authorize('update', $category);

        return Inertia::render('categories/Edit', [
            'category' => CategoryData::fromModel($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category, CategoryManager $manager): RedirectResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validated();

        $manager->update(
            $category,
            $validated['name'],
            $validated['color'] ?? null,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('categories.flash.updated')]);

        return to_route('categories.index');
    }

    public function destroy(Category $category, CategoryManager $manager): RedirectResponse
    {
        $this->authorize('delete', $category);

        $manager->delete($category);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('categories.flash.deleted')]);

        return to_route('categories.index');
    }
}
