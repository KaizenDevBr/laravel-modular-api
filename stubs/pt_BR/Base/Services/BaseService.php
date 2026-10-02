<?php

namespace App\Http\Modules\Base\Services;

use App\Http\Modules\Base\Repositories\Interfaces\BaseRepositoryInterface;
use App\Http\Modules\Base\Services\Interfaces\BaseServiceInterface;

class BaseService implements BaseServiceInterface
{
  private BaseRepositoryInterface $baseRepository;

  /**
   * Construtor desta classe de serviço.
   *
   * @param BaseRepositoryInterface $baseRepository - Instância do repositório.
   */
  public function __construct(BaseRepositoryInterface $baseRepository)
  {
    $this->baseRepository = $baseRepository;
  }

  /**
   * Recupera todos os registros da tabela com base em parâmetros informados.
   *
   * @param array $select - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function all(array $select)
  {
    return $this->baseRepository->all($select);
  }

  /**
   * Lista registros com base em filtros opcionais.
   *
   * @param array $filters - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function index(array $filters): mixed
  {
    // Delegando a lógica de busca para o repositório, passando os filtros como array.
    return $this->baseRepository->index($filters);
  }

  /**
   * Retorna dados completos de um único registro de acordo com o seu ID fornecido.
   *
   * @param int $id - ID do registro.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function show(int $id): mixed
  {
    // Delegando a lógica de busca para o repositório.
    return $this->baseRepository->show($id);
  }

  /**
   * Cria um novo registro.
   *
   * @param array $data - Dados necessários para a criação do registro.
   * @return mixed - Retorna o registro criado ou uma resposta do repositório.
   */
  public function store(array $data): mixed
  {
    // Delegando a criação para o repositório
    return $this->baseRepository->store($data);
  }

  /**
   * Atualiza um registro com base nos dados fornecidos.
   *
   * @param array $data - Dados para atualização.
   * @param int $id - ID do registro.
   * @return mixed - Retorna o registro atualizado ou uma resposta do repositório.
   */
  public function update(array $data, int $id): mixed
  {
    // Delegando a atualização para o repositório
    return $this->baseRepository->update($data, $id);
  }

  /**
   * Exclui um registro com base no ID fornecido.
   *
   * @param int $id - ID do registro a ser excluído.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int $id): bool
  {
    return $this->baseRepository->destroy($id);
  }
}
