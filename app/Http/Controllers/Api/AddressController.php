<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Get user's saved addresses
     * Supports both session and Sanctum authentication
     */
    public function index(Request $request)
    {
        // Get user from request (Sanctum) or fallback to session auth
        $user = null;
        
        try {
            $user = auth('sanctum')->user();
        } catch (\Exception $e) {
            // Sanctum not available, use session auth
        }
        
        if (!$user) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $addresses = $user->addresses()->get();

        return response()->json([
            'success' => true,
            'data' => $addresses->map(function($address) {
                return [
                    'id' => $address->id,
                    'label' => $address->label,
                    'full_name' => $address->full_name,
                    'phone_number' => $address->phone_number,
                    'address' => $address->address,
                    'city' => $address->city,
                    'province' => $address->province,
                    'postal_code' => $address->postal_code,
                    'display_text' => '[' . $address->label . '] ' . $address->address . ', ' . $address->city
                ];
            })->toArray()
        ]);
    }

    /**
     * Create a new address
     */
    public function store(Request $request)
    {
        $user = null;
        
        try {
            $user = auth('sanctum')->user();
        } catch (\Exception $e) {
            // Sanctum not available
        }
        
        if (!$user) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validated = $request->validate([
            'label' => 'required|string',
            'full_name' => 'required|string',
            'phone_number' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'subdistrict' => 'required|string',
            'address' => 'required|string',
            'postal_code' => 'required|string',
        ]);

        $address = $user->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $address
        ], 201);
    }

    /**
     * Delete an address
     */
    public function destroy(Request $request, $id)
    {
        $user = null;
        
        try {
            $user = auth('sanctum')->user();
        } catch (\Exception $e) {
            // Sanctum not available
        }
        
        if (!$user) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $address = $user->addresses()->find($id);
        
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted'
        ]);
    }
}
