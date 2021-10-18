<?php

namespace Jiannius\Atom\Controllers;

use App\Models\File;
use App\Http\Controllers\Controller;
use App\Requests\FileStoreRequest;

class FileController extends Controller
{
    /**
     * File list
     *
     * @return Response
     */
    public function list()
    {
        $files = File::fetch();

        if (request()->isMethod('post')) return back()->with('async', $files);

        return inertia('file/list', [
            'files' => $files,
        ]);
    }

    /**
     * Upload fild
     * 
     * @return Response
     */
    public function upload()
    {
        $files = File::upload();

        return back()->with('async', $files);
    }

    /**
     * Store file
     *
     * @param FileStoreRequest $request
     * @return Response
     */
    public function store(FileStoreRequest $request)
    {
        $request->validated();

        $file = File::findOrFail($request->id);

        $file->fill($request->input('file'))->save();

        return back()->with('toast', 'File Updated::success');
    }

    /**
     * Delete role
     *
     * @return Response
     */
    public function delete()
    {
        File::whereIn('id', explode(',', request()->id))->get()->each(fn($file) => $file->delete());

        return redirect(request()->redirect ?? route('file.list'))->with('toast', 'Files Deleted');
    }
}
