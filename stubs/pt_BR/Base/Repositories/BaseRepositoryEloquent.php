<?php

namespace App\Http\Modules\Base\Repositories;

use App\Http\Modules\Base\Models\BaseModel;
use App\Http\Modules\Base\Traits\BaseTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Modules\Base\Repositories\Interfaces\BaseRepositoryInterface;

class BaseRepositoryEloquent implements BaseRepositoryInterface
{
  use BaseTrait;
  protected BaseModel $model;

  /**
   * Construtor do repositório.
   *
   * @param BaseModel $baseModel - Instância do modelo.
   */
  public function __construct(BaseModel $baseModel)
  {
    $this->model = $baseModel;
  }

  /**
   * Lista os registros com base nas colunas selecionadas.
   *
   * @param $request - Colunas para selecionar na consulta.
   * @return mixed - Retorna os resultados da consulta.
   */
  public function all($request)
  {
    $query = $this->model;

    // Transformando em array os parâmetros passados na requisição para exibição dinâmica da resposta 
    $select = $request->query('select', []);
    $where_col = $request->query('where_col', []);
    $where_val = $request->query('where_val', []);
    $order_col = $request->query('order_col');
    $order_direction = $request->query('order_direction');

    // SELECT
    $query = !empty($select)
      ? $query->select($select)
      : $query->select('*');

    // WHERE
    if (!empty($where_col) && !empty($where_val) && count($where_col) === count($where_val)):
      foreach (array_combine($where_col, $where_val) as $col => $val):
        $query->where($col, $val);
      endforeach;
    endif;

    // ORDER COLUMN
    $order_col = !empty($order_col)
      ? $order_col
      : 'id';

    // ORDER DIRECTION
    $order_direction = !empty($order_direction)
      ? $order_direction
      : 'ASC';

    return
      $query
      ->orderBy($order_col, $order_direction)
      ->get();
  }

