<?php

namespace KaizenDev\ModularApi\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
  // Nome do comando Artisan
  protected $signature = 'make:module {name} {--table=}';

  // Descrição do comando
  protected $description = 'Cria a estrutura padrão de um módulo dentro de app/Http/Modules';

  /**
   * Executando o comando
   */
  public function handle()
  {
    // Obtendo o nome do módulo e padronizando a capitalização
    $name = ucfirst($this->argument('name'));
    $basePath = app_path("Http/Modules/{$name}");

    // Verificando se o módulo já existe
    if (File::exists($basePath)) {
      if (!$this->confirm("O módulo '{$name}' já existe. Deseja sobrescrevê-lo?", false)) {
        $this->info("🚫  Operação cancelada.");
        $this->info("");
        return;
      }

      // Removendo o módulo existente antes de recriá-lo
      File::deleteDirectory($basePath);
      $this->info("🗑️  Módulo '{$name}' removido.");
      $this->info("");
    }

    // Obtendo o nome da tabela (ou fallback padrao para validators se nulo)
    $tableName = $this->option('table');

    // Se a tabela não foi informada via parâmetro, interage com o usuário no terminal
    if (empty($tableName) && $this->confirm("Este módulo deverá ser atrelado a uma tabela? (yes/no)", true)) {
      $tableName = $this->ask("Qual o nome da tabela?");
    }

    $tableFallback = $tableName ? $tableName : 'nome_da_tabela_aqui';

    // Estrutura de diretórios e arquivos com conteúdo base
    $structure = [
      'Controllers' => [
        "{$name}Controller.php" => $this->getControllerTemplate($name, $tableFallback),
      ],
      'Models' => [
        "{$name}.php" => $this->getModelTemplate($name, $tableFallback),
      ],
      'Providers' => [
        "{$name}RepositoryProvider.php" => $this->getRepositoryProviderTemplate($name),
        "{$name}ServiceProvider.php" => $this->getServiceProviderTemplate($name),
      ],
      'Repositories' => [
        "{$name}RepositoryEloquent.php" => $this->getRepositoryTemplate($name),
        'Interfaces' => [
          "{$name}RepositoryInterface.php" => $this->getRepositoryInterfaceTemplate($name),
        ],
      ],
      'Requests' => [
        "IndexRequest.php" => $this->getRequestTemplate($name, 'Index'),
        "StoreRequest.php" => $this->getRequestTemplate($name, 'Store'),
        "UpdateRequest.php" => $this->getRequestTemplate($name, 'Update'),
      ],
      'Services' => [
        "{$name}Service.php" => $this->getServiceTemplate($name),
        'Interfaces' => [
          "{$name}ServiceInterface.php" => $this->getServiceInterfaceTemplate($name),
        ],
      ],
      'Traits' => [
        "{$name}Trait.php" => $this->getTraitTemplate($name),
      ],
      'Resources' => [
        "{$name}Resource.php" => $this->getResourceTemplate($name),
      ],
      'Jobs' => [
        "{$name}Job.php" => $this->getJobTemplate($name),
      ],
      'Migrations' => $tableName ? [
        date('Y_m_d_His') . '_create_' . $tableName . '_table.php' => $this->getMigrationTemplate($name, $tableName),
      ] : [],
      'Seeders' => [
        "{$name}Seeder.php" => $this->getSeederTemplate($name),
      ],
      'Factories' => [
        "{$name}Factory.php" => $this->getFactoryTemplate($name),
      ],
      'Exports' => [
        "{$name}Export.php" => $this->getExportTemplate($name),
      ],
    ];

    // Criando a pasta do módulo com permissão 0777
    if (!File::exists($basePath)) {
      File::makeDirectory($basePath, 0777, true);
      chmod($basePath, 0777);
      $this->info("📂 Criado diretório base do módulo: {$basePath} (permissão 0777)");
    }

    // Criando a estrutura de pastas e arquivos
    foreach ($structure as $folder => $files) {
      $path = "{$basePath}/{$folder}";

      // Criando a pasta principal com permissão 0777
      if (!File::exists($path)) {
        File::makeDirectory($path, 0777, true);
        chmod($path, 0777);
        $this->info("📂 Criado: {$path} (permissão 0777)");
      }

      if (is_array($files)) {
        foreach ($files as $subfolder => $content) {
          // Se for um array associativo, significa que é uma subpasta
          if (is_array($content)) {
            $subfolderPath = "{$path}/{$subfolder}";

            // Criando a subpasta Interfaces dentro de Services e Repositories
            if (!File::exists($subfolderPath)) {
              File::makeDirectory($subfolderPath, 0777, true);
              chmod($subfolderPath, 0777);
              $this->info("📂 Criado subdiretório: {$subfolderPath} (permissão 0777)");
            }

            // Criando arquivos dentro da subpasta
            foreach ($content as $file => $fileContent) {
              $filePath = "{$subfolderPath}/{$file}";

              if (!File::exists($filePath)) {
                File::put($filePath, $fileContent ?? "// Arquivo {$file}");
                chmod($filePath, 0777);
                $this->info("📄 Criado: {$filePath} (permissão 0777)");
              }
            }
          } else {
            // Criando arquivos na pasta principal (não em subpastas)
            $filePath = "{$path}/{$subfolder}";

            if (!File::exists($filePath)) {
              File::put($filePath, $content ?? "// Arquivo {$subfolder}");
              chmod($filePath, 0777);
              $this->info("📄 Criado: {$filePath} (permissão 0777)");
            }
          }
        }
      }
    }



    $this->info("");
    $this->info("🎉🎉🎉 Módulo '{$name}' criado com sucesso! 🎉🎉🎉");
    $this->info("");
  }

  /**
   * REQUESTS
   */
  protected function getRequestTemplate($name, $type)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$type}Request extends FormRequest
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
    ";
  }

  /**
   * CONTROLLER
   */
  protected function getControllerTemplate($name, $tableName)
  {
    $camelCaseName = lcfirst($name);

    return "<?php

namespace App\Http\Modules\\{$name}\\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\Base\Controllers\BaseController;
use App\Http\Modules\\{$name}\\Requests\\IndexRequest;
use App\Http\Modules\\{$name}\\Requests\\StoreRequest;
use App\Http\Modules\\{$name}\\Requests\\UpdateRequest;
use App\Http\Modules\\{$name}\\Services\Interfaces\\{$name}ServiceInterface;

class {$name}Controller extends BaseController
{
  private {$name}ServiceInterface \${$camelCaseName}Service;

  /**
   * Construtor da controller para injeção de dependências.
   *
   * @param {$name}ServiceInterface \${$camelCaseName}Service
   */
  public function __construct({$name}ServiceInterface \${$camelCaseName}Service)
  {
    \$this->{$camelCaseName}Service = \${$camelCaseName}Service;
  }

  /**
   * Retorna uma lista de registros com base nos filtros fornecidos.
   *
   * Este método recebe uma requisição, aplica os filtros e opções fornecidas e retorna a lista de registros encontrados.
   * Caso nenhum registro seja encontrado, retorna uma resposta de erro.
   *
   * @param \$request - Dados da requisição contendo os filtros e opções para a listagem.
   * @return JsonResponse - Resposta JSON contendo os registros encontrados ou uma mensagem de erro.
   */
  public function all(\$request): JsonResponse
  {
    \$result = \$this->{$camelCaseName}Service->all(\$request);

    if (\$result):
      return \$this->sendError('Nenhum registro encontrado.');
    endif;

    return \$this->sendResponse('success', 'Registros recuperados com sucesso!', \$result);
  }

  /**
   * Retorna uma lista de registros com base nos filtros fornecidos.
   *
   * Este método recebe uma requisição, aplica os filtros fornecidos e retorna a lista de registros encontrados.
   * Caso nenhum registro seja encontrado retorna uma resposta de erro.
   *
   * @param {$name}IndexRequest \$request - Requisição contendo os filtros para a listagem.
   * @return JsonResponse - Resposta JSON contendo os registros encontrados ou uma mensagem de erro.
   */
  public function index(IndexRequest \$request): JsonResponse
  {
    \$result = \$this->{$camelCaseName}Service->index(\$request);

    if (\$result->isEmpty()):
      return \$this->sendError('Nenhum registro encontrado.');
    endif;

    return \$this->sendResponse('success', 'Registros recuperados com sucesso!', \$result);
  }

  /**
   * Exibe os detalhes de um registro específico.
   *
   * @param int \$id - Identificador único do registro.
   * @return JsonResponse - Retorna os detalhes do registro ou uma mensagem de erro caso não seja encontrado.
   */
  public function show(\$id): JsonResponse
  {
    // Validando o ID manualmente
    \$validator = Validator::make(
      ['id' => \$id], // Dados à serem validados
      [
        'id' => ['required', 'integer'], // Regras de validação
      ]
    );

    if (\$validator->fails()) {
      return \$this->sendError('O ID do registro não foi informado ou é inválido.');
    }

    // Se o ID passar na validação, o serviço é chamado para buscar as informações do registro.
    \${$camelCaseName} = \$this->{$camelCaseName}Service->show(\$id);

    if (!\${$camelCaseName}) {
      return \$this->sendError('Registro não encontrado.');
    }

    return \$this->sendResponse('success', 'Registro recuperado com sucesso!', \${$camelCaseName});
  }

  /**
   * Cria um novo registro.
   *
   * @param StoreRequest \$request - Objeto de requisição validado.
   * @return JsonResponse - Retorna uma mensagem de sucesso.
   */
  public function store(StoreRequest \$request): JsonResponse
  {
    return !\$this->{$camelCaseName}Service->store(\$request->validated())
      ? \$this->sendError('Registro não cadastrado.')
      : \$this->sendResponse('success', 'Cadastro realizado com sucesso!');
  }

  /**
   * Atualiza um registro existente.
   *
   * @param UpdateRequest \$request - Objeto de requisição validado.
   * @param int \$id - ID do registro.
   * @return JsonResponse - Retorna o registro atualizado ou uma mensagem de erro.
   */
  public function update(UpdateRequest \$request, int \$id): JsonResponse
  {
    \${$camelCaseName} = \$this->{$camelCaseName}Service->update(\$request->validated(), \$id);

    if (!\${$camelCaseName}) {
      return response()->json(['message' => 'Registro não encontrado.'], 404);
    }

    return \$this->sendResponse('success', 'Registro atualizado com sucesso!', \${$camelCaseName});
  }

  /**
   * Remove um registro pelo ID.
   *
   * @param int \$id - ID do registro.
   * @return JsonResponse - Retorna uma mensagem de sucesso ou erro.
   */
  public function destroy(\$id): JsonResponse
  {
    // Validando o tipo do ID (inteiro) e se ele existe no banco de dados.
    \$validator = Validator::make(
      ['id' => \$id], // ID vindo da URL
      ['id' => 'required|integer|exists:{$tableName},id'] // Regras de validação para garantir que id é um inteiro e existe
    );

    if (\$validator->fails()) {
      return \$this->sendError('Registro não encontrado ou o ID informado não é válido.');
    }

    // Se passou na validação, tenta excluir
    \$deleted = \$this->{$camelCaseName}Service->destroy(\$id);

    if (!\$deleted) {
      return \$this->sendError('Registro não encontrado.');
    }

    return \$this->sendResponse('success', 'Registro excluído com sucesso!');
  }
}
    ";
  }

  /**
   * SERVICE
   */
  protected function getServiceTemplate($name)
  {
    $camelCaseName = lcfirst($name);

    return "<?php

namespace App\Http\Modules\\{$name}\\Services;

use App\Http\Modules\\{$name}\\Repositories\Interfaces\\{$name}RepositoryInterface;
use App\Http\Modules\\{$name}\\Services\Interfaces\\{$name}ServiceInterface;

class {$name}Service implements {$name}ServiceInterface
{
  private {$name}RepositoryInterface \${$camelCaseName}Repository;

  /**
   * Construtor desta classe de serviço.
   *
   * @param {$name}RepositoryInterface \${$camelCaseName}Repository - Instância do repositório.
   */
  public function __construct({$name}RepositoryInterface \${$camelCaseName}Repository)
  {
    \$this->{$camelCaseName}Repository = \${$camelCaseName}Repository;
  }

  /**
   * Recupera todos os registros da tabela com base em parâmetros informados.
   *
   * @param \$request - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function all(\$request)
  {
    return \$this->{$camelCaseName}Repository->all(\$request);
  }

  /**
   * Lista registros com base em filtros opcionais.
   *
   * @param \$request - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function index(\$request): mixed
  {
    // Delegando a lógica de busca para o repositório.
    return \$this->{$camelCaseName}Repository->index(\$request);
  }

  /**
   * Retorna dados completos de um único registro de acordo com o seu ID fornecido.
   *
   * @param int \$id - ID do registro.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function show(int \$id): mixed
  {
    // Delegando a lógica de busca para o repositório.
    return \$this->{$camelCaseName}Repository->show(\$id);
  }

  /**
   * Cria um novo registro.
   *
   * @param array \$data - Dados necessários para a criação do registro.
   * @return mixed - Retorna o registro criado ou uma resposta do repositório.
   */
  public function store(array \$data): mixed
  {
    // Delegando a criação para o repositório
    return \$this->{$camelCaseName}Repository->store(\$data);
  }

  /**
   * Atualiza um registro com base nos dados fornecidos.
   *
   * @param array \$data - Dados para atualização.
   * @param int \$id - ID do registro.
   * @return mixed - Retorna o registro atualizado ou uma resposta do repositório.
   */
  public function update(array \$data, int \$id): mixed
  {
    // Delegando a atualização para o repositório
    return \$this->{$camelCaseName}Repository->update(\$data, \$id);
  }

  /**
   * Exclui um registro com base no ID fornecido.
   *
   * @param int \$id - ID do registro a ser excluído.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int \$id): bool
  {
    return \$this->{$camelCaseName}Repository->destroy(\$id);
  }
}
    ";
  }

  /**
   * SERVICE (INTERFACE)
   */
  protected function getServiceInterfaceTemplate($name)
  {
    $camelCaseName = lcfirst($name);

    return "<?php

namespace App\Http\Modules\\{$name}\\Services\Interfaces;

interface {$name}ServiceInterface
{
  /**
   * Recupera todos os registros da tabela com base em parâmetros informados.
   *
   * @param \$request - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção de registros ou um resultado do repositório.
   */
  public function all(\$request);
  
  /**
   * Lista registros com base em critérios de pesquisa opcionais.
   *
   * @param \$request - Parâmetros filtrados da requisição.
   * @return mixed - Retorna uma coleção de registros ou um resultado do repositório.
   */
  public function index(\$request): mixed;

  /**
   * Retorna dados completos de um único registro de acordo com o seu ID fornecido.
   *
   * @param int \$id - ID do registro.
   * @return mixed - Retorna uma coleção ou resultados do repositório.
   */
  public function show(int \$id): mixed;

  /**
   * Cria um novo registro.
   *
   * @param array \$data - Dados necessários para criar o registro.
   * @return mixed - Retorna o registro criado.
   */
  public function store(array \$data): mixed;

  /**
   * Atualiza um registro com os dados fornecidos.
   *
   * @param array \$data - Dados para atualização.
   * @param int \$id - ID do registro a ser atualizado.
   * @return mixed - Retorna o registro atualizado ou uma resposta do repositório.
   */
  public function update(array \$data, int \$id): mixed;

  /**
   * Exclui um registro com base no ID fornecido.
   *
   * @param int \$id - ID do registro a ser excluído.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int \$id): bool;
}
    ";
  }

  /**
   * SERVICE (PROVIDER)
   */
  protected function getServiceProviderTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Providers;

use App\Http\Modules\\{$name}\\Services\\{$name}Service;
use App\Http\Modules\\{$name}\\Services\Interfaces\\{$name}ServiceInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Este é um provedor de serviços responsável por 
 * registrar a ligação entre a interface \"{$name}ServiceInterface\" e 
 * a implementação \"{$name}Service\" no container de injeção de dependência do Laravel.
 */
class {$name}ServiceProvider extends ServiceProvider
{

  /**
   * Registra os serviços ou bindings no container de serviços do Laravel.
   *
   * Este método é responsável por vincular a interface 
   * \"{$name}ServiceInterface\" à sua implementação concreta, 
   * \"{$name}Service\", permitindo que o Laravel resolva automaticamente 
   * a dependência quando necessário.
   *
   * @return void
   */
  public function register(): void
  {
    \$this->app->bind(
      {$name}ServiceInterface::class, // A interface a ser resolvida
      {$name}Service::class // A implementação concreta
    );
  }

  /**
   * Inicializa os serviços após o registro.
   *
   * Este é um método utilizado para realizar ações após o registro dos serviços. 
   * Neste caso, ele não contém nenhuma lógica, mas pode ser estendido no futuro 
   * caso seja necessário realizar alguma ação adicional durante o boot da aplicação.
   *
   * @return void
   */
  public function boot(): void
  {
    //
  }
}
    ";
  }

  /**
   * REPOSITORY
   */
  protected function getRepositoryTemplate($name)
  {
    $camelCaseName = lcfirst($name);

    return "<?php

namespace App\Http\Modules\\{$name}\\Repositories;

use App\Http\Modules\Base\Traits\BaseTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Modules\\{$name}\\Models\\{$name};
use App\Http\Modules\\{$name}\\Repositories\Interfaces\\{$name}RepositoryInterface;

class {$name}RepositoryEloquent implements {$name}RepositoryInterface
{
  use BaseTrait;
  protected {$name} \$model;

  /**
   * Construtor do repositório.
   *
   * @param {$name} \${$camelCaseName} - Instância do modelo.
   */
  public function __construct({$name} \${$camelCaseName})
  {
    \$this->model = \${$camelCaseName};
  }

  /**
   * Lista os registros com base nas colunas selecionadas.
   *
   * @param \$request - Colunas para selecionar na consulta.
   * @return mixed - Retorna os resultados da consulta.
   */
  public function all(\$request)
  {
    \$query = \$this->model;

    // Transformando em array os parâmetros passados na requisição para exibição dinâmica da resposta 
    \$select = \$request->query('select', []);
    \$where_col = \$request->query('where_col', []);
    \$where_val = \$request->query('where_val', []);
    \$order_col = \$request->query('order_col');
    \$order_direction = \$request->query('order_direction');

    // SELECT
    \$query = !empty(\$select)
      ? \$query->select(\$select)
      : \$query->select('*');

    // WHERE
    if (!empty(\$where_col) && !empty(\$where_val) && count(\$where_col) === count(\$where_val)):
      foreach (array_combine(\$where_col, \$where_val) as \$col => \$val):
        \$query->where(\$col, \$val);
      endforeach;
    endif;

    // ORDER COLUMN
    \$order_col = !empty(\$order_col)
      ? \$order_col
      : 'id';

    // ORDER DIRECTION
    \$order_direction = !empty(\$order_direction)
      ? \$order_direction
      : 'ASC';

    return
      \$query
      ->orderBy(\$order_col, \$order_direction);
  }

  /**
   * Lista registros com base em filtros de pesquisa opcionais.
   *
   * @param \$request - Filtros opcionais para a busca.
   * @return LengthAwarePaginator - Retorna um paginador contendo os registros encontrados com base nos filtros informados.
   */
  public function index(\$request): LengthAwarePaginator
  {
    \$query = \$this->model;

    // Transformando em array os parâmetros passados na requisição para exibição dinâmica da resposta 
    \$select = \$request->query('select', []);
    \$where_col = \$request->query('where_col', []);
    \$where_val = \$request->query('where_val', []);
    \$like_col = \$request->query('like_col', []);
    \$like_val = \$request->query('like_val', []);
    \$where_rel = \$request->query('where_rel', []);
    \$where_rel_col = \$request->query('where_rel_col', []);
    \$where_rel_val = \$request->query('where_rel_val', []);
    \$with = \$request->query('with', []); // Relacionamentos
    \$order_col = \$request->query('order_col');
    \$order_direction = \$request->query('order_direction');
    \$limit = \$request->query('limit');

    // Lista de relacionamentos permitidos para evitar carregamento indevido
    \$allowedRelationships = [];

    // SELECT
    \$query = !empty(\$select)
      ? \$query->select(\$select)
      : \$query->select('*');

    // WITH
    if (!empty(\$with)) {
      // Filtra apenas os relacionamentos permitidos
      \$with = array_intersect((array) \$with, \$allowedRelationships);
      \$query->with(\$with);
    }

    // WHERE (na tabela principal) - igualdade exata
    if (!empty(\$where_col) && !empty(\$where_val) && count(\$where_col) === count(\$where_val)):
      foreach (array_combine(\$where_col, \$where_val) as \$col => \$val):
        
        // Verifica se o valor é uma string que se parece com um array (inicia com [ e termina com ])
        // Ex: \"[1,3,4,5]\" vindo da URL
        \$isJsonArray = is_string(\$val) && str_starts_with(\$val, '[') && str_ends_with(\$val, ']');
        
        if (\$isJsonArray) {
            // Remove os colchetes e transforma em array real do PHP
            // Removemos '[' e ']' e explodimos pela vírgula
            \$cleanVal = str_replace(['[', ']'], '', \$val);
            \$valuesArray = explode(',', \$cleanVal);
            
            // Aplica whereIn
            \$query->whereIn(\$col, \$valuesArray);
        } else {
            // Comportamento padrão (igualdade exata)
            \$query->where(\$col, \$val);
        }

      endforeach;
    endif;

    // LIKE (na tabela principal) - busca parcial (case-insensitive depende do collation do DB)
    if (!empty(\$like_col) && !empty(\$like_val) && count(\$like_col) === count(\$like_val)):
      foreach (array_combine(\$like_col, \$like_val) as \$col => \$val):
        // usa binding para evitar SQL injection. Aceita colunas qualificadas (table.col) ou simples.
        // Quando a coluna vier qualificada (ex: \"perfis.nome\") mantemos como está.
        // OBS: a sensibilidade a maiúsculas/minúsculas depende do collation do seu banco.
        \$query->whereRaw(\"{\$col} LIKE ?\", [\"%{\$val}%\"]);
      endforeach;
    endif;

    // WHERE (nos relacionamentos) - QUALIFICANDO colunas para evitar ambiguidades
    if (
      !empty(\$where_rel) && !empty(\$where_rel_col) && !empty(\$where_rel_val)
      && count(\$where_rel) === count(\$where_rel_col) && count(\$where_rel_col) === count(\$where_rel_val)
    ) {
      foreach (\$where_rel as \$index => \$relation) {
        \$col = \$where_rel_col[\$index] ?? null;
        \$val = \$where_rel_val[\$index] ?? null;

        if (\$relation && \$col && isset(\$val)) {
          // 1) Se a coluna já vier qualificada (ex: \"permissoes.id\" ou \"perfil_permissao.permissao_id\"), usa como está.
          if (strpos(\$col, '.') !== false) {
            \$qualifiedCol = \$col;
          } else {
            // 2) tenta descobrir a tabela do modelo relacionado via a relação do Eloquent
            \$relatedTable = \$relation; // fallback: nome da relação
            try {
              // Verifica se existe método de relação no modelo
              if (method_exists(\$this->model, \$relation)) {
                \$relationObj = \$this->model->{\$relation}();
                if (method_exists(\$relationObj, 'getRelated')) {
                  \$relatedModel = \$relationObj->getRelated();
                  if (method_exists(\$relatedModel, 'getTable')) {
                    \$relatedTable = \$relatedModel->getTable();
                  }
                }
              }
            } catch (\Throwable \$e) {
              // fallback: mantém \$relatedTable como o nome da relação
            }

            // agora qualifica a coluna com o nome da tabela deduzido
            \$qualifiedCol = \"{\$relatedTable}.{\$col}\";
          }

          // Usa whereHas com whereRaw qualificado (binding)
          \$query->whereHas(\$relation, function (\$subQuery) use (\$qualifiedCol, \$val) {
            \$subQuery->whereRaw(\"{\$qualifiedCol} = ?\", [\$val]);
          });

          // Se o relacionamento estiver no \"with\", aplica o mesmo filtro no carregamento para não trazer itens não correspondentes
          if (in_array(\$relation, (array) \$with, true)) {
            \$query->with([
              \$relation => function (\$subQuery) use (\$qualifiedCol, \$val) {
                \$subQuery->whereRaw(\"{\$qualifiedCol} = ?\", [\$val]);
              }
            ]);
          }
        }
      }
    }

    // ORDER COLUMN
    \$order_col = !empty(\$order_col)
      ? \$order_col
      : 'id';

    // ORDER DIRECTION
    \$order_direction = !empty(\$order_direction)
      ? \$order_direction
      : 'ASC';

    // LIMIT
    \$limit = !empty(\$limit)
      ? \$limit
      : 10;

    return
      \$query
      ->orderBy(\$order_col, \$order_direction)
      ->paginate(\$limit); // Retorna os registros com paginação.
  }

  /**
   * Busca um registro pelo ID.
   *
   * @param int \$id - Identificador único do registro.
   * @return {$name}|null - Retorna o registro correspondente ao ID informado ou null se não encontrado.
   */
  public function show(int \$id): ?{$name}
  {
    return \$this->model->find(\$id);
  }

  /**
   * Cria um novo registro.
   *
   * @param array \$data - Dados necessários para criar o registro.
   * @return {$name}|null - Retorna o registro criado ou null em caso de falha.
   */
  public function store(array \$data): ?{$name}
  {
    try {
      // Cria uma nova instância do modelo e preenche com os dados fornecidos.
      \${$camelCaseName} = \$this->model->create(\$data);

      return \${$camelCaseName}; // Retorna o modelo criado.

    } catch (\Exception \$e) {
      return null; // Retorna null se o registro não for cadastrado.
    }
  }

  /**
   * Atualiza um registro existente.
   *
   * @param array \$data - Dados para atualização do registro.
   * @param int \$id - ID do registro.
   * @return {$name}|null - Retorna o registro atualizado ou null se não for encontrado.
   */
  public function update(array \$data, int \$id): ?{$name}
  {
    // Busca o registro pelo ID.
    \${$camelCaseName} = \$this->model->find(\$id);

    if (!\${$camelCaseName}) {
      return null; // Retorna null se o registro não for encontrado.
    }

    // Atualiza os dados e salva.
    \${$camelCaseName}->fill(\$data);
    \${$camelCaseName}->save();

    return \${$camelCaseName}; // Retorna o registro atualizado.
  }

  /**
   * Remove um registro pelo ID.
   *
   * @param int \$id - ID do registro a ser removido.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int \$id): bool
  {
    \${$camelCaseName} = {$name}::find(\$id);

    if (!\${$camelCaseName}) {
      return false;
    }

    return \${$camelCaseName}->delete();
  }
}
    ";
  }

  /**
   * REPOSYTORY (INTERFACE)
   */
  protected function getRepositoryInterfaceTemplate($name)
  {
    $camelCaseName = lcfirst($name);

    return "<?php

namespace App\Http\Modules\\{$name}\\Repositories\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Modules\\{$name}\\Models\\{$name};

interface {$name}RepositoryInterface
{
  /**
   * Lista os registros com base nas colunas selecionadas.
   *
   * @param \$request - Parâmetros filtrados da requisição.
   * @return mixed - Retorna os resultados da consulta.
   */
  public function all(\$request);

  /**
   * Lista registros com base em filtros de pesquisa opcionais.
   *
   * @param \$request - Parâmetros filtrados da requisição.
   * @return Collection - Retorna uma coleção de registros encontrados.
   */
  public function index(\$request): LengthAwarePaginator;

  /**
   * Busca um registro pelo ID.
   *
   * @param int \$id - Identificador único do registro.
   * @return {$name}|null - Retorna o registro correspondente ao ID informado ou null se não encontrado.
   */
  public function show(int \$id): ?{$name};

  /**
   * Cria um novo registro.
   *
   * @param array \$data - Dados necessários para criar o registro.
   * @return {$name}|null - Retorna o registro criado ou null em caso de falha.
   */
  public function store(array \$data): ?{$name};

  /**
   * Atualiza um registro existente.
   *
   * @param array \$data - Dados para atualização do registro.
   * @param int \$id - ID do registro.
   * @return {$name}|null - Retorna o registro atualizado ou null se não for encontrado.
   */
  public function update(array \$data, int \$id): ?{$name};

  /**
   * Remove um registro pelo ID.
   *
   * @param int \$id - ID do registro a ser removido.
   * @return bool - Retorna true se a exclusão for bem-sucedida, false caso contrário.
   */
  public function destroy(int \$id): bool;
}
    ";
  }

  /**
   * REPOSITORY (PROVIDER)
   */
  protected function getRepositoryProviderTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Providers;

use App\Http\Modules\\{$name}\\Repositories\\{$name}RepositoryEloquent;
use App\Http\Modules\\{$name}\\Repositories\Interfaces\\{$name}RepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Este é um provedor de serviços responsável por 
 * registrar a ligação entre a interface \"{$name}RepositoryInterface\" e 
 * a implementação \"{$name}RepositoryEloquent\" no container de injeção de dependência do Laravel.
 */
class {$name}RepositoryProvider extends ServiceProvider
{

  /**
   * Registra os serviços ou bindings no container de serviços do Laravel.
   *
   * Este método é responsável por vincular a interface 
   * \"{$name}RepositoryInterface\" à sua implementação concreta, 
   * \"{$name}RepositoryEloquent\", permitindo que o Laravel 
   * resolva automaticamente a dependência quando necessário.
   *
   * @return void
   */
  public function register(): void
  {
    \$this->app->bind(
      {$name}RepositoryInterface::class, // A interface a ser resolvida
      {$name}RepositoryEloquent::class // A implementação concreta
    );
  }

  /**
   * Inicializa os serviços após o registro.
   *
   * Este é um método utilizado para realizar ações após o registro dos serviços. 
   * Neste caso, ele não contém nenhuma lógica, mas pode ser estendido no futuro 
   * caso seja necessário realizar alguma ação adicional durante o boot da aplicação.
   *
   * @return void
   */
  public function boot(): void
  {
    //
  }
}
    ";
  }

  /**
   * MODEL
   */
  protected function getModelTemplate($name, $tableName)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Models;

use App\Http\Modules\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class {$name} extends BaseModel
{
  use SoftDeletes;

  /**
   * Nome da tabela associada ao modelo.
   *
   * @var string
   */
  protected \$table = '{$tableName}';

  /**
   * Converte automaticamente os atributos informados para seus tipos nativos definidos aqui.
   *
   * @var array
   */
  protected \$casts = [];
}
    ";
  }

  /**
   * TRAIT
   */
  protected function getTraitTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Traits;

trait {$name}Trait
{
    //
}
    ";
  }

  /**
   * RESOURCE
   */
  protected function getResourceTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class {$name}Resource extends JsonResource
{
  //
}
    ";
  }

  /**
   * JOB
   */
  protected function getJobTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {$name}Job implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Construtor do job.
   * Este exemplo não usa nenhum parâmetro específico.
   */
  public function __construct()
  {
    //
  }

  /**
   * A lógica que será executada quando o job for processado.
   *
   * @return void
   */
  public function handle()
  {
    // Lógica do job
  }
}
    ";
  }

  /**
   * SEEDER
   */
  protected function getSeederTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Modules\\{$name}\Models\\{$name};

class {$name}Seeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Exemplo de como usar a factory (descomente para usar)
    // {$name}::factory()->count(10)->create();
  }
}
    ";
  }

  /**
   * FACTORY
   */
  protected function getFactoryTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Http\Modules\\{$name}\Models\\{$name};

