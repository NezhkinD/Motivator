<?php

namespace App\Command;

use App\Entity\TodoPageEntity;
use App\Helper\PageHelper;
use App\Helper\ScoreCounter;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:update-todo-page
#[AsCommand(
    name: 'app:update-todo-page',
    description: 'Посчитать total для страницы на текущую дату',
    aliases: ['app:update-todo-page'],
    hidden: false
)]
class UpdateTodoPageCommand extends Command
{
    protected const int SLEEP_IN_SEC = 300;
    protected const string DIR_PAGES = __DIR__ . '/../../var/files/';
    protected const string DIR_ALL_RULES = __DIR__ . "/../../config/allRules.json";
    protected PageHelper $pageHelper;
    protected DateTime $nowDate;
    protected string $pageDate;
    protected string $pathYearFolder;
    protected string $pathToPage;
    protected ScoreCounter $scoreCounter;

    public function __construct(PageHelper $pageCreator, ScoreCounter $scoreCounter)
    {
        $this->pageHelper = $pageCreator;
        $this->nowDate = new DateTime();
        $pageName = $this->nowDate->format('Y-m-d') . ".md";
        $this->pageDate = $this->nowDate->format('Y');
        $this->pathYearFolder = self::DIR_PAGES . $this->pageDate;
        $this->pathToPage = $this->pathYearFolder . "/$pageName";
        $this->scoreCounter = $scoreCounter;
        parent::__construct('app:create-todo-page');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);

        if (!is_file($this->pathToPage)) {
            $output->writeln("Файл " . $this->pathToPage . " НЕ существует");
            return Command::SUCCESS;
        }

        $tasksContent = file_get_contents($this->pathToPage);
        $currentTodoPageEntity = TodoPageEntity::fromData($rules, explode("\n", $this->getProperties($tasksContent)));
        $todoPageEntity = $this->scoreCounter->countTotal($currentTodoPageEntity, []);

        $content = $todoPageEntity->buildProperties() . $todoPageEntity->buildScoreInfo();
        file_put_contents($this->pathToPage, $content);

        $output->writeln("файл успешно обновлен " . $this->pathToPage);
        $this->showProgressBar($output, self::SLEEP_IN_SEC);
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

    protected function getProperties(string $text): string
    {
        $pattern = '/---\s*(.*?)\s*---/s';
        preg_match($pattern, $text, $matches);

        if (!empty($matches[1])) {
            return $matches[1];
        }

        return "";
    }
}