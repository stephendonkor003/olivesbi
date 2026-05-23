<?php

namespace App\Http\Controllers\API\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskAssignment\UpdateTaskAssignment;
use App\Http\Requests\API\TaskAssignment\UploadReportRequest;
use App\Http\Requests\API\User\UpdateUserRequest;
use App\Models\Block;
use App\Models\Payment;
use App\Models\Property;
use App\Models\ReportUpload;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $data = User::orderBy('created_at', 'DESC')
            ->with(['assembly', 'division'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('access_level', 'Assembly_Agent')
            ->get();

        return response()->json([
            'message' => 'Get all agents',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $user = User::query()
            ->with(['assembly', 'division'])
            ->where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($user)) {
            return response()->json([
                'message' => 'Agent not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular agent',
            'data' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();

        $user = User::query()
            ->with(['assembly', 'division'])
            ->where('id', $id)
            ->first();

        if (empty($user)) {
            return response()->json([
                'message' => 'Agent not found',
            ], 422);
        }

        if (empty($request->validated('password'))) {
            $data['password'] = $user->password;
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Agent record updated successfully',
        ]);
    }

    public function agentTask(Request $request)
    {
        $taskAssignments = TaskAssignment::orderBy('created_at', 'DESC')
            ->with(['supervisor', 'agent', 'assembly'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when($request->user()->access_level == 'Assembly_Supervisor', function ($query) use ($request) {
                $query->where('supervisor_id', $request->user()->id);
            })
            ->when($request->user()->access_level == 'Assembly_Agent', function ($query) use ($request) {
                $query->where('agent_id', $request->user()->id);
            })
            ->get()
            ->map(function ($assignment) {
                $assignment->block_data = collect($assignment->block_data)->map(function ($block) {
                    $blockModel = Block::find($block['block_id']);
                    $block['name'] = $blockModel ? $blockModel->block_name : 'Unknown';

                    return $block;
                })->toArray();

                $assignment->block_count = count($assignment->block_data);

                return $assignment;
            });

        return response()->json([
            'message' => 'Get all task assignments',
            'data' => $taskAssignments,
        ]);
    }

    public function agentTaskShow(Request $request, $id)
    {
        $taskAssignment = TaskAssignment::orderBy('created_at', 'DESC')
            ->with(['supervisor', 'agent', 'assembly'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when($request->user()->access_level == 'Assembly_Supervisor', function ($query) use ($request) {
                $query->where('supervisor_id', $request->user()->id);
            })
            ->when($request->user()->access_level == 'Assembly_Agent', function ($query) use ($request) {
                $query->where('agent_id', $request->user()->id);
            })
            ->where('id', $id)
            ->first();

        if ($taskAssignment) {
            $taskAssignment->block_data = collect($taskAssignment->block_data)->map(function ($block) {
                $blockModel = Block::find($block['block_id']);
                $block['name'] = $blockModel ? $blockModel->block_name : 'Unknown';

                return $block;
            })->toArray();

            $taskAssignment->block_count = count($taskAssignment->block_data);
        }

        if (empty($taskAssignment)) {
            return response()->json([
                'message' => 'Agent assignment not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular task assignmnet',
            'data' => $taskAssignment,
        ]);
    }

    public function agentTaskAssignment(Request $request, $id)
    {
        $agent = User::where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($agent)) {
            return response()->json([
                'message' => 'Agent not found or the user is not an agent, check and try again',
            ], 422);
        }

        $totalTask = 0;
        $totalTaskType = [
            'totalTask' => [],
            'Pending' => [],
            'Completed' => [],
        ];

        $taskAssignments = TaskAssignment::orderBy('created_at', 'DESC')
            ->with(['supervisor', 'agent', 'assembly'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when($request->user()->access_level == 'Assembly_Supervisor', function ($query) use ($request) {
                $query->where('supervisor_id', $request->user()->id);
            })
            ->when($request->user()->access_level == 'Assembly_Agent', function ($query) use ($request) {
                $query->where('agent_id', $request->user()->id);
            })
            ->where('agent_id', $agent->id)
            ->get()
            ->map(function ($assignment) use (&$totalTask, &$totalTaskType) {
                $assignment->block_data = collect($assignment->block_data)->map(function ($block) use ($assignment) {
                    $blockModel = Block::find($block['block_id']);
                    $block['block_name'] = $blockModel ? $blockModel->block_name : 'Unknown';

                    $block['property_count'] = Property::where('block_id', $block['block_id'])->count();

                    $taskAssignment = TaskAssignment::where('id', $assignment->id)->first();
                    $task = null;

                    if ($taskAssignment && is_array($taskAssignment->block_data)) {
                        foreach ($taskAssignment->block_data as $taskBlock) {
                            if ($taskBlock['block_id'] == $block['block_id']) {
                                $task = $assignment->task;
                                break;
                            }
                        }
                    }

                    if ($task === 'Payment Collection') {
                        $block['total_payments'] = Property::where('block_id', $block['block_id'])
                            ->get()
                            ->reduce(function ($carry, $property) {
                                $propertyPayments = DB::table('payments')
                                    ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
                                    ->where('bills.property_id', $property->id)
                                    ->select(
                                        DB::raw('SUM(CASE 
                                            WHEN payment_mode = "momo" AND transaction_status = "Success" THEN payments.amount 
                                            WHEN payment_mode != "momo" THEN payments.amount 
                                            ELSE 0 
                                        END) as total_payments')
                                    )
                                    ->value('total_payments') ?? 0;

                                return $carry + $propertyPayments;
                            }, 0);

                        $block['total_bills'] = Property::where('block_id', $block['block_id'])
                            ->get()
                            ->reduce(function ($carry, $property) {
                                $propertyBills = DB::table('bills')
                                    ->where('property_id', $property->id)
                                    ->sum('amount');

                                return $carry + $propertyBills;
                            }, 0);
                    }

                    return $block;
                })->toArray();

                $assignment->block_count = count($assignment->block_data);
                $totalTask = collect($assignment->block_data)->sum('property_count');

                foreach ($assignment->block_data as $block) {
                    $taskStatus = $block['status'];
                    $taskType = $assignment->task;

                    if (! isset($totalTaskType['totalTask'][$taskType])) {
                        $totalTaskType['totalTask'][$taskType] = 0;
                    }

                    $totalTaskType['totalTask'][$taskType] += $block['property_count'];

                    if ($taskStatus === 'Pending') {
                        if (! isset($totalTaskType['Pending'][$taskType])) {
                            $totalTaskType['Pending'][$taskType] = 0;
                        }

                        $totalTaskType['Pending'][$taskType] += $block['property_count'];
                    } elseif ($taskStatus === 'Completed') {
                        if (! isset($totalTaskType['Completed'][$taskType])) {
                            $totalTaskType['Completed'][$taskType] = 0;
                        }

                        $totalTaskType['Completed'][$taskType] += $block['property_count'];
                    }

                    if ($taskType === 'Payment Collection') {
                        if (! isset($totalTaskType['totalPayments'][$taskType])) {
                            $totalTaskType['totalPayments'][$taskType] = 0;
                        }

                        if (! isset($totalTaskType['totalBills'][$taskType])) {
                            $totalTaskType['totalBills'][$taskType] = 0;
                        }

                        $block['total_payments'] = Property::where('block_id', $block['block_id'])
                            ->get()
                            ->reduce(function ($carry, $property) {
                                return $carry + DB::table('payments')
                                    ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
                                    ->where('bills.property_id', $property->id)
                                    ->select(
                                        DB::raw('SUM(CASE 
                                            WHEN payment_mode = "momo" AND transaction_status = "Success" THEN payments.amount 
                                            WHEN payment_mode != "momo" THEN payments.amount 
                                            ELSE 0 
                                        END) as total_payments')
                                    )
                                    ->value('total_payments') ?? 0;
                            }, 0);

                        $block['total_bills'] = Property::where('block_id', $block['block_id'])
                            ->get()
                            ->reduce(function ($carry, $property) {
                                return $carry + DB::table('bills')
                                    ->where('property_id', $property->id)
                                    ->sum('amount');
                            }, 0);

                        $totalTaskType['totalPayments'][$taskType] += $block['total_payments'];
                        $totalTaskType['totalBills'][$taskType] += $block['total_bills'];
                    }
                }

                if (isset($totalTaskType['Completed']) && empty($totalTaskType['Completed'])) {
                    $totalTaskType['Completed'] = (object) [];
                } else {
                    foreach ($totalTaskType['Completed'] as $taskType => $value) {
                        if ($value === 0) {
                            $totalTaskType['Completed'][$taskType] = (object) [];
                        }
                    }

                    if (! empty($totalTaskType['Completed'])) {
                        $totalTaskType['Completed'] = (array) $totalTaskType['Completed'];
                    }
                }

                return $assignment;
            });

        return response()->json([
            'message' => 'Get all agent task assignments',
            'data' => $taskAssignments,
            'dashboard' => $totalTaskType,
        ]);
    }

    public function agentTaskBlock($id, $task_type)
    {
        $agent = User::where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($agent)) {
            return response()->json([
                'message' => 'Agent not found or the user is not an agent, check and try again',
            ], 422);
        }

        $taskAssignments = TaskAssignment::orderBy('created_at', 'DESC')
            ->where('agent_id', $agent->id)
            ->where('task', 'LIKE', $task_type)
            ->get()
            ->map(function ($assignment) {
                $assignment->block_data = collect($assignment->block_data)->map(function ($block) {
                    $blockModel = Block::find($block['block_id']);

                    if ($blockModel) {
                        $block['block_code'] = $blockModel->block_code;
                        $block['block_name'] = $blockModel->block_name;
                    } else {
                        $block['block_name'] = 'Unknown';
                        $block['block_description'] = 'No description available';
                    }

                    $block['property_count'] = Property::where('block_id', $block['block_id'])->count();

                    return $block;
                })->toArray();

                return $assignment;
            });

        return response()->json([
            'message' => 'Get agent task blocks by task type',
            'data' => $taskAssignments,
        ]);
    }

    public function agentPropertyBlock($id)
    {
        $properties = Property::orderBy('created_at', 'DESC')
            ->with([
                'customer',
                'assembly',
                'polygon.block.division',
                'bills.payments',
                'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            ])
            ->whereHas('polygon', function ($q) use ($id) {
                $q->where('block_id', $id);
            })
            ->get();

        if (count($properties) == 0) {
            return response()->json([
                'message' => 'No properties under the block provided',
            ], 422);
        }

        return response()->json([
            'message' => 'Get properties by block',
            'data' => $properties,
        ]);
    }

    public function agentPayment($id)
    {
        $agent = User::where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($agent)) {
            return response()->json([
                'message' => 'Agent not found, or the user is not an agent',
            ], 422);
        }

        $payments = Payment::orderBy('created_at', 'DESC')
            ->with(['assembly', 'bill'])
            ->where('created_by', $agent->id)
            ->get()
            ->map(function ($payment) {
                if (is_null($payment->bill->property_id)) {
                    $payment->bill_type = 'Business Bill';
                } elseif (is_null($payment->bill->business_id)) {
                    $payment->bill_type = 'Property Bill';
                } else {
                    $payment->bill_type = 'Unknown';
                }

                return $payment;
            });

        return response()->json([
            'message' => 'Get agent payment collection',
            'data' => $payments,
        ]);
    }

    public function taskUpdate(UpdateTaskAssignment $request, $id)
    {
        $taskAssignment = TaskAssignment::where('id', $id)->first();

        if (empty($taskAssignment)) {
            return response()->json([
                'message' => 'Agent task assignment not found',
            ], 422);
        }

        $blockData = $taskAssignment->block_data;

        $statusUpdate = [
            $request->block_id => $request->status,
        ];

        // Update the block_data
        foreach ($blockData as &$data) {
            if (array_key_exists($data['block_id'], $statusUpdate)) {
                $data['status'] = $statusUpdate[$data['block_id']];
            }
        }

        $taskAssignment->block_data = $blockData;
        $taskAssignment->save();

        return response()->json([
            'message' => 'Task assignment status updated successfully',
        ]);
    }

    public function uploadReport(UploadReportRequest $request)
    {
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $file_name = ReportUpload::generateFileName();
            $file_name = $file_name.'.'.$file->getClientOriginalExtension();
            $fileDestinationPath = storage_path('app/public/files');
            $file->move($fileDestinationPath, $file_name);
        }

        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? '';
        $data['file_path'] = $file_name ?? null;

        ReportUpload::create($data);

        return response()->json([
            'message' => 'Task assignment report uploaded successfully',
        ]);
    }
}