  /**
   * Lista registros com base em filtros de pesquisa opcionais.
   *
   * @param $request - Filtros opcionais para a busca.
   * @return LengthAwarePaginator - Retorna um paginador contendo os registros encontrados com base nos filtros informados.
   */
  public function index($request): LengthAwarePaginator
  {
    $query = $this->model;

    // Transformando em array os parâmetros passados na requisição para exibição dinâmica da resposta 
    $select = $request->query('select', []);
    $where_col = $request->query('where_col', []);
    $where_val = $request->query('where_val', []);
    $like_col = $request->query('like_col', []);
    $like_val = $request->query('like_val', []);
    $where_rel = $request->query('where_rel', []);
    $where_rel_col = $request->query('where_rel_col', []);
    $where_rel_val = $request->query('where_rel_val', []);
    $with = $request->query('with', []); // Relacionamentos
    $order_col = $request->query('order_col');
    $order_direction = $request->query('order_direction');
    $limit = $request->query('limit');

    // Lista de relacionamentos permitidos para evitar carregamento indevido
    $allowedRelationships = [
    ];

    // SELECT
    $query = !empty($select)
      ? $query->select($select)
      : $query->select('*');

    // WITH
    if (!empty($with)) {
      // Filtra apenas os relacionamentos permitidos
      $with = array_intersect((array)$with, $allowedRelationships);
      $query->with($with);
    }

    // WHERE (na tabela principal) - igualdade exata
    if (!empty($where_col) && !empty($where_val) && count($where_col) === count($where_val)):
      foreach (array_combine($where_col, $where_val) as $col => $val):
        // permite colunas qualificadas (table.col) ou simples
        $query->where($col, $val);
      endforeach;
    endif;

    // LIKE (na tabela principal) - busca parcial (case-insensitive depende do collation do DB)
    if (!empty($like_col) && !empty($like_val) && count($like_col) === count($like_val)):
      foreach (array_combine($like_col, $like_val) as $col => $val):
        // usa binding para evitar SQL injection. Aceita colunas qualificadas (table.col) ou simples.
        // Quando a coluna vier qualificada (ex: "perfis.nome") mantemos como está.
        // OBS: a sensibilidade a maiúsculas/minúsculas depende do collation do seu banco.
        $query->whereRaw("{$col} LIKE ?", ["%{$val}%"]);
      endforeach;
    endif;

    // WHERE (nos relacionamentos) - QUALIFICANDO colunas para evitar ambiguidades
    if (
      !empty($where_rel) && !empty($where_rel_col) && !empty($where_rel_val)
      && count($where_rel) === count($where_rel_col) && count($where_rel_col) === count($where_rel_val)
    ) {
      foreach ($where_rel as $index => $relation) {
        $col = $where_rel_col[$index] ?? null;
        $val = $where_rel_val[$index] ?? null;

        if ($relation && $col && isset($val)) {
          // 1) Se a coluna já vier qualificada (ex: "permissoes.id" ou "perfil_permissao.permissao_id"), usa como está.
          if (strpos($col, '.') !== false) {
            $qualifiedCol = $col;
          } else {
            // 2) tenta descobrir a tabela do modelo relacionado via a relação do Eloquent
            $relatedTable = $relation; // fallback: nome da relação
            try {
              // Verifica se existe método de relação no modelo
              if (method_exists($this->model, $relation)) {
                $relationObj = $this->model->{$relation}();
                if (method_exists($relationObj, 'getRelated')) {
                  $relatedModel = $relationObj->getRelated();
                  if (method_exists($relatedModel, 'getTable')) {
                    $relatedTable = $relatedModel->getTable();
                  }
                }
              }
            } catch (\Throwable $e) {
              // fallback: mantém $relatedTable como o nome da relação
            }

            // agora qualifica a coluna com o nome da tabela deduzido
            $qualifiedCol = "{$relatedTable}.{$col}";
          }

          // Usa whereHas com whereRaw qualificado (binding)
          $query->whereHas($relation, function ($subQuery) use ($qualifiedCol, $val) {
            $subQuery->whereRaw("{$qualifiedCol} = ?", [$val]);
          });

          // Se o relacionamento estiver no "with", aplica o mesmo filtro no carregamento para não trazer itens não correspondentes
          if (in_array($relation, (array)$with, true)) {
            $query->with([$relation => function ($subQuery) use ($qualifiedCol, $val) {
              $subQuery->whereRaw("{$qualifiedCol} = ?", [$val]);
            }]);
          }
        }
      }
    }

    // ORDER COLUMN
    $order_col = !empty($order_col)
      ? $order_col
      : 'id';

    // ORDER DIRECTION
    $order_direction = !empty($order_direction)
      ? $order_direction
      : 'ASC';

    // LIMIT
    $limit = !empty($limit)
      ? $limit
      : 10;

    return
      $query
      ->orderBy($order_col, $order_direction)
      ->paginate($limit); // Retorna os registros com paginação.
  }

  /**
   * Busca um registro pelo ID.
   *
   * @param int $id - Identificador único do registro.
   * @return Model|null - Retorna o registro correspondente ao ID informado ou null se não encontrado.
   */
  public function show(int $id): ?BaseModel
  {
    return $this->model->find($id);
  }

  /**
   * Cria um novo registro.
   *
   * @param array $data - Dados necessários para criar o registro.
   * @return BaseModel|null - Retorna o registro criado ou null em caso de falha.
   */
  public function store(array $data): ?BaseModel
  {
    try {
      // Cria uma nova instância do modelo e preenche com os dados fornecidos.
      $baseModel = $this->model->create($data);

      return $baseModel; // Retorna o modelo criado.

    } catch (\Exception $e) {
      return $this->sendError('Ocorreu um erro ao cadastrar o registro no banco de dados.', 500);
    }
  }

  /**
   * Atualiza um registro existente.
   *
   * @param array $data - Dados para atualização do registro.
   * @param int $id - ID do registro.
   * @return BaseModel|null - Retorna o registro atualizado ou null se não for encontrado.
   */
  public function update(array $data, int $id): ?BaseModel
  {
    // Busca o registro pelo ID.
    $baseModel = $this->model->find($id);

    if (!$baseModel) {
      return null; // Retorna null se o registro não for encontrado.
    }

    // Atualiza os dados e salva.
    $baseModel->fill($data);
    $baseModel->save();

    return $baseModel; // Retorna o registro atualizado.
  }

  /**
   * Remove um registro pelo ID.
   *
   * @param int $id - ID do registro a ser removido.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int $id): bool
  {
    $baseModel = BaseModel::find($id);

    if (!$baseModel) {
      return false;
    }

    return $baseModel->delete();
  }
}
