<?php

namespace App\Http\Modules\Base\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
  /**
   * Autoriza a solicitação.
   *
   * @return bool - Retorna true se a solicitação for autorizada.
   */
  public function authorize(): bool
  {
    return true; // Pode ser ajustada conforme necessário.
  }

  /**
   * Define as regras de validação para a criação de um registro.
   *
   * @return array - Retorna um array contendo as regras de validação.
   */
  public function rules(): array
  {
    return [];
  }

  /**
   * Define mensagens personalizadas para as falhas de validação.
   *
   * @return array - Retorna um array contendo as mensagens de erro personalizadas.
   */
  public function messages(): array
  {
    return [];
  }
}
