<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'createdAt' => $this->created_at->format('Y-m-d'),
            'dueDate' => $this->due_date->format('Y-m-d'),
            'status' => $this->status,
            'priority' => $this->priority,
            'project_id' => $this->project_id,
            'assigned_user_id' => $this->assigned_user_id,
            'image' => $this->image ? Storage::url($this->image) : '',
            'project' => new ProjectResource($this->project),
            'assignedUser' => $this->assignedUser ? new UserResource($this->assignedUser) : null,
            'createdBy' => new UserResource($this->createdBy),
            'updatedBy' => new UserResource($this->updatedBy),
        ];
    }
}