class {$name}Factory extends Factory
{
  /**
   * O nome do model correspondente a esta factory.
   *
   * @var string
   */
  protected \$model = {$name}::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      // 'column_name' => \$this->faker->word(),
    ];
  }
}
    ";
  }

  /**
   * EXPORT
   */
  protected function getExportTemplate($name)
  {
    return "<?php

namespace App\Http\Modules\\{$name}\Exports;

use App\Http\Modules\\{$name}\Models\\{$name};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class {$name}Export implements FromCollection, WithHeadings, WithMapping
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return {$name}::all();
  }

  /**
   * @return array
   */
  public function headings(): array
  {
    return [
      'ID',
      'Data de Criação',
      'Data de Atualização',
    ];
  }

  /**
   * @param mixed \$row
   *
   * @return array
   */
  public function map(\$row): array
  {
    return [
      \$row->id,
      \$row->created_at ? \$row->created_at->format('d/m/Y H:i:s') : '',
      \$row->updated_at ? \$row->updated_at->format('d/m/Y H:i:s') : '',
    ];
  }
}
    ";
  }

  /**
   * MIGRATION
   */
  protected function getMigrationTemplate($name, $tableName)
  {
    return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            // \$table->string('nome');
            \$table->timestamp('created_at')->nullable(false)->useCurrent();
            \$table->timestamp('updated_at')->nullable(false)->useCurrent()->useCurrentOnUpdate();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
    ";
  }
}
