<?php

namespace App\Http\Controllers\AgentAssignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentAssignment\CreateAgentAssignmentRequest;
use App\Http\Requests\AgentAssignment\UpdateAgentAssignmentRequest;
use App\Models\AgentAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class AgentAssignmentController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('agent-assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        $agentAssignments = AgentAssignment::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when($request->user()->access_level == 'Assembly_Supervisor', function ($query) use ($request) {
                $query->where('supervisor_id', $request->user()->id);
            })
            ->when($request->user()->access_level == 'Assembly_Agent', function ($query) use ($request) {
                $query->where('agent_id', $request->user()->id);
            })
            ->get();

        return view('agent-assignments.index', compact('agentAssignments'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('agent-assignments.create')) {
            abort(403, 'Unauthorized action.');
        }

        $agents = User::where('access_level', 'Assembly_Agent')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('status', 'Active')
            ->whereDoesntHave('agentAssignments', function ($query) {
                $query->whereNotNull('agent_id');
            })
            ->get();

        $supervisors = User::where('access_level', 'Assembly_Supervisor')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('status', 'Active')
            ->get();

        $assembly = $request->user()->assembly->name ?? '';
        $assemblyCode = $request->user()->assembly_code;

        $data = [
            'assembly' => isset($assembly) ? $assembly : 'N/A',
            'assemblyCode' => isset($assemblyCode) ? $assemblyCode : 'N/A',
        ];

        return view('agent-assignments.create', compact('agents', 'supervisors', 'data'));
    }

    public function store(CreateAgentAssignmentRequest $request)
    {
        $agentAssignment = [];

        foreach ($request->validated('agent_id') as $agent) {
            $agentAssignment[] = [
                'supervisor_id' => $request->validated('supervisor_id'),
                'agent_id' => $agent,
                'assembly_code' => $request->validated('assembly_code'),
                'created_by' => $request->user()->id,
            ];
        }

        foreach ($agentAssignment as $data) {
            AgentAssignment::create($data);
        }

        return redirect()->route('agent-assignments.index')->with('status', 'Agent assignment created successfully.');
    }

    public function show(AgentAssignment $agentAssignment)
    {
        return view('agent-assignments.show', compact('agentAssignment'));
    }

    public function edit(Request $request, AgentAssignment $agentAssignment)
    {
        if (! auth()->user()->can('agent-assignments.update')) {
            abort(403, 'Unauthorized action.');
        }

        $supervisors = User::where('access_level', 'Assembly_Supervisor')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('status', 'Active')
            ->get();

        return view('agent-assignments.edit', compact('supervisors', 'agentAssignment'));
    }

    public function update(UpdateAgentAssignmentRequest $request, AgentAssignment $agentAssignment)
    {
        $agentAssignment->update($request->validated());

        return redirect()->route('agent-assignments.index')->with('status', 'Agent assignment updated successfully.');
    }
}
