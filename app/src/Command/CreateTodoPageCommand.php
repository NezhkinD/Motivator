<?php

namespace App\Command;

use App\Helper\PageHelper;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
    protected const int SLEEP_IN_SEC = 600;
    protected const string DIR_PAGES = __DIR__ . '/../../var/files/';
    protected const string DIR_ALL_RULES = __DIR__ . "/../../config/allRules.json";
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
            $this->showProgressBar($output, self::SLEEP_IN_SEC);
            return Command::FAILURE;
        }

        if (is_file($this->pathToPage)) {
            $output->writeln("Файл " . $this->pathToPage . " уже существует");
            $this->showProgressBar($output, self::SLEEP_IN_SEC);
            return Command::SUCCESS;
        }

        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);
        $content = $this->pageCreator->createNewTodoPageContent($this->nowDate, $rules);
        if (!file_put_contents($this->pathToPage, $content)) {
            $output->writeln("не удалось создать файл " . $this->pathToPage);
            $this->showProgressBar($output, self::SLEEP_IN_SEC);
            return Command::FAILURE;
        }

        $output->writeln("файл успешно создан " . $this->pathToPage);
        return Command::SUCCESS;
    }

    protected function showProgressBar(OutputInterface $output, int $units): void
    {
        $output->writeln("Ждем $units сек.");
        $progressBar = new ProgressBar($output, $units);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        $i = 0;
        while ($i++ < $units) {
            $progressBar->advance();
            sleep(1);
        }
        $progressBar->finish();
        $output->writeln("");
        $output->writeln("Завершаем работу");
    }
}