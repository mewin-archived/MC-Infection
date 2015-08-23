<?php
/*
 * Copyright (C) 2015 mewin <mewin@mewin.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace mewin;

use ManiaControl\ManiaControl;
use ManiaControl\Plugins\Plugin;
use ManiaControl\Commands\CommandListener;
use ManiaControl\Callbacks\CallbackListener;
use ManiaControl\Admin\AuthenticationManager;
use ManiaControl\Players\Player;

class Infection implements CallbackListener, Plugin, CommandListener
{
    const ID = 64;
    const VERSION = "0.1";
    const SETTING_PERMISSION_INFECT = "Infect";
    const CB_INFECT = "Inf_Infect";
    
    private $maniaControl;
    
    public static function getDescription()
    {
        return "Adds functionallity for the Infection game mode.";
    }

    public static function getAuthor()
    {
        return "mewin";
    }

    public static function getId()
    {
        return self::ID;
    }

    public static function getName()
    {
        return "Infection";
    }

    public static function getVersion()
    {
        return self::VERSION;
    }
    
    public function load(ManiaControl $maniaControl)
    {
        $this->maniaControl = $maniaControl;
        $this->maniaControl->commandManager->registerCommandListener('infect', $this, 'command_Infect', true, 'Infect a player');
        $this->maniaControl->authenticationManager->definePermissionLevel(self::SETTING_PERMISSION_INFECT, AuthenticationManager::AUTH_LEVEL_ADMIN);
    }

    public function unload()
    {
        $this->maniaControl = null;
    }

    public static function prepare(ManiaControl $maniaControl)
    {
        
    }
    
    public function command_Infect(array $chatCallback, Player $player)
    {
        if (!$this->maniaControl->authenticationManager->checkPermission($player, self::SETTING_PERMISSION_INFECT))
        {
            $this->maniaControl->authenticationManager->sendNotAllowed($player);
            return;
        }
        
        $cmd = explode(' ', $chatCallback[1][2]);
        if (empty($cmd[1]))
        {
            $this->maniaControl->chat->sendUsageInfo("Usage example: '//infect login'", $player->login);
        }
        else
        {
            $target = $this->maniaControl->playerManager->getPlayer($cmd[1]);
            if (!$target)
            {
                $this->maniaControl->chat->sendError("Player '{$cmd[1]}' not found!", $player->login);
                return;
            }
            
            $this->maniaControl->client->triggerModeScriptEvent(self::CB_INFECT, $target->login);
            $this->maniaControl->chat->sendInformation('$<' . $player->nickname . '$> forced $<' . $target->nickname . '$> to the infecteds.');
        }
    }
}
