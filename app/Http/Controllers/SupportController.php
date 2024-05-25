<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Support;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Validation\Rule;

class SupportController extends Controller
{
    public $setFilteredRecords = 0;

    public function index()
    {
        try {
         
            $model = new Support();
            return view('support.support.index', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Support::find($id);
            if ($model) {
               
                return view('support.support.update', compact('model'));
            } else {
                return redirect()->back()->with('error', 'Support not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function create(Request $request)
    {
        try {

            $model  = new Support();
            if ($model) {
               
                return view('support.support.update', compact('model'));
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Support::find($id);
            if ($model) {
            
                return view('support.support.view', compact('model'));
            } else {
                return redirect()->back()->with('error', 'Support not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function update(Request $request)
    {
        if ($this->validator($request->all())->fails()) {
            $message = $this->validator($request->all())->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }
        try {
            $model = Support::find($request->id);
            if (!$model) {
                return redirect()->back()->with('error', 'Support not found');
            }
            $all_images = null;

            if ($request->hasFile('image')) {
                $ticket_images = $request->file('image');
                foreach ($ticket_images as $image) {
                    if ($image->isValid()) {
                        $imageName = rand(1, 100000) . time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('support_module/ticket_images'), $imageName);
                        $all_images[] =  $imageName;
                    }
                }
                $all_images = json_encode($all_images);
            } else {
                $all_images = $model->image;
            }
            $model->fill($request->all());
            $model->image = $all_images;
            if ($model->save()) {
                return redirect()->back()->with('success', 'Support updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Support not updated');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public $s_no = 1;


    


    public function getSupportList(Request $request, $id = null)
    {
        if (User::isUser()) {
            $query = Support::my()->orderBy('id', 'desc');
        } else {
            $query = Support::orderBy('id', 'desc');
        }

        if (!empty($id))
            $query->where('id', $id);

        return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('title', function ($data) {
                return !empty($data->title) ? (strlen($data->title) > 60 ? substr(ucfirst($data->title), 0, 60) . '...' : ucfirst($data->title)) : 'N/A';
            })

            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })

            ->addColumn('priority_id', function ($data) {
                return $data->getPriority();
            })
           
            ->addColumn('department_id', function ($data) {
                return $data->getDepartment ?  $data->getDepartment->title : 'N/A';
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('support/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('support/view/' . $data->id) . '"  ><i class="fa fa-eye
                    "data-toggle="tooltip"  title="View"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;

                return $html;
            })
            ->rawColumns([
                'action',
                'created_at',
                'status',
                'customerClickAble'
            ])

            ->filter(function ($query) {
                if (!empty(request('search')['value'])) {
                    $searchValue = request('search')['value'];
                    $searchTerms = explode(' ', $searchValue);
                    $query->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where('id', 'like', "%$term%")
                            ->orWhere('title', 'like', "%$term%")
                            ->orWhere('created_at', 'like', "%$term%")
                            ->orWhereHas('getDepartment', function ($query) use ($term) {
                                $query->where('title', 'like', "%$term%");
                            })
                            ->orWhere(function ($query) use ($term) {
                                $query->searchState($term);
                            })
                            ->orWhere(function ($query) use ($term) {
                                $query->searchPriority($term);
                            })
                            ->orWhereHas('createdBy', function ($query) use ($term) {
                                $query->where('name', 'like', "%$term%");
                            });
                        }
                    });
                }
            })
            ->make(true);
    }











    protected static function validator(array $data, $id = null)
    {
        return Validator::make(
            $data,
            [
                'title' => 'required|string|max:255',
                'priority_id' => 'required',
                'department_id' => 'required|exists:support_departments,id',
                'message' => 'required|string|max:255'
            ],
            [
                'title.required' => 'The subject field is required.',
                'title.max' => 'The subject field must not exceed 255 characters.',
                'department_id.required' => 'The department field is required.',
                'priority_id.required' => 'The priority field is required.',
                'message.required' => 'The message field is required.',
                'message.max' => 'The message field must not exceed 255 characters.'
            ]
        );
    }

    public function add(Request $request)
    {
        try {
            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $all_images = [];
            $ticket_images = $request->file('images');
            if ($request->hasFile('images')) {
                foreach ($ticket_images as $image) {
                    if ($image->isValid()) {
                        $imageName = rand(1, 100000) . time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('support_module/ticket_images'), $imageName);
                        $all_images[] =  $imageName;
                    }
                }
            }
            $model = new Support();
            $model->state_id = Support::STATE_PENDING;
            $model->image = !empty($all_images) ? json_encode($all_images) : null;
            $model->created_by_id = Auth::user()->id;
            $model->fill($request->all());
            if ($model->save()) {
                return redirect('/support')->with('success', 'Support created successfully!');
            } else {
                return redirect('/support/create')->with('error', 'Unable to save the Support!');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect('/support/create')->with('error', $bug);
        }
    }



    public function stateChange($id, $state)
    {
        try {
            $model = Support::find($id);
            if ($model) {
                $update = $model->update([
                    'state_id' => $state,
                ]);
                return redirect()->back()->with('success', 'Support has been ' . (($model->getState() != "New") ? $model->getState() . 'd!' : $model->getState()));
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function finalDelete($id)
    {
        try {
            $model = Support::find($id);
            if ($model) {
                $model->delete();
                return redirect('support')->with('success', 'Support has been deleted successfully!');
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
