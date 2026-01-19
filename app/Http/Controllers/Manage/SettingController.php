<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class SettingController extends Controller
{
    //

    public function index(Competition $competition)
    {
        $competition->makeVisible('token');
        
        return Inertia::render('Manage/Settings/Index', [
            'competition' => fn () => $competition,

            'draw' => fn () => [
                'background' => $competition->getDrawBackgroundUrlAttribute(),
                'cover' => $competition->getDrawCoverUrlAttribute(),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Competition $competition
     * @return RedirectResponse
     */
    public function updateLogo(Request $request, Competition $competition)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png'],
        ]);

        try {
            $competition->addMedia($request->file('logo'))
                ->usingFileName(uniqid('logo-') . '.png')
                ->toMediaCollection('logo');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }

    public function updateDrawBackground(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $competition->addMedia($request->file('file'))
                ->usingFileName(uniqid('draw-background-') . '.png')
                ->toMediaCollection('draw-background');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }
    public function updateCertificate(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $competition->addMedia($request->file('file'))
                ->usingFileName(uniqid('certificate') . '.png')
                ->toMediaCollection('certificate');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }
    }
    public function updateDrawCover(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $competition->addMedia($request->file('file'))
                ->usingFileName(uniqid('draw-cover-') . '.png')
                ->toMediaCollection('draw-cover');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }

    public function removeDevice(Request $request, Competition $competition, string $uuid)
    {
        $competition->tokens()->where('name', $uuid)->delete();

        return redirect()->back();
    }
}
