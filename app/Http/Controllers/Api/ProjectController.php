<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request)
    {
        $projects = $this->projectService->getProjects($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Projects fetched successfully',
            'data' => $projects,
        ]);
    }

    public function store(CreateProjectRequest $request)
    {
        return $this->projectService->create($request->validated());
    }

    
    public function show(string $project_id)
    {
        return $this->projectService->view($project_id);
    }

    
    public function update(UpdateProjectRequest $request,string $project_id) {

        return $this->projectService->update($project_id, $request->validated());
    }

    
    public function destroy(string $project_id)
    {
        return $this->projectService->delete($project_id);
    }
}
