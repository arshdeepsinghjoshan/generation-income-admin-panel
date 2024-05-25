<?php

namespace App\Http\Controllers;

use App\Models\SupportDepartment;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Support\Str;
use DataTables;

class SupportDepartmentController extends Controller
{
    public $setFilteredRecords = 0;
    public function index()
    {

        $model = new SupportDepartment();


        return view('support.department.index', compact('model'));
    }
    public function create()
    {


        $model = new SupportDepartment();


        return view('support.department.update', compact('model'));
    }
    protected static function validator(array $data, $id = null)
    {
        return Validator::make($data, [
            'title' => [
                'required',
                'string',
                'max:150',
                Rule::unique('support_departments', 'title')->ignore($id)
            ],

        ]);
    }

    public function store(Request $request)
    {
        try {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $message = $validator->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }

            $model = new SupportDepartment();
            $model->state_id = SupportDepartment::STATE_ACTIVE;
            $model->created_by_id = Auth::user()->id;
            $model->fill($request->all());
            $model->save();
            return redirect('support/department')->with('success', 'Category created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = SupportDepartment::find($id);
            if ($model) {


                return view('support.department.update', compact('model'));
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
            $model  = SupportDepartment::find($id);
            if ($model) {

                return view('support.department.view', compact('model'));
            } else {
                return redirect('support/department')->with('error', 'Category does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $model =  SupportDepartment::find($id);
            if (empty($model)) {
                return redirect('support/department')->with('error', 'Category does not exist');
            }
            if ($this->validator($request->all(), $id)->fails()) {
                $message = $this->validator($request->all(), $id)->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model->update($request->all());
            return redirect('support/department')->with('success', 'Category updated  successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating department');
        }
    }



    public function getDepartmenttList(Request $request, $id = null)
    {
        $query  = SupportDepartment::with(['createdBy']);


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
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('support/department/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('support/department/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                                ->orWhere('description', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->Where('name', 'like', "%$term%");
                                })->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                });
                        }
                    });
                }
            })
            ->make(true);
    }


    public function stateChange($id, $state)
    {
        try {
            $model = SupportDepartment::find($id);
            if (!$model) {
                return redirect('404');
            }
            $update = $model->update([
                'state_id' => $state,
            ]);
            return redirect()->back()->with('success', 'Category has been ' . (($model->getState() != "New") ? $model->getState() . 'd!' : $model->getState()));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while changing department state');
        }
    }

    public function finalDelete($id)
    {
        try {
            $model = SupportDepartment::find($id);
            if (!$model) {
                return redirect('support/department')->with('error', 'Category not found!');
            }
            $supportExists = Support::where('department_id', $model->id)->exists();
            if ($supportExists) {
                return redirect('support/department')->with('error', 'You are not allowed to perform this action!');
            }
            $model->delete();
            return redirect('support/department')->with('success', 'Category has been deleted successfully!');
        } catch (\Exception $e) {
            return redirect('support/department')->with('error', 'An error occurred while deleting department');
        }
    }
}
