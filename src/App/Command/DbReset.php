<?php
/**
 * Created by PhpStorm.
 * User: joey.rivera
 * Date: 9/29/15
 * Time: 9:47 PM
 */

namespace App\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DbReset
 * @package Cgaa\Console\Command
 */
class DbReset extends Command
{
	/**
	 * sets up the db reset job
	 */
	protected function configure()
	{
		$this->setName("db:reset")
			->setDescription("Rebuilds the DB and populates with default data.")
			->addOption("regenerate", "r", InputOption::VALUE_NONE, "Rebuild entities.");
	}

	/**
	 * Takes care of recreating the DB
	 *
	 * Takes in an optional parameter of -r or --regenerate. If passed, will recreate all entities
	 * as well.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return boolean
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$doctrineBin = "vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "doctrine";
		$sqlSchemaFile = "db" . DIRECTORY_SEPARATOR . "schema.sql";
		$sqlDataFile = "db" . DIRECTORY_SEPARATOR . "sample.sql";
		
		$output->writeln("Resetting Schema");
		$doctrineOutput = shell_exec("{$doctrineBin} dbal:import {$sqlSchemaFile}");
		$output->write($doctrineOutput);

		$output->writeln("Adding sample data");
		$doctrineOutput = shell_exec("{$doctrineBin} dbal:import {$sqlDataFile}");
		$output->write($doctrineOutput);

		$doctrineOutput = shell_exec("{$doctrineBin} orm:clear-cache:metadata");
		$output->write($doctrineOutput);

		$doctrineOutput = shell_exec("{$doctrineBin} orm:clear-cache:query");
		$output->write($doctrineOutput);

		$doctrineOutput = shell_exec("{$doctrineBin} orm:clear-cache:result");
		$output->write($doctrineOutput);

		if (!$input->getOption('regenerate')) {
			return;
		}

		$doctrineOutput = shell_exec("{$doctrineBin} orm:convert-mapping --namespace='App\Entity\' --force --from-database annotation src/");
		$output->write($doctrineOutput);

		$doctrineOutput = shell_exec("{$doctrineBin} orm:generate-entities --generate-annotations=true --generate-methods=true --regenerate-entities=true src/");
		$output->write($doctrineOutput);

		$doctrineOutput = shell_exec("{$doctrineBin} orm:generate-proxies src/App/Entity/Proxy/");
		$output->write($doctrineOutput);

		// generate repositories but need to add annotations first
	}
}
