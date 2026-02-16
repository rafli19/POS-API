<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with('category');
            $this->applyFilters($query, $request);
            
            $perPage = $request->get('per_page', 10);
            $products = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Append full image URL
            $products->getCollection()->transform(function ($product) {
                if ($product->image) {
                    $product->image_url = Storage::disk('public')->url($product->image);
                }
                return $product;
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = $this->validateProduct($request);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $data = $request->except('image');
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('products', $imageName, 'public');
                $data['image'] = 'products/' . $imageName;
            }
            
            $product = Product::create($data);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product->load('category')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'transactionDetails'])->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Add image URL
            if ($product->image) {
                $product->image_url = Storage::disk('public')->url($product->image);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            $validator = $this->validateProduct($request, $id);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $data = $request->except('image');
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('products', $imageName, 'public');
                $data['image'] = 'products/' . $imageName;
            }
            
            $product->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->load('category')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Delete image file
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product'
            ], 500);
        }
    }

    public function lowStock(Request $request)
    {
        try {
            $products = Product::lowStock()
                ->with('category')
                ->orderBy('stock', 'asc')
                ->paginate($request->get('per_page', 10));
            
            return response()->json([
                'success' => true,
                'message' => 'Low stock products retrieved successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch low stock products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch low stock products'
            ], 500);
        }
    }

    public function statistics()
    {
        try {
            $stats = [
                'total_products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'low_stock_products' => Product::lowStock()->count(),
                'out_of_stock_products' => Product::where('stock', 0)->count()
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', 0);
            }
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
    }

    private function validateProduct(Request $request, $id = null)
    {
        $skuRule = $id 
            ? 'sometimes|required|string|unique:products,sku,' . $id 
            : 'required|string|unique:products,sku';

        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => $skuRule,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'is_active' => 'boolean'
        ];

        return Validator::make($request->all(), $rules, [
            'image.image' => 'File harus berupa gambar (jpg, jpeg, png, gif)',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ]);
    }
}