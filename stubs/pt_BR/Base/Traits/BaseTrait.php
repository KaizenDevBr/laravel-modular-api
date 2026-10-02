<?php

namespace App\Http\Modules\Base\Traits;

trait BaseTrait
{
  /**
   * Retorna uma resposta JSON padronizada para sucesso ou erro.
   *
   * @param string $type - Tipo da resposta ("success" ou "error").
   * @param mixed $message - Mensagem da resposta.
   * @param mixed|null $data - Dados adicionais (conteúdo misto).
   * @param int|null $statusCode - Código HTTP da resposta (se informado, sobrescreve o padrão relacionado com mo "type").
   * 
   * @return \Illuminate\Http\JsonResponse - Retorna a resposta JSON formatada.
   */
  public function sendResponse(string $type, mixed $message, mixed $data = null, ?int $statusCode = null)
  {
    // Define os status padrão com base no tipo
    $defaultStatus = match ($type) {
      'success' => 200,
      'error'   => 400,
      default   => 200, // fallback
    };

    // Se o usuário passou statusCode, sobrescreve o padrão
    $status = $statusCode ?? $defaultStatus;

    // Monta o payload da resposta
    $response = [
      'success' => $type === 'success',
      'message' => $message,
      'data'    => $data,
      'status'  => $status,
    ];

    return response()->json($response, $status);
  }

  /**
   * Retorna uma resposta de erro padronizada.
   *
   * @param mixed $message - Mensagem de erro.
   * @param mixed|null $data - Dados adicionais de erro.
   * @param int|null $statusCode - Código HTTP (padrão 400).
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendError(mixed $message, mixed $data = null, ?int $statusCode = 400)
  {
    return $this->sendResponse('error', $message, $data, $statusCode);
  }
}
