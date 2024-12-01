<?php

namespace App\Http\Controllers\WebPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\ClubActivity; // Import the ClubActivity model
use App\Models\ClubGallery;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class EditWebContentController extends Controller
{
    public function showEditPage()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Ensure the user has the role of 'club_manager'
        if (!$user->hasRole('club_manager')) {
            abort(403, 'Unauthorized access.');
        }

        // Retrieve the club associated with the logged-in club manager
        $club = Club::with(['club_category', 'club_activity', 'club_gallery'])
            ->where('id', $user->club_id) // Assuming the `users` table has a `club_id` column
            ->firstOrFail();

        return view('webplatform.edit', compact('club'));
    }

    public function saveContent(Request $request, $clubId)
    {
        try {

            // Validate the request data
            $validatedData = $request->validate([
                'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
                'about_us_img_url' => 'nullable|file|mimes:jpeg,png,jpg|max:10240',
                'description' => 'nullable|string',
                'join_description' => 'nullable|string',
                'email' => 'nullable|email',
                'instagram_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'activities' => 'nullable|array|max:6',
                'activities.*.name' => 'required_with:activities.*|string|max:255',
                'activities.*.description' => 'required_with:activities.*|string|max:500',
                'activities.*.image' => 'nullable|file|mimes:jpeg,png,jpg|max:10240',
                'deleted_activities' => 'nullable|array',
                'deleted_activities.*' => 'integer|exists:club_activity,id',
                'gallery' => 'nullable|array|max:5',
                'gallery.*' => 'nullable|file|mimes:jpeg,png,jpg|max:10240',
                'deleted_gallery_images' => 'nullable|array',
                'deleted_gallery_images.*' => 'integer|exists:club_gallery,id',
            ]);

            $club = Club::findOrFail($clubId);

            // Handle the logo upload
            if ($request->hasFile('logo')) {
                $club->logo = $request->file('logo')->getContent();
            }

            // Handle the about us image upload
            if ($request->hasFile('about_us_img_url')) {
                $folderPath = public_path('images/about-us/' . $club->name);
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }

                if ($club->about_us_img_url && file_exists(public_path($club->about_us_img_url))) {
                    unlink(public_path($club->about_us_img_url));
                }

                $aboutUsImagePath = 'images/about-us/' . $club->name . '/about.jpg';
                $request->file('about_us_img_url')->move($folderPath, 'about.jpg');
                $club->about_us_img_url = $aboutUsImagePath;
            }

            // Save other club fields
            $club->description = $validatedData['description'] ?? null;
            $club->join_description = $validatedData['join_description'] ?? null;
            $club->email = $validatedData['email'] ?? null;
            $club->instagram_url = $validatedData['instagram_url'] ?? null;
            $club->facebook_url = $validatedData['facebook_url'] ?? null;
            $club->linkedin_url = $validatedData['linkedin_url'] ?? null;
            $club->save();

            // Handle deleted activities
            if ($request->has('deleted_activities')) {
                foreach ($validatedData['deleted_activities'] as $activityId) {
                    $activity = ClubActivity::findOrFail($activityId);
                    if ($activity->activity_img_url && file_exists(public_path($activity->activity_img_url))) {
                        unlink(public_path($activity->activity_img_url));
                    }
                    $activity->delete();
                }
            }

            // Handle activities
            if ($request->has('activities')) {
                foreach ($request->input('activities') as $key => $activityData) {
                    $activity = strpos($key, 'new-') === 0
                        ? new ClubActivity()
                        : ClubActivity::findOrFail($key);

                    $activity->club_id = $club->id;
                    $activity->name = $activityData['name'];
                    $activity->description = $activityData['description'];

                    if ($request->hasFile("activities.$key.image")) {
                        $folderPath = public_path('images/clubs-activity/' . $club->name);
                        if (!file_exists($folderPath)) {
                            mkdir($folderPath, 0777, true);
                        }

                        $imageName = 'activity' . ($activity->id ?? uniqid()) . '.jpg';
                        $imagePath = $folderPath . '/' . $imageName;

                        if ($activity->activity_img_url && file_exists(public_path($activity->activity_img_url))) {
                            unlink(public_path($activity->activity_img_url));
                        }

                        $request->file("activities.$key.image")->move($folderPath, $imageName);
                        $activity->activity_img_url = 'images/clubs-activity/' . $club->name . '/' . $imageName;
                    }

                    $activity->save();
                }
            }

            // Handle deleted gallery images
            if ($request->has('deleted_gallery_images')) {
                foreach ($validatedData['deleted_gallery_images'] as $imageId) {
                    $galleryImage = ClubGallery::findOrFail($imageId);
                    if ($galleryImage->gallery_img_url && file_exists(public_path($galleryImage->gallery_img_url))) {
                        unlink(public_path($galleryImage->gallery_img_url));
                    }
                    $galleryImage->delete();
                }
            }

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $galleryId => $file) { // Use the $galleryId from the frontend

                    // Find the existing gallery image record by its ID
                    $galleryImage = ClubGallery::find($galleryId);

                    // Ensure folder path exists
                    $folderPath = public_path('images/gallery/' . $club->name);
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true); // Create the folder if it doesn't exist
                    }

                    // If the gallery image exists, delete the old one before saving the new one
                    if ($galleryImage) {
                        if ($galleryImage->gallery_img_url && file_exists(public_path($galleryImage->gallery_img_url))) {
                            // Delete the old image from the file system
                            unlink(public_path($galleryImage->gallery_img_url));
                        }

                        // Generate a unique image name for the new file
                        $imageName = 'gallery_' . uniqid() . '.jpg';
                        $file->move($folderPath, $imageName);

                        // Update the existing gallery image record with the new file path
                        $galleryImage->gallery_img_url = 'images/gallery/' . $club->name . '/' . $imageName;
                        $galleryImage->save();
                        
                    } else {
                        // If the gallery image doesn't exist, create a new record
                        $imageName = 'gallery_' . uniqid() . '.jpg';
                        $file->move($folderPath, $imageName);

                        // Create a new gallery image record
                        $galleryImage = new ClubGallery();
                        $galleryImage->club_id = $club->id;
                        $galleryImage->gallery_img_url = 'images/gallery/' . $club->name . '/' . $imageName;
                        $galleryImage->save();
                        
                    }
                }
            } 

            return response()->json(['success' => true, 'message' => 'Content saved successfully.']);
        } catch (\Exception $e) {
            Log::error('Save Content Error: ', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to save content.'], 500);
        }
    }
}
