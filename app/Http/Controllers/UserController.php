<?php

namespace App\Http\Controllers;

use App\Models\Classess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private const AVATAR_DIR = 'images';

    public function index(Request $request)
    {
        return $this->classList($request, Classess::STATUS_ACTIVE, 'user.index');
    }

    public function archive(Request $request)
    {
        return $this->classList($request, Classess::STATUS_ARCHIVED, 'user.archiveclass');
    }

    public function setting(Request $request)
    {
        return view('user.setting.setting', ['user' => $request->user()]);
    }

    public function changeNamePost(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|max:100']);

        $request->user()->update(['name' => trim($data['name'])]);

        return $this->success('Your name was updated.', redirectUrl: route('user.setting'));
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'newPass' => ['required', 'string', 'confirmed', 'different:current_password', Password::min(8)],
        ], [
            'current_password.current_password' => 'Your current password is not correct.',
            'newPass.different' => 'Choose a password that is different from your current one.',
        ], ['newPass' => 'new password']);

        $request->user()->update(['password' => $request->newPass]);

        return $this->success('Password changed.', redirectUrl: route('user.setting'));
    }

    public function changeProfile(Request $request): JsonResponse
    {
        // SVG is intentionally not accepted: it can carry scripts.
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = $request->user();
        $image = $request->file('profile_picture');
        $directory = public_path(self::AVATAR_DIR);
        $filename = Str::uuid().'.'.$image->extension();

        File::ensureDirectoryExists($directory);
        $image->move($directory, $filename);

        // basename() keeps a tampered value from pointing outside the folder.
        if ($user->profile) {
            File::delete($directory.DIRECTORY_SEPARATOR.basename($user->profile));
        }

        $user->update(['profile' => $filename]);

        return $this->success('Profile picture updated.', redirectUrl: url()->previous(route('user.index')));
    }

    private function classList(Request $request, string $status, string $view)
    {
        $classess = Classess::forMember($request->user())
                            ->where('status', $status)
                            ->with('owner')
                            ->latest()
                            ->get();

        return view($view, ['classess' => $classess]);
    }
}
