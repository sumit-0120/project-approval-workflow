<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Approval;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\ProjectStatusMail;


class ProjectController extends Controller
{

    public function projectList(Request $request)
    {
        if(auth()->user()->role->name == 'admin'){
            $query = Project::with('user');
        }else{
            $query = Project::with('user')->where('user_id', auth()->id());
        }

        // Status Filter
        if($request->status){
            $query->where('status',$request->status);
        }

        // Date Filter
        if($request->date){
            $query->whereDate('created_at',$request->date);
        }

        // User Filter (admin only)
        if(auth()->user()->role->name == 'admin'){
            $query->whereHas('user',function($q) use ($request){
                $q->where('name','like','%'.$request->user.'%');
            });
        }

        $projects = $query->latest()->get();

        return view('projects.index',compact('projects'));
    }

    public function createProject()
    {
        return view('projects.create');
    }

    public function storeProject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'file'        => 'nullable|file|mimes:pdf,doc,docx,zip|max:2048',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $filePath = null;
            if ($request->hasFile('file')) {
                $uploadPath = 'uploads/projects';
                $file       = $request->file('file');
                $fileName   = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath   = $uploadPath . '/' . $fileName;
                // Folder create if not exists
                if (!file_exists(public_path($uploadPath))) {
                    mkdir(public_path($uploadPath), 0755, true);
                }
                $file->move(public_path($uploadPath), $fileName);
            }
             $project =  Project::create([
                'title'       => $request->title,
                'description' => $request->description,
                'file'        => $filePath,
                'user_id'     => Auth::id(),
                'status'      => 
                'pending'
            ]);

            Mail::to(Auth::user()->email)
            ->queue(new ProjectStatusMail($project,'submitted'));

            return redirect()->route('projects.list')->with('success', 'Project Submitted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

   
    public function approve($id)
    {
        // stored procedure call
        DB::statement('CALL sp_approve_project(?, ?)',[
            $id,
            auth()->id()
        ]);

        // project data get
        $project = Project::with('user')->findOrFail($id);

        // email send
        Mail::to($project->user->email)
            ->queue(new ProjectStatusMail($project,'approved'));

        return redirect()->back()->with('success','Project Approved');
    }

    public function reject(Request $request,$id)
    {
        // stored procedure call
        DB::statement('CALL sp_reject_project(?,?,?)',[
            $id,
            auth()->id(),
            $request->reason
        ]);

        // project data get
        $project = Project::with('user')->findOrFail($id);

        // send email with reason
        Mail::to($project->user->email)
            ->queue(new ProjectStatusMail($project,'rejected',$request->reason));

        return redirect()->back()->with('success','Project Rejected');
    }


    public function history($id)
    {
        $project = Project::with('auditLog')->findOrFail($id);

        return view('projects.view',compact('project'));
    }

    // public function bulkAction(Request $request)
    // {
    //     $request->validate([
    //         'projects' => 'required|array',
    //         'action'   => 'required|in:approve,reject',
    //         'reason'   => 'required_if:action,reject|string|max:500'
    //     ]);

    //     dd($request->all());

    //     try {

    //         DB::beginTransaction();

    //         foreach ($request->projects as $id) {

    //             $project = Project::findOrFail($id);

    //             if ($request->action == 'approve') {

    //                 $project->update([
    //                     'status' => 'approved'
    //                 ]);

    //                 Approval::create([
    //                     'project_id' => $project->id,
    //                     'admin_id'   => auth()->id(),
    //                     'status'     => 'approved'
    //                 ]);

    //                 AuditLog::create([
    //                     'user_id'    => auth()->id(),
    //                     'project_id' => $project->id,
    //                     'action'     => 'approved'
    //                 ]);

    //             }

    //             if ($request->action == 'reject') {

    //                 $project->update([
    //                     'status' => 'rejected'
    //                 ]);

    //                 Approval::create([
    //                     'project_id' => $project->id,
    //                     'admin_id'   => auth()->id(),
    //                     'status'     => 'rejected',
    //                     'reason'     => $request->reason
    //                 ]);

    //                 AuditLog::create([
    //                     'user_id'    => auth()->id(),
    //                     'project_id' => $project->id,
    //                     'action'     => 'rejected'
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         return back()->with('success','Bulk action completed successfully');

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return back()->with('error',$e->getMessage());
    //     }
    // }

    public function bulkAction(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'projects' => 'required|array',
            'action'   => 'required|in:approve,reject',
            'reason'   => 'required_if:action,reject|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            DB::beginTransaction();

            foreach ($request->projects as $id) {

                $project = Project::with('user')->findOrFail($id);

                if ($request->action == 'approve') {

                    $project->update(['status' => 'approved']);

                    Approval::create([
                        'project_id' => $project->id,
                        'admin_id'   => auth()->id(),
                        'status'     => 'approved'
                    ]);

                    AuditLog::create([
                        'user_id'    => auth()->id(),
                        'project_id' => $project->id,
                        'action'     => 'approved'
                    ]);

                    // EMAIL SEND
                    Mail::to($project->user->email)
                        ->queue(new ProjectStatusMail($project,'approved'));
                }

                if ($request->action == 'reject') {

                    $project->update(['status' => 'rejected']);

                    Approval::create([
                        'project_id' => $project->id,
                        'admin_id'   => auth()->id(),
                        'status'     => 'rejected',
                        'reason'     => $request->reason
                    ]);

                    AuditLog::create([
                        'user_id'    => auth()->id(),
                        'project_id' => $project->id,
                        'action'     => 'rejected'
                    ]);

                    // EMAIL SEND
                    Mail::to($project->user->email)
                        ->queue(new ProjectStatusMail($project,'rejected',$request->reason));
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Bulk action completed successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ],500);
        }
    }


}


    
    /*DELIMITER $$

    CREATE PROCEDURE sp_approve_project(IN pid INT, IN uid INT)
    BEGIN

    UPDATE projects
    SET status = 'approved'
    WHERE id = pid;

    INSERT INTO approvals(project_id, admin_id, status, created_at, updated_at)
    VALUES (pid, uid, 'approved', NOW(), NOW());

    INSERT INTO audit_logs(user_id, project_id, action, created_at, updated_at)
    VALUES (uid, pid, 'approved', NOW(), NOW());

    END $$

    DELIMITER ; */


   /* for reject  */

   
   
    /* DELIMITER $$

    CREATE PROCEDURE sp_reject_project(
        IN pid INT,
        IN uid INT,
        IN reason TEXT
    )
    BEGIN

    UPDATE projects
    SET status = 'rejected',
        updated_at = NOW()
    WHERE id = pid;

    INSERT INTO approvals(project_id, admin_id, status, reason, created_at, updated_at)
    VALUES (pid, uid, 'rejected', reason, NOW(), NOW());

    INSERT INTO audit_logs(user_id, project_id, action, created_at, updated_at)
    VALUES (uid, pid, 'rejected', NOW(), NOW());

    END $$

    DELIMITER ; */