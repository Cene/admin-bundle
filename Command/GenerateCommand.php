<?php

namespace Mesalab\Bundle\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Symfony\Component\Console\Input\ArrayInput;
use Sensio\Bundle\GeneratorBundle\Generator\BundleGenerator;

class GenerateCommand extends ContainerAwareCommand
{
	protected $replacements = array();

    protected function configure()
    {
        $this
            ->setName('admin:generate')
            ->setDescription('Generate Admin base structure')
            ->addArgument('bundle_name', InputArgument::REQUIRED, 'Write the bundle name')
            ->addArgument('entity', InputArgument::REQUIRED, 'Write the entity name')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Se impostato, urlerà in lettere maiuscole')
        ;
    }

	// admin:generate Acme/Bundle/DemoBundle Product
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		/*
		$namespace = 'Acme/Bundle/NewsBundle';
		$bundle = 'AcmeNewsBundle';
		$dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src';
		$format = 'yml';
		$structure = 'no';

		$command = $this->getApplication()->find('generate:bundle');
		$arguments = array(
			'command' => 'generate:bundle',
			'--namespace'    => 'Acme/Bundle/NewsBundle',
			'--bundle-name'    => 'AcmeNewsBundle',
			'--dir'    => dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src',
			'--format'    => 'yml',
			'--structure'    => 'no',
		);
		$input = new ArrayInput($arguments);
		$returnCode = $command->run($input, $output);
		if($returnCode != 0) {
			$output->writeln($returnCode);
		}
		else{
			$output->writeln($returnCode);
		}
		*/




		$output->writeln('Admin generation');
		$srcDir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src';
		$vendorDir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/vendor';

		$bundleDir = $input->getArgument('bundle_name'); // Acme/DemoBundle | Acme/Bundle/DemoBundle
		$entity = $input->getArgument('entity'); // Product
		$bundleArray = explode('/', $bundleDir); // Array(Acme,DemoBundle) | Array(Acme,Bundle,DemoBundle)
		if($bundleArray[1]=='Bundle'){ unset($bundleArray[1]); $bundleArray = array_values($bundleArray); } // remove 'Bundle' from namespace

		// data used to create files
		$namespace = str_replace('/', '\\', $bundleDir); // Acme\DemoBundle | Acme\Bundle\DemoBundle
		$bundle = str_replace('/', '', $bundleArray[0] . $bundleArray[1]); // AcmeDemoBundle | AcmeDemoBundle
		$bundleName = str_replace('Bundle', '', str_replace('Bundle', '', $bundle)); // AcmeDemo

