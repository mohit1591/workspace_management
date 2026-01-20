<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Item::query()->with(['assignedUser:id,name,email', 'workspace:id,name']);

        // Apply member-specific filtering
        if ($user->isMember()) {
            $query->where('assigned_user_id', $user->id);
        }

        $perPage = $request->input('per_page', 10);
        $items = $query->latest()->paginate($perPage);

        return response()->json($items);
    }

    public function store(StoreItemRequest $request)
    {
        $user = $request->user();
        
        $data = $request->validated();
        
        // Set workspace_id based on user role
        if ($user->isSystemAdmin() && $request->has('workspace_id')) {
            $data['workspace_id'] = $request->input('workspace_id');
        } else {
            $data['workspace_id'] = $user->workspace_id;
        }

        $item = Item::create($data);
        $item->load(['assignedUser:id,name,email', 'workspace:id,name']);

        return response()->json([
            'message' => 'Item created successfully',
            'item' => $item
        ], 201);
    }

    public function show(Item $item)
    {

        $item->load(['assignedUser:id,name,email', 'workspace:id,name', 'attachments']);

        return response()->json($item);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());
        $item->load(['assignedUser:id,name,email', 'workspace:id,name']);

        return response()->json([
            'message' => 'Item updated successfully',
            'item' => $item
        ]);
    }

    public function destroy(Item $item)
    {

        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully'
        ]);
    }
}