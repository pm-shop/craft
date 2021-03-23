<?php
namespace CraftCommand;

use pocketmine\block\Block;
use pocketmine\block\CraftingTable;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\CraftingGrid;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class Craft extends Command{
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $time = Main::getInstance()->db->get($sender->getName());
        $timeNow = time();
        if (empty($time)) {
            $time = 0;
        }
        if ($sender instanceof Player) {
            if ($sender->hasPermission("craft.cmd")) {
                if ($timeNow - $time >= Main::getInstance()->cooldown * 60) {
                    Main::getInstance()->db->set($sender->getName(), time());
                    Main::getInstance()->db->save();
                    $block = Block::get(Block::CRAFTING_TABLE);
                    $block->x = (int)floor($sender->x);
                    $block->y = (int)floor($sender->y) - 2;
                    $block->z = (int)floor($sender->z);
                    $block->level = $sender->getLevel();
                    $block->level->sendBlocks([$sender], [$block]);
                    $sender->setCraftingGrid(new CraftingGrid($sender, CraftingGrid::SIZE_BIG));
                    if (!array_key_exists($windowId = Player::HARDCODED_CRAFTING_GRID_WINDOW_ID, $sender->openHardcodedWindows)) {
                        $pk = new ContainerOpenPacket();
                        $pk->windowId = $windowId;
                        $pk->type = WindowTypes::WORKBENCH;
                        $pk->x = $sender->getFloorX();
                        $pk->y = $sender->getFloorY() - 2;
                        $pk->z = $sender->getFloorZ();
                        $sender->sendDataPacket($pk);
                        $sender->openHardcodedWindows[$windowId] = true;
                        return;
                    } else {
                        $sender->sendPopup("§cVous n'avez pas la permission d'effectuer la commande");
                    }
                } else {
                    $sender->sendPopup("§cVous devez attendre un peu plus");
                }
            } else {
                $sender->sendMessage("§cIMPOSSIBLE D'EFFECTUER LA COMMANDE DEPUIS LA CONSOLE");
            }
        }
    }

}