<?php

namespace App\Http\Controllers;

use App\Models\Music;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MusicController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->get('per_page', 20);
        $music = Music::where('is_active', true)->orderBy('is_featured', 'desc')->paginate($perPage);

        // Transform data into public format

        $music = $music->map(function ($m) {
            return $this->transformMusic($m);
        });

        return response()->json($music);
    }

    public function show(Music $music)
    {
        if (!$music->is_active) {
            return response()->json('Music not found', 401);
        }

        return response()->json($this->transformMusic($music));
    }

    private function transformMusic($music)
    {
        return [
            'id' => $music->id,
            'title' => $music->title,
            'artist' => $music->artist,
            'album' => $music->album,
            'cover_url' => $music->cover_url,
            'duration' => $music->duration,
            'source' => $music->source,
            'is_featured' => $music->is_featured,
            'created_at' => $music->created_at,
            'updated_at' => $music->updated_at,
            'genre' => $music->genre,
        ];
    }

    public function generateMusic()
    {
        $musicDir = storage_path('app/public/music');

        if (!file_exists($musicDir)) {
            return response()->json('Music directory does not exist', 404);
        }

        $files = File::files($musicDir);
        $getID3 = new \getID3;

        $added = [];
        $updated = [];
        $removed = [];

        Music::where('file_url', 'LIKE', '%music%')
            ->get()
            ->each(function ($music) {
                $music->update([
                    'file_url' => str_replace('music', 'storage/music', $music->file_url)
                ]);
            });

        foreach ($files as $file) {

            $filename = $file->getFilename();
            $realPath = $file->getPath();

            $title = Str::title(
                trim(preg_replace('/\s+/', '', str_replace(' ', '-', pathinfo($filename, PATHINFO_FILENAME))))
            );

            // Extract audio metadata
            $fileInfo = $getID3->analyze($realPath);
            \getid3_lib::CopyTagsToComments($fileInfo);

            $artist = $fileInfo['comments_html']['artist'][0] ?? null;
            $album = $fileInfo['comments_html']['album'][0] ?? null;
            $genre = $fileInfo['comments_html']['genre'][0] ?? null;
            $duration = $fileInfo['comments_html']['duration'][0] ?? null;

            $cover_path = null;

            // Try extracting embedded cover image art from MP3 file
            if (!empty($fileInfo['comments']['picture'][0]['data'])) {
                $imageData = $fileInfo['comments']['picture'][0]['data'];
                $imageExist = $fileInfo['comments']['picture'][0]['image_mime'] === 'image/jpeg' ? 'jpg' : 'png';

                $coverName = pathinfo($filename, PATHINFO_FILENAME) . '.' . $imageExist;
                $coverFullPath = storage_path('app/public' . $cover_path);
                File::ensureDirectoryExists(dirname($coverFullPath));
                File::put($coverFullPath, base64_decode($imageData));
            }

            // Always use storage/ prefix for consistency
            $storedFilePath = 'storage/music/' . $filename;


            // Upload existing file entry or create a new one
            $music = Music::where('file_url', $storedFilePath)->first();
            if (!$music) {
                $music = new Music();
                $music->file_url = $storedFilePath;
                $music->title = $title;
                $music->artist = $artist;
                $music->album = $album;
                $music->source = 'local';
                $music->genre = $genre;
                $music->duration = $duration;
                $music->cover_url = $cover_path;
                $music->is_active = true;
                $music->is_featured = false;
                $music->save();
                $added[] = $music->title;
            } else {
                $music->update([
                    'title' => $title,
                    'artist' => $artist,
                    'album' => $album,
                    'genre' => $genre,
                    'duration' => $duration,
                    'cover_url' => $cover_path,
                    'file_url' => $storedFilePath,
                    'source' => 'local',
                    'is_active' => true,
                    'is_featured' => false
                ]);
                $updated[] = $music->title;
            }
            $processedFiles[] = $storedFilePath;
        }

        $existingFiles = collect($files)->map(fn($f) => '/storage/music/' . $f->getFilename());
        $orphans = Music::whereNotIn('file_url', $existingFiles)->get();

        foreach ($orphans as $music) {
            $music->delete();
            $removed[] = $music->file_url;
        }

        return response()->json([
            'message' => 'Sync complete',
            'count_added' => count($added),
            'count_updated' => count($updated),
            'count_removed' => count($removed),
            'added' => $added,
            'updated' => $updated,
            'removed' => $removed
        ]);
    }

    public function fetchCoverImage($artist, $album, $title, $filename)
    {
        $query = urldecode($artist ? $album : $title);
        $cover_path = null;

        try {
            $response = Http::get("https://itunes.apple.com/search?term={$query}&entity=album&limit=1");
            if ($response->ok() && isset($response['results'][0]['artworkUrl100'])) {
                $image_url = str_replace('100x100', '600x600', $response['results'][0]['artworkUrl100']);
                $cover_path = $this->saveCoverFromUrl($image_url, $filename);
            }

            if (!$cover_path) {
                $placeholder = 'https://ui-avatars.com/api/?name=' . urlencode($title) . "background=random&size=512";
                $cover_path = $this->saveCoverFromUrl($placeholder, $filename);
            }
            return $cover_path ? 'covers/' . basename($cover_path) : null;

        } catch (\Exception $e) {
            Log::error("Cover fetch failed for $filename: " . $e->getMessage());
            return null;
        }

    }

    protected function saveCoverFromUrl($url, $filename)
    {
        $repsonse = Http::get($url);

        if ($repsonse->ok()) {
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: "jpg";
            $cover_name = pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
            $cover_full_path = storage_path('app/public/covers' . $cover_name);

            File::ensureDirectoryExists(dirname($cover_full_path));
            File::put($cover_full_path, $repsonse->body());
            return $cover_full_path;
        }

        return null;
    }
}
