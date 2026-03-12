<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\ProjectStatusMail;


class ProjectApiController extends Controller
{

    public function storeProject(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'file'        => 'required|file|mimes:pdf,doc,docx,zip|max:2048',
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Validation Error',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $filePath = null;

            if ($request->hasFile('file')) {

                $uploadPath = public_path('uploads/projects');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file = $request->file('file');

                $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                $file->move($uploadPath, $fileName);

                $filePath = 'uploads/projects/'.$fileName;
            }

            $project = Project::create([
                'title'       => $request->title,
                'description' => $request->description,
                'file'        => $filePath,
                'user_id'     => Auth::id(),
                'status'      => 'pending'
            ]);

            Mail::to($project->user->email)
            ->queue(new ProjectStatusMail($project,'approved'));

            return response()->json([
                'status'=>true,
                'message'=>'Project submitted successfully',
                'data'=>new ProjectResource($project)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function approveProject($id)
    {

        $user = auth()->user();

        // admin check
        if($user->role->name != 'admin'){
            return response()->json([
                'status'=>false,
                'message'=>'Unauthorized'
            ],403);
        }

        try{

            // stored procedure call
            DB::statement('CALL sp_approve_project(?, ?)',[
                $id,
                auth()->id()
            ]);

            // project get
            $project = Project::with('user')->findOrFail($id);

            // email send
            Mail::to($project->user->email)
                ->queue(new ProjectStatusMail($project,'approved'));

            return response()->json([
                'status'=>true,
                'message'=>'Project approved successfully'
            ]);

        }catch(\Exception $e){

            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage()
            ],500);

        }

    }

    
}
