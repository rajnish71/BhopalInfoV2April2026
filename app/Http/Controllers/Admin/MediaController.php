<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class MediaController extends Controller
{
    public function index() {
        $media = Media::latest('created_at')->paginate(20);
        return view('admin.media.index', compact('media'));
    }
    public function store(Request $request) {
        $request->validate([
            'image' => 'required|image|max:5120',
            'alt_text' => 'nullable|string|max:255'
        ]);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('media', $filename, 'public');
            Media::create([
                'file_path' => $path,
                'alt_text' => $request->alt_text,
                'uploaded_by' => auth()->id(),
                'created_at' => now()
            ]);
            return redirect()->back()->with('success', 'Image uploaded successfully.');
        }
        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    public function destroy(Media $medium) {
        Storage::disk('public')->delete($medium->file_path);
        $medium->delete();
        return redirect()->back()->with('success', 'Media deleted successfully.');
    }
}