<?php

namespace App\Http\Modules\Base\Providers;

use App\Http\Modules\Base\Repositories\BaseRepositoryEloquent;
use App\Http\Modules\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Este é um provedor de serviços responsável por 
 * registrar a ligação entre a interface "BaseRepositoryInterface" e 
 * a implementação "BaseRepositoryEloquent" no container de injeção de dependência do Laravel.
 */
class BaseRepositoryProvider extends ServiceProvider
{

  /**
   * Registra os serviços ou bindings no container de serviços do Laravel.
   *
   * Este método é responsável por vincular a interface 
   * "BaseRepositoryInterface" à sua implementação concreta, 
   * "BaseRepositoryEloquent", permitindo que o Laravel 
   * resolva automaticamente a dependência quando necessário.
   *
   * @return void
   */
  public function register(): void
  {
    $this->app->bind(
      BaseRepositoryInterface::class, // A interface a ser resolvida
      BaseRepositoryEloquent::class // A implementação concreta
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
