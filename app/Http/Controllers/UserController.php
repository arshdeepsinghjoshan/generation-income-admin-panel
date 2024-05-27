<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\Permission;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            // if (User::isAdmin()) {
            $model = new User();
            return view('user.index', compact('model'));
            // }
        } catch (\Exception $e) {
            return redirect('user')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {

            $model  = new User();
            return view('user.add', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    protected static function stateValidator(array $data, $id = null)
    {
        return Validator::make(
            $data,
            [
                'model_type' => 'required',
                'model_id' => 'required',
                'workflow' => 'required'

            ],
            [
                'model_type.required' => 'The model type is not found.',
                'model_id.required' => 'The model id is not found.',
                'workflow.required' => 'The workflow is not found.',
            ]
        );
    }

    public function stateChange(Request $request)
    {
        try {
            if ($this->stateValidator($request->all())->fails()) {
                $message = $this->stateValidator($request->all())->messages()->first();
                return redirect()->back()->with('error', $message);
            }
            $model   = $request->model_type::find($request->model_id);
            if ($model) {
                $update = $model->update([
                    $request->attribute => $request->workflow,
                ]);
                return redirect()->back()->with('success', preg_replace('/(?<!\s)[A-Z]/', ' $0', class_basename($model)) . ' has been ' . $model->getState() . '!');
            } else {
                redirect()->back()->with('success', 'Something went wrong');
                return redirect('404');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $model =  User::find($id);
            if (empty($model)) {
                return redirect('user')->with('error', 'User does not exist');
            }
            if (!User::isAdmin()) {
                if ($model->role_id == User::ROLE_ADMIN) {
                    return redirect('user')->with('error', 'You are not allowed to perform this action.');
                }
                if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                    return redirect('user')->with('error', 'You are not allowed to perform this action.');
                }
            }
            if ($this->validator($request->all(), $id)->fails()) {
                $message = $this->validator($request->all(), $id)->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }

            $model->fill($request->all());
            if ($request->profile_image) {
                // dd($request->profile_image);
                $model->profile_image = $this->imageUpload($request, "profile_image", '/public/uploads');
            }
            $model->save();
            return redirect("user/view/$model->id")->with('success', 'User updated  successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public $s_no = 1;

    public function getUserList(Request $request, $id = null)
    {
        $query  = User::orderBy('id', 'Desc');

        if (empty($id)) {
            $query->where('role_id', User::ROLE_USER);
            if (!User::isAdmin())
                $query->my();
        }

        if (!empty($id))
            $query->where('id', $id);

        return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('role_id', function ($data) {
                return  $data->getRole();
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
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('user/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('user/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                                ->Where('name', 'like', "%$term%")
                                ->orWhere('email', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->Where('name', 'like', "%$term%");
                                })->orWhere(function ($query) use ($term) {
                                    $query->searchRole($term);
                                })->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                });
                        }
                    });
                }
            })
            ->make(true);
    }


   

    public function getrelationData(Request $request, $id = null)
    {
        return  $this->relationTable($request);
    }







    public function search(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'search' => 'required'
            ]);
            if ($validator->fails()) {
                $message = $validator->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $q =  $request->search;
            $customer = User::where('name', 'LIKE', '%' . $q . '%')->orWhere('email', 'LIKE', '%' . $q . '%')
                ->orWhere('id', 'LIKE', '%' . $q . '%')
                ->orWhere('email', 'LIKE', '%' . $q . '%')
                ->orWhere('referral_id', 'LIKE', '%' . $q . '%')
                ->first();
            if (!$customer)
                return redirect('/dashboard')->with('error', 'User not found');

            return redirect()->route('serach.user', $customer->id);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }


    public function searchUser(Request $request, $id)
    {
        try {
            $model  = User::find($id);

            if (!$model)
                return redirect('/')->with('error', 'User not found');
            if (!User::isAdmin()) {
                if ($model->role_id == User::ROLE_ADMIN) {
                    return redirect('user')->with('error', 'You are not allowed to perform this action.');
                }
                if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                    return redirect('user')->with('error', 'You are not allowed to perform this action.');
                }
            }
            return view('user.view', compact('model'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    protected static function validator(array $data, $id = null)
    {
        $rules = [
            "name" => "required|string",
            "email" => "required|email",
            "profile_image" => 'nullable|image|mimes:jpeg,png,jpg'
        ];
        if ($id === null) {
            $rules = array_merge($rules, [
                "password" => "required|string|min:4",
                "confirm_password" => "required|same:password",
                "referrad_code" => "required|exists:users,referral_id",

            ]);
        }
        return Validator::make($data, $rules);
    }

    public function add(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model = new User();
            $userGet = User::where('referral_id', $request->referrad_code)->first();
            if (!$userGet) {
                return redirect('/')->with('error', 'Invalid Code!');
            }
            $model->referrad_code = $userGet->referrad_code;
            $model->fill($request->all());
            $model->generateReferralCode();
            $model->role_id = User::ROLE_USER;
            $model->state_id = User::STATE_ACTIVE;
            $model->created_by_id = Auth::id();
            $model->parent_id = $userGet->id;
            $model->password = Hash::make($request->password);
            if ($request->profile_image) {
                $model->profile_image = $this->imageUpload($request, "profile_image", '/public/uploads');
            }
            if ($model->save()) {
                $walletModel = new Wallet();
                $walletModel->state_id = Wallet::STATE_ACTIVE;
                $walletModel->created_by_id = $model->id;
                $walletModel->generateWalletNumber();
                if (!$walletModel->save()) {
                    DB::rollBack();
                    return redirect('/')->with('error', 'Unable to save the User!');
                }
                DB::commit();
                return redirect('/user/view/' . $model->id)->with('success', 'User created successfully!');
            } else {
                DB::rollBack();
                return redirect('/user')->with('error', 'Unable to save the User!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = User::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->role_id == User::ROLE_ADMIN) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                    if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('user.update', compact('model'));
            } else {
                return redirect('user')->with('error', 'User not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = User::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->role_id == User::ROLE_ADMIN) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                    if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('user.view', compact('model'));
            } else {
                return redirect('/user')->with('error', 'User does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function registerReport()
    {
        try {
            $model = new User();
            return view('user.register_report', compact('model'));
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function getUserRegistrationData(Request $request)
    {

        $dataType = $request->input('type');
        $users = null;
        if ($dataType === 'dailyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereDate('created_at', now())->get();
        } elseif ($dataType === 'weeklyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        } elseif ($dataType === 'monthlyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->get();
        } elseif ($dataType === 'yearlyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereYear('created_at', now()->year)->get();
        }

        $userData = [];
        $userCount = 1;
        foreach ($users as $order) {
            $userData[] = [
                'date' => $order->created_at->format('Y-m-d'), // Assuming 'created_at' is the order date field
                'user' => $userCount++,
            ];
        }

        // Group user data by date
        $groupedUserData = [];
        foreach ($userData as $data) {
            $groupedUserData[$data['date']][] = $data['user'];
        }

        // Calculate total user for each date
        $userByDate = [];
        foreach ($groupedUserData as $date => $users) {
            $userByDate[] = [
                'date' => $date,
                'totalUser' => array_sum($users),
            ];
        }

        // Return user registration data as JSON
        return response()->json($userByDate);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();



        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/login');
    }



    public function userLogin($id)
    {
        try {
            Auth::loginUsingId($id);
            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
