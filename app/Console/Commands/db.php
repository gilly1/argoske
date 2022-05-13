<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Dotenv\Dotenv;

/**
 * Codigo inspirado en:
 * - https://gist.github.com/phillipsharring/fb500556e7173a1c222d
 *
 * Mayor información de los commandos personalizados:
 * - https://laravel.com/docs/5.8/artisan#generating-commands
 *
 *
 *
 * vendor/laravel/framework/src/Illuminate/Support/helpers.php:643
 **/
class DbDump extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'db:dump';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Respalda la bd del host, si host es nula genera el respaldo de todas la BDs existentes en el sistema';

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
		$database = env('DB_DATABASE');
		$path = database_path('backups' . $ds);
		$file = 'bd' . date('_Y-m-d') . '.sql';
		if (!is_dir($path)) {
				mkdir($path, 0755, true);
		}
		$this->line('<fg=cyan>Backup: </><fg=yellow;bg=black>'. $path . $file . '</>');
		# Generamos el comando con mysqldump para exportar los datos
		$command = sprintf(
			'C:\xampp\mysql\bin\mysqldump --skip-comments --skip-compact --no-create-info'
			. ' --skip-triggers --complete-insert --skip-add-locks'
			. ' --disable-keys --lock-tables --host="%s" --user="%s" '
			, $host, $username
			);
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
		// Comprimiendo el archivo:
		// mayor info: https://www.php.net/manual/es/function.gzopen.php
		// Open the gz file (w9 is the highest compression)
		$fileCompress = gzopen ($path . $ds . $file . '.gz', 'w9');
		// Compress the file
		gzwrite ($fileCompress, file_get_contents($path . $ds . $file));
		// Close the gz file and we are done
		gzclose($fileCompress);
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
		if ($return) {
			$this->line('<fg=red;bg=yellow>Error al intentar generar el Schema</>');
			if (file_exists($path . $ds . $file)) {
				unlink($path . $ds . $file);
			}
			return; // error
		}
	}
}