<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    public function getProjects(array $filters)
    {

        $query = Project::with('creator:name,id,role');

        if (!empty($filters['search'])) {
            $query->where('name', 'LIKE', '%' . $filters['search'] . '%');
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $allowedSorts = ['id', 'name', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query->select('id','name','description','created_by')->orderBy($sortBy, $sortOrder);

        return $query->paginate(
            $filters['per_page'] ?? 10
        );
    }

    public function create(array $data)
    {
        try{
            $data['created_by'] = auth()->id();
            $project = Project::create($data);
            $project->load('creator:id,name,role');

            return response()->json([
                'status' => true,
                'message' => 'Project created successfully',
                'data' => $project
            ], 201);

        }catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(string $project_id, array $data)
    {
        try {
            $project = Project::findOrFail($project_id);
            $project->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Project updated successfully',
                'data' => $project->refresh()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function delete(string $project_id)
    {
        try {

            Project::findOrFail($project_id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Project deleted successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function view(string $project_id)
    {
        try {

            $project = Project::with('creator:name,id,role')->select('id','name','description','created_by')->findOrFail($project_id);
            return response()->json([
                'status' => true,
                'data' => $project
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}