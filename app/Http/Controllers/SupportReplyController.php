<?php

namespace App\Http\Controllers;

use App\Models\Support;
use App\Models\SupportReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;

class SupportReplyController extends Controller
{


    public function getReplies(Request $request)
    {



        $replies = SupportReply::where('support_id', $request->support_id)->select('support_replies.*', 'users.first_name', 'users.last_name')
            ->leftJoin('users', 'support_replies.created_by_id', '=', 'users.id')
            ->get();
        return response()->json($replies);
    }

    public function add(Request $request)
    {

        try {
            $validation_response = Validator::make($request->all(), [
                'support_id' => 'required',
            ]);
            if ($validation_response->fails()) {
                return response()->json(['error' =>  $validation_response->messages()->first()], 400);
            }
            $supportModel = Support::find($request->support_id);
            if (!$supportModel) {
                return response()->json(['error' => 'Support not found!'], 400);
            }
            if ($supportModel->state_id != Support::STATE_INPROGRESS) {
                return response()->json(['error' => 'You are not allowed to perform this action!'], 400);
            }
            $profileImage = null;
            $model = new SupportReply();
            if ($request->hasFile('attachment')) {
                $profileImage = $this->saveAttachment($request);
            }
            $modelSaveAttachment = $this->create($request, $model, $supportModel, $profileImage, $request->hasFile('attachment') ?  true : false);
            if ($modelSaveAttachment) {
                return response()->json(['success' => 'Support created successfully!'], 200);
            } else {
                return response()->json(['error' => 'Unable to save the Support!'], 400);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json(['error' => $bug], 500);
        }
    }

    private function saveAttachment(Request $request)
    {
        $image = $request->file('attachment');
        $destinationPath = 'support_module/ticket_images';
        $profileImage = date('YmdHis') . time() . "." . $image->getClientOriginalExtension();
        $image->move(public_path($destinationPath), $profileImage);
        return $profileImage;
    }

    public function create(\Illuminate\Http\Request $request, $model, $supportModel, $profileImage, $image = false)
    {
        $model->setTypeId($supportModel);
        $model->image = $image ? $profileImage : null;
        $model->created_by_id = Auth::user()->id;
        $model->fill($request->all());
        return $model->save();
    }
}
