<?php

namespace App\Http\Modules\Base\Repositories\Interfaces;

use Illuminate\Support\Collection;
use App\Http\Modules\Base\Models\BaseModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
  /**
   * Lista os registros com base nas colunas selecionadas.
   *
   * @param $request - Colunas para selecionar na consulta.
   * @return mixed - Retorna os resultados da consulta.
   */
  public function all($request);

  /**
   * Lista registros com base em filtros de pesquisa opcionais.
   *
   * @param $request - Filtros opcionais para a busca.
   * @return LengthAwarePaginator - Retorna um paginador contendo os registros encontrados com base nos filtros informados.
   */
  public function index($request): LengthAwarePaginator;

  /**
   * Busca um registro pelo ID.
   *
   * @param int $id - Identificador único do registro.
   * @return BaseModel|null - Retorna o registro correspondente ao ID informado ou null se não encontrado.
   */
  public function show(int $id): ?BaseModel;

  /**
   * Cria um novo registro.
   *
   * @param array $data - Dados necessários para criar o registro.
   * @return BaseModel|null - Retorna o registro criado ou null em caso de falha.
   */
  public function store(array $data): ?BaseModel;

  /**
   * Atualiza um registro existente.
   *
   * @param array $data - Dados para atualização do registro.
   * @param int $id - ID do registro.
   * @return BaseModel|null - Retorna o registro atualizado ou null se não for encontrado.
   */
  public function update(array $data, int $id): ?BaseModel;

  /**
   * Remove um registro pelo ID.
   *
   * @param int $id - ID do registro a ser removido.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int $id): bool;
}
