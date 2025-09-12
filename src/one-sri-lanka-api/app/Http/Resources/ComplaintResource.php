<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'submitted_at' => $this->submitted_at,
            'resolved_at' => $this->resolved_at,
            'form_data' => $this->form_data ? json_decode($this->form_data, true) : null,
            
            // User information (only for admin views)
            'user' => $this->when($request->routeIs('admin.*'), [
                'id' => $this->user_id,
                'name' => $this->user_name ?? 'N/A',
                'email' => $this->user_email ?? 'N/A',
            ]),
            
            // Category information
            'category' => [
                'id' => $this->complaint_category_id,
                'name' => $this->category_name ?? 'N/A',
                'description' => $this->category_description ?? null,
            ],
            
            // Entity information
            'entity' => [
                'id' => $this->entity_id,
                'name' => $this->entity_name ?? 'N/A',
                'type' => $this->entity_type ?? 'N/A',
                'description' => $this->entity_description ?? null,
            ],
            
            // Common complaint template info (if applicable)
            'common_complaint' => $this->when($this->common_complaint_id, [
                'id' => $this->common_complaint_id,
                'title' => $this->common_complaint_title ?? 'N/A',
                'form_data' => $this->common_complaint_form_data ? json_decode($this->common_complaint_form_data, true) : null,
            ]),
            
            // Timestamps
            'created_at' => $this->created_at ?? $this->submitted_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
