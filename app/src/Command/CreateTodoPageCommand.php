<?php

namespace App\Command;

use App\Helper\PageHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:create-todo-page
#[AsCommand(
    name: 'app:create-todo-page',
    description: 'Создать страницу с задачами на текущий день',
    aliases: ['app:create-todo-page'],
    hidden: false
)]
class CreateTodoPageCommand extends Command
{
    protected const DIR_PAGES = __DIR__ . '/../../var/files/';
    protected PageHelper $pageCreator;

    public function __construct(PageHelper $pageCreator)
    {
        $this->pageCreator = $pageCreator;
        parent::__construct('app:create-todo-page');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateTime = new \DateTime();
        $pageName = $dateTime->format('Y-m-d') . ".md";
        $year = $dateTime->format('Y');
        $path = self::DIR_PAGES . $year . "/" . $pageName;

        if (!is_dir(self::DIR_PAGES . $year) && !mkdir($concurrentDirectory = self::DIR_PAGES . $year) && !is_dir($concurrentDirectory)) {
            $output->writeln("Не удалось создать директорию $concurrentDirectory");
            return Command::FAILURE;
        }

        if (is_file($path)) {
            $output->writeln("Файл $path уже существует");
            return Command::SUCCESS;
        }

        $content = $this->pageCreator->createTodoPageContent($dateTime);
        if (!file_put_contents($path, $content)) {
            $output->writeln("не удалось создать файл $path");
            return Command::FAILURE;
        }

        $output->writeln("файл успешно создан $path");
        return Command::SUCCESS;
    }
}