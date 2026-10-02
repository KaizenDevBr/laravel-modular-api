<?php

namespace App\Http\Modules\Base\Services\Interfaces;

interface BaseServiceInterface
{
  /**
   * Obtém todos os departamentos com base nas colunas selecionadas.
   *
   * @param array $select - Colunas para selecionar.
   * @return mixed - Retorna a lista de departamentos.
   */
  public function all(array $select);

  /**
   * Lista registros com base em critérios de pesquisa opcionais.
   *
   * @param array $filters - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção de registros ou um resultado do repositório.
   */
  public function index(array $filters): mixed;

  /**
   * Retorna dados completos de um único registro de acordo com o seu ID fornecido.
   *
   * @param int $id - ID do registro.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function show(int $id): mixed;

  /**
   * Cria um novo registro.
   *
   * @param array $data - Dados necessários para criar o registro.
   * @return mixed - Retorna o registro criado.
   */
  public function store(array $data): mixed;

  /**
   * Atualiza um registro com os dados fornecidos.
   *
   * @param array $data - Dados para atualização.
   * @param int $id - ID do registro a ser atualizado.
   * @return mixed - Retorna o registro atualizado ou uma resposta do repositório.
   */
  public function update(array $data, int $id): mixed;

  /**
   * Exclui um registro com base no ID fornecido.
   *
   * @param int $id - ID do registro a ser excluído.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int $id): bool;
}
