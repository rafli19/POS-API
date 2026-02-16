<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    // ==================== INDEX ====================
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Apply search filter
            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            }

            // Apply active filter
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            $perPage = $request->get('per_page', 10);
            $categories = $query->withCount('products')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse('Categories retrieved successfully', $categories);

        } catch (\Exception $e) {
            Log::error('Failed to fetch categories: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch categories');
        }
    }

    // ==================== STORE ====================
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = $this->validateCategory($request);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $data = $request->only('name', 'description', 'is_active');

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create($data);

            return $this->successResponse('Category created successfully', $category, 201);

        } catch (\Exception $e) {
            Log::error('Failed to create category: ' . $e->getMessage());
            return $this->errorResponse('Failed to create category: ' . $e->getMessage());
        }
    }

    // ==================== SHOW ====================
    public function show($id): JsonResponse
    {
        try {
            $category = Category::with('products')->find($id);

            if (!$category) {
                return $this->errorResponse('Category not found', null, 404);
            }

            return $this->successResponse('Category retrieved successfully', $category);

        } catch (\Exception $e) {
            Log::error('Failed to fetch category: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch category');
        }
    }

    // ==================== UPDATE ====================
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse('Category not found', null, 404);
            }

            $validator = $this->validateCategory($request, true);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $data = $request->only('name', 'description', 'is_active');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category->update($data);

            // Refresh to get updated image_url
            $category->refresh();

            return $this->successResponse('Category updated successfully', $category);

        } catch (\Exception $e) {
            Log::error('Failed to update category: ' . $e->getMessage());
            return $this->errorResponse('Failed to update category: ' . $e->getMessage());
        }
    }

    // ==================== DESTROY ====================
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse('Category not found', null, 404);
            }

            // Delete image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return $this->successResponse('Category deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete category: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete category');
        }
    }

    // ==================== VALIDATION ====================
    private function validateCategory(Request $request, bool $isUpdate = false)
    {
        $nameRule = $isUpdate 
            ? 'sometimes|required|string|max:255' 
            : 'required|string|max:255';

        return Validator::make($request->all(), [
            'name' => $nameRule,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);
    }

    // ==================== RESPONSE HELPERS ====================
    private function successResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    private function errorResponse(string $message, $errors = null, int $code = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}