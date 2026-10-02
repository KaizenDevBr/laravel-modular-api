<?php

namespace App\Http\Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe BaseModel
 * 
 * Esta classe serve como base para todos os outros modelos do sistema.
 * Ela implementa configurações padrão, como a proteção contra atribuição em massa
 * (mass assignment) e funções dinâmicas para manipulação de colunas protegidas e preenchíveis.
 */
class BaseModel extends Model
{
  /**
   * Colunas protegidas (guarded).
   * 
   * Impede a atribuição em massa (mass assignment) nestas colunas.
   * Ao definir apenas os campos de controle (id e timestamps), ativamos o
   * "autoload de fillables", ou seja, todas as outras colunas da tabela
   * passam a ser preenchíveis (fillable) automaticamente sem precisarmos listá-las.
   * 
   * @var array
   */
  protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

  /**
   * Construtor da BaseModel.
   * 
   * Permite configurar comportamentos padrão e injetar atributos no modelo.
   *
   * @param array $attributes Atributos iniciais do modelo.
   */
  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
  }

  /**
   * Adiciona novas colunas à lista de protegidas (guarded) dinamicamente.
   * 
   * Útil quando precisamos proteger campos extras em tempo de execução
   * para uma operação específica.
   *
   * @param array $columns Lista de colunas a serem protegidas.
   * @return void
   */
  public function addGuarded(array $columns): void
  {
    $this->guarded = array_merge($this->guarded, $columns);
  }

  /**
   * Adiciona colunas preenchíveis (fillable) dinamicamente.
   * 
   * Caso haja necessidade de explicitar campos preenchíveis em tempo de
   * execução, este método fará a mesclagem com a lista atual.
   *
   * @param array $columns Lista de colunas a serem marcadas como preenchíveis.
   * @return void
   */
  public function addFillable(array $columns): void
  {
    $this->fillable = array_merge($this->fillable ?? [], $columns);
  }

  /**
   * Remove colunas da lista de protegidas (guarded).
   * 
   * Permite liberar a atribuição em massa temporariamente para colunas
   * que normalmente estariam protegidas.
   *
   * @param array $columns Lista de colunas a terem sua proteção removida.
   * @return void
   */
  public function removeGuarded(array $columns): void
  {
    $this->guarded = array_diff($this->guarded, $columns);
  }
}
