<?php

namespace App\Command;

use App\Entity\TodoPageEntity;
use App\Helper\PageHelper;
use App\Helper\ScoreCounter;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:update-todo-page -v
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
        $logger = new ConsoleLogger($output);
        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);

        if (!is_file($this->pathToPage)) {
            $this->warning($logger, "Файл " . $this->pathToPage . " НЕ существует");
            return Command::SUCCESS;
        }

        $tasksContent = file_get_contents($this->pathToPage);
        $currentTodoPageEntity = TodoPageEntity::fromData($rules, explode("\n", $this->getProperties($tasksContent)));
        $todoPageEntity = $this->scoreCounter->countTotal($currentTodoPageEntity, []);

        $content = $todoPageEntity->buildProperties() . $todoPageEntity->buildScoreInfo();
        file_put_contents($this->pathToPage, $content);

        $this->notice($logger, "Файл успешно обновлен " . $this->pathToPage);
        return Command::SUCCESS;
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

    protected function warning(ConsoleLogger $logger, string $message): void
    {
        $logger->warning(self::getDataForLog() . ", " . "$message | Ждем " . self::SLEEP_IN_SEC . " сек");
        sleep(self::SLEEP_IN_SEC);
    }

    protected function notice(ConsoleLogger $logger, string $message): void
    {
        $logger->notice(self::getDataForLog() . ", " . "$message | Ждем " . self::SLEEP_IN_SEC . " сек");
        sleep(self::SLEEP_IN_SEC);
    }

    protected function error(ConsoleLogger $logger, string $message): void
    {
        $logger->error(self::getDataForLog() . ", " . "$message | Ждем " . self::SLEEP_IN_SEC . " сек");
        sleep(self::SLEEP_IN_SEC);
    }

    protected static function getDataForLog(): string
    {
        return (new DateTime())->format('Y-m-dTH-i-s');
    }
}