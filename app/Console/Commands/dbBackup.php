<?php

namespace App\Console\Commands;

use Artisan;
use Carbon\Carbon;
use App\Mail\DbBackups;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class dbBackup extends Command
{	/**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'db:backup';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Creates db backup -schema and the data';

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct() {
           parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {
       $ds = DIRECTORY_SEPARATOR;
       $host = env('DB_HOST');
       $username = env('DB_USERNAME');
       $password = env('DB_PASSWORD');
       $database = env('BACKUP_DB');
       $path = database_path('backups' . $ds);
       $file = 'bd' . date('_Y-m-d') . '.sql';
       if (!is_dir($path)) {
               mkdir($path, 0755, true);
       }
       $this->line('<fg=cyan>Backup: </><fg=yellow;bg=black>'. $path . $file . '</>');
       # Generamos el comando con mysqldump para exportar los datos
    //    $command = sprintf(
    //        'C:\xampp\mysql\bin\mysqldump --skip-comments --skip-compact --no-create-info'
    //        . ' --skip-triggers --complete-insert --skip-add-locks'
    //        . ' --disable-keys --lock-tables --host="%s" --user="%s" '
    //        , $host, $username
    //        );

       $command = sprintf(
           'mysqldump --skip-comments --skip-compact --no-create-info'
           . ' --skip-triggers --complete-insert --skip-add-locks'
           . ' --disable-keys --lock-tables --host="%s" --user="%s" '
           , $host, $username
           );

    //    $command = sprintf(
    //        'C:\laragon\bin\mysql\bin\mysqldump --skip-comments --skip-compact --no-create-info'
    //        . ' --skip-triggers --complete-insert --skip-add-locks'
    //        . ' --disable-keys --lock-tables --host="%s" --user="%s" '
    //        , $host, $username
    //        );
       if (!empty($password)) {
           $command .= sprintf('--password="%s" ', $password);
       }
       $command .= sprintf('%s > "%s"', $database, $path . $file);
       $this->line('<fg=green>CMD: </><fg=yellow;bg=black>'. $command . '</>');
       exec($command, $output, $return);

       if ($return) {
           $this->line('<fg=red;bg=yellow>Error al intentar generar el Backup</>');
           if (file_exists($path . $ds . $file)) {
               unlink($path . $ds . $file);
           }
           return; // error
       }
       
       $fileCompress = gzopen ($path . $ds . $file . '.gz', 'w9');
       // Compress the file
       gzwrite ($fileCompress, file_get_contents($path . $ds . $file));
       // Close the gz file and we are done
       gzclose($fileCompress);

       $email_path = $path . $ds . $file . '.gz';
       // Generando el esquema
       $path = database_path('backups' . $ds . 'schemas'. $ds);
       $file = 'schema.sql';
       if (!is_dir($path)) {
           mkdir($path, 0755, true);
       }
       # Generamos el comando con mysqldump para exportar la estructura
       $command = sprintf(
           'C:\xampp\mysql\bin\mysqldump --skip-comments --skip-compact '
           . ' --no-data --host="%s" --user="%s" '
           , $host, $username
           );
       if (!empty($password)) {
           $command .= sprintf('--password="%s" ', $password);
       }
       $command .= sprintf(
           '%s | sed "s/ AUTO_INCREMENT=[0-9]*//g"  > "%s"',
           $database, $path . $file
       );
       $this->line('<fg=magenta>Generando Schema</>');
       exec($command, $output, $return);

       $when = Carbon::now();

        if(env('BACKUP_EMAIL')){
            \Mail::to(env('BACKUP_EMAIL'))->later($when->addMinutes(1),new DbBackups($email_path));
         }
       

       if ($return) {
           $this->line('<fg=red;bg=yellow>Error al intentar generar el Schema</>');
           if (file_exists($path . $ds . $file)) {

               unlink($path . $ds . $file);
           }
           return; // error
       }
   }
}
