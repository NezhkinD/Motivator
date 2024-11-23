<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

// -q (quiet): Показывает только критические ошибки.
// -v (verbose): Показывает стандартные сообщения и предупреждения.
// -vv (very verbose): Показывает информационные сообщения.
// -vvv (debug): Показывает все сообщения, включая отладочные.
// php bin/console app:test -v
class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Создаём ConsoleLogger
        $logger = new ConsoleLogger($output);

        // Логирование сообщений разных уровней
        $logger->debug('Debug message');
        $logger->info('Info message');
        $logger->notice('Notice message');
        $logger->warning('Warning message');
        $logger->error('Error message');
        $logger->critical('Critical message');
        $logger->alert('Alert message');
        $logger->emergency('Emergency message');

        return Command::SUCCESS;
    }
}