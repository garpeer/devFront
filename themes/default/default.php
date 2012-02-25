<?php
/*
 * devFront localhost frontend
 * Copyright (C) 2012 Gergely Aradszki (garpeer)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */
?><!DOCTYPE html><html xmlns='http://www.w3.org/1999/xhtml' xml:lang='hu-HU' lang="hu-HU">
    <head>
    <meta charset="utf-8" />
    <title><?php echo $this->clean($this->title); ?></title>
    <link rel="shortcut icon" href="<?php echo $this->theme_dir;?>favicon.ico" />
    <link rel='stylesheet' type='text/css' href='<?php echo $this->theme_dir;?>style.css' />
    </head>
    <body>
        <div class="wrapper">
            <?php if ($this->notices): ?>
            <ul class="notices">
                <?php foreach ($this->notices as $notice): ?>
                    <li class="notice-level-<?php echo $notice['level']; ?>"><?php echo $notice['message']; ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php if ($this->request->is_local): ?>
            <a class="settings-link" href="?page=settings"><?php echo $this->locale->settings?></a>
            <?php endif; ?>
            <h1><a href="/"><?php echo $this->clean($this->title); ?></a></h1>
            <?php
            echo $this->content;
            $from = "2012";
            $to = date('Y');
            if ($from == $to){
                $copy = $from;
            }else{
                $copy = $from . "–". $to;
            }
            ?>
            <p class="footer">© <?php echo $copy ?> Garpeer</p>
        </div>
    </body>
</html>

