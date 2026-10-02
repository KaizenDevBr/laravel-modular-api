<?php

namespace App\Http\Modules\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BaseJob implements ShouldQueue
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
