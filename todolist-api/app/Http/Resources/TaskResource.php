<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TaskResource extends JsonResource
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
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date ? $this->formatDateForBrazil($this->due_date) : null,
            'created_at' => $this->formatDateForBrazil($this->created_at),
            'updated_at' => $this->formatDateForBrazil($this->updated_at),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }

    /**
     * Formatar data para o padrão brasileiro
     */
    private function formatDateForBrazil($date)
    {
        if (!$date) {
            return null;
        }

        // Garantir que a data esteja no timezone do Brasil
        $carbonDate = Carbon::parse($date)->setTimezone('America/Sao_Paulo');
        
        return [
            'formatted' => $carbonDate->format('d/m/Y H:i:s'),
            'iso' => $carbonDate->toISOString(),
            'timestamp' => $carbonDate->timestamp,
            'relative' => $carbonDate->diffForHumans(),
        ];
    }
}
