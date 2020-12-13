<?php
declare(strict_types=1);
namespace supercrafter333\AllClaimPlotCommand;

use MyPlot\MyPlot;
use MyPlot\subcommand\ClaimSubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class AllClaimPlotCommand extends PluginBase
{

    public function onEnable()
    {
        if ($this->getServer()->getPluginManager()->getPlugin("MyPlot") === null) {
            $this->getLogger()->error("You need the plugin MyPlot to use this plugin!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        if (!$this->getConfig()->exists("version") || $this->getConfig()->get("version") !== $this->getDescription()->getVersion()) {
            $this->getLogger()->error("OUTDATED CONFIG.YML!! Your config.yml is outdated! Please delete the file and restart your server to Update the config.yml!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        $this->getCommand("allclaimplot")->setPermissionMessage($this->getConfig()->get("no-permission-message"));
    }

    public function onCommand(CommandSender $s, Command $cmd, string $label, array $args): bool
    {
        foreach($this->getServer()->getOnlinePlayers() as $player) {
            $levelName = $player->getLevelNonNull()->getFolderName();
            if(!MyPlot::getInstance()->isLevelLoaded($levelName)) {
                $s->sendMessage($this->getConfig()->get("not-in-plot-world-message"));
                continue;
            }
            if(($plot = MyPlot::getInstance()->getNextFreePlot($levelName)) !== null && MyPlot::getInstance()->teleportPlayerToPlot($player, $plot, true)) {
                $cmd = new ClaimSubCommand(MyPlot::getInstance(), "claim");
                $cmd->execute($player, []);
            }
            $player->sendMessage($this->getConfig()->get("broadcast-successful-message"));
        }
        $s->sendMessage($this->getConfig()->get("successful-message"));
        return true;
    }
}
