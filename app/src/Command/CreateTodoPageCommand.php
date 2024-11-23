<?php

namespace App\Command;

use App\Helper\PageHelper;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:create-todo-page -v
#[AsCommand(
    name: 'app:create-todo-page',
    description: 'Создать страницу с задачами на текущий день',
    aliases: ['app:create-todo-page'],
    hidden: false
)]
class CreateTodoPageCommand extends Command
{
    protected const int SLEEP_IN_SEC = 3600;
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
        $logger = new ConsoleLogger($output);
        if (!is_dir($this->pathYearFolder) && !mkdir($concurrentDirectory = $this->pathYearFolder) && !is_dir($concurrentDirectory)) {
            $this->error($logger, "Не удалось создать директорию $concurrentDirectory");
            return Command::FAILURE;
        }

        if (is_file($this->pathToPage)) {
            $this->warning($logger, "Файл " . $this->pathToPage . " уже существует");
            return Command::SUCCESS;
        }

        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);
        $content = $this->pageCreator->createNewTodoPageContent($this->nowDate, $rules);
        if (!file_put_contents($this->pathToPage, $content)) {
            $this->error($logger, "Не удалось создать файл " . $this->pathToPage);
            return Command::FAILURE;
        }

        $this->notice($logger, "файл успешно создан " . $this->pathToPage);
        return Command::SUCCESS;
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
        $dateTime = new DateTime();
        $logger->error(self::getDataForLog() . ", " . "$message | Ждем " . self::SLEEP_IN_SEC . " сек");
        sleep(self::SLEEP_IN_SEC);
    }
    protected static function getDataForLog(): string
    {
        return (new DateTime())->format('Y-m-dTH-i-s');
    }
}