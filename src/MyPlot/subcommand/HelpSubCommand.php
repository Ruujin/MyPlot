<?php
namespace MyPlot\subcommand;

use MyPlot\MyPlot;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class HelpSubCommand extends SubCommand
{
    public function canUse(CommandSender $sender) {
        return $sender->hasPermission("myplot.command.help");
    }

    public function execute(CommandSender $sender, array $args) {
        if (count($args) === 0) {
            $pageNumber = 1;
        } elseif (is_numeric($args[0])) {
            $pageNumber = (int) array_shift($args);
            if ($pageNumber <= 0) {
                $pageNumber = 1;
            }
        } else {
            return false;
        }

        if ($sender instanceof ConsoleCommandSender) {
            $pageHeight = PHP_INT_MAX;
        } else {
            $pageHeight = 5;
        }

        $commands = [];
        foreach (MyPlot::getInstance()->getDescription()->getCommands() as $command) {
            if($command instanceof Command);
            foreach($sender->getEffectivePermissions() as $permission) {
                if($command->getPermission() === $permission->getName()) {
                    $commands[$command->getName()] = $command;
                }
            }
        }
        ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
        $commands = array_chunk($commands, $pageHeight);
        /** @var SubCommand[][] $commands */
        $pageNumber = (int) min(count($commands), $pageNumber);

        $sender->sendMessage($this->translateString("help.header", [$pageNumber, count($commands)]));
        foreach ($commands[$pageNumber - 1] as $command) {
            $sender->sendMessage(TextFormat::DARK_GREEN . $command->getName() . ": " . TextFormat::WHITE . $command->getDescription());
        }
        return true;
    }
}
