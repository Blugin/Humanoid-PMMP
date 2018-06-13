<?php

namespace kim\presenthumanoid\command\subcommands;

use pocketmine\command\CommandSender;
use kim\presenthumanoid\Humanoid as Plugin;
use kim\presenthumanoid\command\{
  SubCommand, PoolCommand, SimpleSubCommand
};
use kim\presenthumanoid\command\subcommands\simple\{
  ShowListCommand, SetNameCommand, SetRotationCommand, SetItemCommand, SetArmorCommand, SetSkinCommand, SetCapeCommand, SetGeometryCommand, SetSneakCommand, SetPositionCommand, SetScaleCommand
};

class SetSubCommand extends SubCommand{

    /** @var SimpleSubCommand[] */
    protected static $subCommands = [];

    /** @return SimpleSubCommand[] */
    public static function getSubCommands() : array{
        return self::$subCommands;
    }

    /** @param SimpleSubCommand[] $subCommands */
    public static function setSubCommands(array $subCommands) : void{
        self::$subCommands = $subCommands;
    }

    /** @param SimpleSubCommand $subCommand */
    public static function addSubCommand(SimpleSubCommand $subCommand) : void{
        self::$subCommands[] = $subCommand;
    }

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'set');
        self::addSubCommand(new ShowListCommand());
        self::addSubCommand(new SetNameCommand());
        self::addSubCommand(new SetRotationCommand());
        self::addSubCommand(new SetItemCommand());
        self::addSubCommand(new SetArmorCommand());
        self::addSubCommand(new SetSkinCommand());
        self::addSubCommand(new SetCapeCommand());
        self::addSubCommand(new SetGeometryCommand());
        self::addSubCommand(new SetSneakCommand());
        self::addSubCommand(new SetPositionCommand());
        self::addSubCommand(new SetScaleCommand());
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[0])) {
            $label = array_shift($args);
            foreach (self::$subCommands as $key => $value) {
                if ($value->checkLabel($label)) {
                    if ($value->onCommand($sender, $args)) {
                        $sender->sendMessage(Plugin::$prefix . $this->translate('success'));
                    }
                    return true;
                }
            }
            $sender->sendMessage(Plugin::$prefix . $this->translate('failure', $label));
            return true;
        }
        return false;
    }
}