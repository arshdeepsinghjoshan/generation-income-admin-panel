<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Traits\Permission;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        try {
            if (User::isAdmin()) {
                $model = new SubscriptionPlan();
                return view('subscription.plan.index', compact('model'));
            }
            $model =  SubscriptionPlan::findActive()->get();
            return view('subscription.plan.plan', compact('model'));
        } catch (\Exception $e) {
            return redirect('/dashboard')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function create(Request $request)
    {
        try {
            $model  = new SubscriptionPlan();
            return view('subscription.plan.add', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $model =  SubscriptionPlan::find($id);
            if (empty($model)) {
                return redirect('subscription/plan')->with('error', 'SubscriptionPlan does not exist');
            }
            if (!User::isAdmin()) {
                if ($model->role_id == User::ROLE_ADMIN) {
                    return redirect('subscription/plan')->with('error', 'You are not allowed to perform this action.');
                }
                if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                    return redirect('subscription/plan')->with('error', 'You are not allowed to perform this action.');
                }
            }
            if ($this->validator($request->all(), $id)->fails()) {
                $message = $this->validator($request->all(), $id)->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model->update($request->all());
            return redirect("subscription/plan/view/$model->id")->with('success', 'SubscriptionPlan updated  successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public $s_no = 1;

    public function getSubscriptionPlanList(Request $request, $id = null)
    {
        $query  = SubscriptionPlan::orderBy('id', 'Desc');
        if (empty($id))
            if (!User::isAdmin())
                $query->my();

        if (!empty($id))
            $query->where('created_by_id', $id);


        return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->addColumn('duration_type', function ($data) {
                return $data->getDurationType();
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('subscription/plan/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('subscription/plan/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                                ->orWhere('price', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->Where('name', 'like', "%$term%");
                                })->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                })->orWhere(function ($query) use ($term) {
                                    $query->durationType($term);
                                });
                        }
                    });
                }
            })
            ->make(true);

    }


    protected static function validator(array $data, $id = null)
    {
        $rules = [
            "title" => "required|string||max:128",
            "description" => "required|max:128",
            "duration_type" => "required|max:128",
            "duration" => "required|numeric|max:128",
            "price" => "required|numeric|max:12800",
        ];
        return Validator::make($data, $rules);
    }

    public function add(Request $request)
    {
        try {

            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model = new SubscriptionPlan();
            $model->fill($request->all());
            $model->state_id = SubscriptionPlan::STATE_ACTIVE;
            $model->created_by_id = Auth::id();
            if ($model->save()) {
                return redirect('/subscription/plan/view/' . $model->id)->with('success', 'SubscriptionPlan created successfully!');
            } else {
                return redirect('/subscription/plan')->with('error', 'Unable to save the SubscriptionPlan!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {

            $id = $request->id;
            $model  = SubscriptionPlan::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->created_by_id != Auth::user()->id) {
                        return redirect('subscription/plan/')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('subscription.plan.update', compact('model'));
            } else {
                return redirect('subscription/plan')->with('error', 'User not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = SubscriptionPlan::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->created_by_id != Auth::user()->id) {
                        return redirect('subscription/plan/')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('subscription.plan.view', compact('model'));
            } else {
                return redirect('/subscription/plan')->with('error', 'SubscriptionPlan does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

}
