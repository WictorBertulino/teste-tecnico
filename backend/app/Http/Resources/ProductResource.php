<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'nome' => $this->nome,
            'sku' => $this->sku,
            'preco' => (float) $this->preco,
            'estoque_atual' => $this->estoque_atual,
            'estoque_minimo' => $this->estoque_minimo,
            'ativo' => $this->ativo,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