		// data used to create file contents
		$this->setReplacement('|namespace|', $namespace); // Acme\DemoBundle | Acme\Bundle\DemoBundle
		$this->setReplacement('|entity|', $entity); // Product
		$this->setReplacement('|bundle|', $bundle); // AcmeDemoBundle | AcmeDemoBundle
		$this->setReplacement('|bundle_name|', $bundleName); // AcmeDemo
		$this->setReplacement('|bundle_name_underscored|', ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $bundleArray[1])), '_')); // acme_demo
		$this->setReplacement('|route_name|', strtolower('admin_' . $entity)); // admin_product
		$this->setReplacement('|route_path|', strtolower('/admin/' . $entity)); // /admin/product

		// source and destination target dirs
		$source = $vendorDir . '/mesalab/admin-bundle/Mesalab/Bundle/AdminBundle/Resources/skeleton/';
		$target = $srcDir . '/' . $bundleDir . '/';

		// check if needed files exist
		if(
			file_exists($target . 'Controller/' . $entity . 'Controller.php') ||
			file_exists($target . 'Entity/' . $entity . '.php') ||
			file_exists($target . 'Form/Type/' . $entity . 'Type.php') ||
			file_exists($target . 'Resources/views/' . $entity)
		){
			$output->writeln('This class already exists');
			$output->writeln('Exiting');
		}
		else{

			$skeleton = array(
				array('source' => $source . 'Bundle.php', 'target' => $target . $bundle . '.php', 'overwrite' => false, 'append' => false), // bootstrap
				array('source' => $source . 'Controller/Controller.php', 'target' => $target . 'Controller/' . $entity . 'Controller.php', 'overwrite' => false, 'append' => false), // controller
				array('source' => $source . 'DependencyInjection/Extension.php', 'target' => $target . 'DependencyInjection/' . $bundleName . 'Extension.php', 'overwrite' => false, 'append' => false), // dependency injection
				array('source' => $source . 'DependencyInjection/Configuration.php', 'target' => $target . 'DependencyInjection/Configuration.php', 'overwrite' => false, 'append' => false), // dependency injection
				array('source' => $source . 'Entity/Entity.php', 'target' => $target . 'Entity/' . $entity . '.php', 'overwrite' => false, 'append' => false), // entity
				array('source' => $source . 'Form/Type/Type.php', 'target' => $target . 'Form/Type/' . $entity . 'Type.php', 'overwrite' => false, 'append' => false), // form
				array('source' => $source . 'Resources/config/bundle_parameters.yml', 'target' => $target . 'Resources/config/bundle_parameters.yml', 'overwrite' => true, 'append' => true), // Config
				array('source' => $source . 'Resources/config/routing.yml', 'target' => $target . 'Resources/config/routing.yml', 'overwrite' => true, 'append' => true), // Config
				array('source' => $source . 'Resources/config/services.yml', 'target' => $target . 'Resources/config/services.yml', 'overwrite' => true, 'append' => true), // Config
				array('source' => $source . 'Resources/config/validation.yml', 'target' => $target . 'Resources/config/validation.yml', 'overwrite' => true, 'append' => true), // Config
				array('source' => $source . 'Resources/views/index.html.twig', 'target' => $target . 'Resources/views/' . $entity . '/index.html.twig', 'overwrite' => false, 'append' => false), // Views
				array('source' => $source . 'Resources/views/new.html.twig', 'target' => $target . 'Resources/views/' . $entity . '/new.html.twig', 'overwrite' => false, 'append' => false), // Views
				array('source' => $source . 'Resources/views/paged.html.twig', 'target' => $target . 'Resources/views/' . $entity . '/paged.html.twig', 'overwrite' => false, 'append' => false), // Views
				array('source' => $source . 'Resources/views/sort.html.twig', 'target' => $target . 'Resources/views/' . $entity . '/sort.html.twig', 'overwrite' => false, 'append' => false), // Views
			);

			// save file only if it doesn't exist or it must be overwritten
			foreach($skeleton as $file){
				if(!file_exists($file['target']) || $file['overwrite']){
					$output->writeln($this->renderFile($file));
				}
				else{
					$output->writeln('File exists. Skipping ' . $file['target']);
				}
			}
			$output->writeln('File created');

			// Add the bundle in AppKernel.php
			$output->writeln('Enabling the bundle inside the Kernel');
			$manip = new KernelManipulator($this->getContainer()->get('kernel'));
			try {
				$manip->addBundle($namespace.'\\'.$bundle);
				$output->writeln('Bundle enabled');
			} catch (\RuntimeException $e) {
				$output->writeln('Bundle <comment>%s</comment> is already defined in <comment>AppKernel::registerBundles()</comment>');
			}


			// Import the routing into routing.yml
			$output->writeln('Importing the bundle routing resource');
			$routing = new RoutingManipulator($this->getContainer()->getParameter('kernel.root_dir').'/config/routing.yml');
			try {
				$routing->addResource($bundle, 'yml');
				$output->writeln('Bundle imported');
			} catch (\RuntimeException $e) {
				$output->writeln('Bundle <comment>%s</comment> is already imported.');
			}

			$output->writeln('Bundle generation completed');
			$output->writeln('');
			$output->writeln('Run doctrine:generate:entities ' . $bundleDir . '/Entity/' . $entity . ' to update schema');
			$output->writeln('and Run doctrine:schema:update --force to update schema');


			/*
			$output->writeln('Generating Entity');
			$command = $this->getApplication()->find('doctrine:generate:entities');
			$arguments = array(
				'command' => 'doctrine:generate:entities',
				'name'    => $bundleDir . '/Entity/' . $entity,
			);
			$input = new ArrayInput($arguments);
			$returnCode = $command->run($input, $output);
			if($returnCode != 0) {
				$output->writeln('Entity generated');


				// sleep before creating entity and table schema
				sleep (5);

				$output->writeln('Generating Database Schema');
				$command = $this->getApplication()->find('doctrine:schema:update');
				$arguments = array(
					'command' => 'doctrine:schema:update',
					'--force'    => true,
				);
				$input = new ArrayInput($arguments);
				$returnCode = $command->run($input, $output);
				if($returnCode != 0) {
					$output->writeln('Database Schema generated');
				}

			}
			*/

		}






		/*foreach (array('namespace') as $option) {
			if (null === $input->getOption($option)) {
				throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
			}
		}*/



		//

		//$dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton';

		//$this->renderFile()

/*
        if ($name) {
            $text = 'Ciao '.$name;
        } else {
            $text = 'Ciao';
        }
*/


		/*
		if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }
		*/

        //$output->writeln($text);
    }

	protected function setReplacement($search, $replace)
	{
		$this->replacements[$search] = $replace;
	}

	protected function renderFile($file)
	{
		if (!is_dir(dirname($file['target']))) {
			mkdir(dirname($file['target']), 0777, true);
		}

		$generatedSource = file_get_contents($file['source']);
		foreach($this->replacements as $search => $replace){
			$generatedSource = str_replace($search, $replace, $generatedSource);
		}

		if($file['append']){
			$content = file_put_contents($file['target'], $generatedSource, FILE_APPEND);
			if($content === false){
				$result = 'Error adding content to ' . $file['target'];
			}
			else{
				$result = 'adding content to ' . $file['target'];
			}
		}
		else{
			$content = file_put_contents($file['target'], $generatedSource);
			if($content === false){
				$result = 'Error creating ' . $file['target'];
			}
			else{
				$result = 'creating ' . $file['target'];
			}
		}
		return $result;
	}
}