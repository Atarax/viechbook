<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 22.11.14
 * Time: 19:38
 */
namespace Parvati\Shell;


use Parvati\Model\Neuron;

require 'vendor/autoload.php';

class ParvatiShell {
    public function main() {
		$neuron = new Neuron();
    	echo "foo\n";
    }
}

$shell = new ParvatiShell();
$shell->main();

