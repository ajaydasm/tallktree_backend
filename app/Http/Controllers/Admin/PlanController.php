<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response as Http;

class PlanController extends Controller
{
    public function index()
    {
        try {
            $plans = Plan::all();
    
            $plans->transform(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'total_minutes' => $plan->total_minutes,
                    'image' => url( $plan->image), 
                    'status' => $plan->status
                ];
            });
    
            return response()->json(['success' => true, 'data' => $plans], Http::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'total_minutes' => 'required|integer|min:0',
            'image' => 'required|string', 
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = null;
        if ($request->has('image')) {

            $base64Image = $request->image;
            // Get the image extension from the base64 string (e.g., 'jpeg' or 'png')
            $extension = explode('/', mime_content_type($base64Image))[1]; 
            $imageName = 'plan_' . time() . '.' . $extension;

            // Decode the base64 string and store the image
            $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));
            $imagePath = 'assets/plans/' . $imageName;

            // Save the image to the public directory
            file_put_contents(public_path($imagePath), $image);
        }

        $plan = Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'total_minutes' => $request->total_minutes,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        return response()->json($plan, 201);
    }

    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        return response()->json($plan);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'total_minutes' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|string', 
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        if ($request->has('image')) {
            $base64Image = $request->image;
            $extension = explode('/', mime_content_type($base64Image))[1];
            $imageName = 'plan_' . time() . '.' . $extension;

            $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));
            $imagePath = 'assets/plans/' . $imageName;

            // Save the new image
            file_put_contents(public_path($imagePath), $image);

            // Delete the old image if it exists
            if (File::exists(public_path($plan->image))) {
                File::delete(public_path($plan->image));
            }

            $plan->image = $imagePath;
        }

        $plan->update($request->only(['name', 'price', 'total_minutes', 'status']));

        return response()->json($plan);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        // Delete the image if it exists
        if (File::exists(public_path($plan->image))) {
            File::delete(public_path($plan->image));
        }

        $plan->delete();

        return response()->json(['message' => 'Plan deleted successfully']);
    }

    public function updateStatus(Request $request){
        try {
            $id = $request->id;
            $status = $request->status;
            $plan = Plan::find($id);
            if (!$plan) {
                return response()->json(['error' => 'Plan not found'], 404);
            }
            $plan->status = $status;
            $plan->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully'], Http::HTTP_OK);
        } catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
