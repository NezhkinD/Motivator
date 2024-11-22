<?php

namespace App\Command;

use App\Helper\PageHelper;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:count-total-todo-page
#[AsCommand(
    name: 'app:count-total-todo-page',
    description: 'Посчитать total для страницы на текущую дату',
    aliases: ['app:create-todo-page'],
    hidden: false
)]
class CountTotalTodoPageCommand extends Command
{
    protected const DIR_PAGES = __DIR__ . '/../../var/files/';
    protected PageHelper $pageCreator;
    protected DateTime $nowDate;
    protected string $pageDate;
    protected string $pathYearFolder;
    protected string $pathToPage;

    public function __construct(PageHelper $pageCreator)
    {
        $this->pageCreator = $pageCreator;
        $this->nowDate = new DateTime();
        $pageName = $this->nowDate->format('Y-m-d') . ".md";
        $this->pageDate = $this->nowDate->format('Y');
        $this->pathYearFolder = self::DIR_PAGES . $this->pageDate;
        $this->pathToPage = $this->pathYearFolder . "/$pageName";
        parent::__construct('app:create-todo-page');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!is_dir($this->pathYearFolder) && !mkdir($concurrentDirectory = $this->pathYearFolder) && !is_dir($concurrentDirectory)) {
            $output->writeln("Не удалось создать директорию $concurrentDirectory");
            return Command::FAILURE;
        }

        if (is_file($this->pathToPage)) {
            $output->writeln("Файл " . $this->pathToPage . " уже существует");
            return Command::SUCCESS;
        }

        $content = $this->pageCreator->createTodoPageContent($this->nowDate);
        if (!file_put_contents($this->pathToPage, $content)) {
            $output->writeln("не удалось создать файл " . $this->pathToPage);
            return Command::FAILURE;
        }

        $output->writeln("файл успешно создан ". $this->pathToPage);
        return Command::SUCCESS;
    }
}