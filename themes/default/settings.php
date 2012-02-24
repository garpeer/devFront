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
?>
<div class="settings-page">
    <form action="?page=settings" method="post">
        <h2><?php echo $this->locale->settings ?></h2>
        <div>
        <label><?php echo $this->locale->theme ?>:
        <select name="theme">
            <?php if ($this->themes):
                foreach($this->themes as $theme):
                ?>
                    <option value="<?php echo $theme; ?>" <?php if ($this->c_theme == $theme){echo ' selected="selected"';}?>><?php echo $theme; ?></option>
            <?php
                endforeach;
                endif;
            ?>
        </select>
        </label>
        <label><?php echo $this->locale->locale ?>:
            <select name="locale">
            <?php if ($this->locales):
                foreach($this->locales as $locale):
                ?>
                    <option value="<?php echo $locale; ?>" <?php if ($this->c_locale == $locale){echo ' selected="selected"';}?>><?php echo $this->locale->$locale; ?></option>
            <?php
                endforeach;
                endif;
            ?>
        </select>
        </label>
        <input type="hidden" name="type" value="basic" />
        <input type="submit" value="<?php echo $this->locale->save ?>"/>
        </div>
    </form>

    <form action="?page=settings" method="post">
        <h2><?php echo $this->locale->projects ?></h2>
    <?php if ($this->projects): ?>     
        <table>
            <thead>
                <tr>
                    <td><?php echo $this->locale->project_name ?></td>
                    <td><?php echo $this->locale->project_path ?></td>
                    <td><?php echo $this->locale->project_icon ?></td>
                    <td>&#160;</td>
                </tr>
            </thead>
            <tbody>   
            <?php foreach ($this->projects as $id => $project):  ?>
                <tr>
                <?php if ($this->request->type=='projects' && $this->request->action=='edit' && (string)$this->request->id == (string)$id):?>
                    <td>
                        <input type="hidden" name="type" value="projects" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <?php field($project, $this->request, 'name', true) ?>
                    </td>
                    <td><?php field($project, $this->request, 'path', true) ?></td>
                    <td><?php field($project, $this->request, 'icon') ?></td>
                    <td><input type="submit" value="<?php echo $this->locale->save ?>"/></td>
                <?php else: ?>            
                    <td><?php echo isset($project['name']) ? $project['name'] : '' ?></td>
                    <td><?php echo isset($project['path']) ? $project['path'] : '' ?></td>
                    <td><?php echo isset($project['icon']) ? $project['icon'] : ''  ?></td>
                    <td>
                        <a href="?page=settings&amp;type=projects&amp;action=edit&amp;id=<?php echo $id; ?>"><?php echo $this->locale->edit ?></a>
                        <a href="?page=settings&amp;type=projects&amp;action=delete&amp;id=<?php echo $id; ?>"><?php echo $this->locale->delete ?></a>
                    </td>
                <?php endif; ?>
                 </tr>
            <?php endforeach; ?> 
            </tbody>
        </table>
    <?php endif; ?>
    </form>
    
    <form action="?page=settings" method="post">
        <div>
        <h3><?php echo $this->locale->create_project ?></h3>
        <input type="hidden" name="type" value="projects" />
        <input type="hidden" name="action" value="create" />
        <?php field(false, $this->request, 'name', true, $this->locale->project_name); ?>
        <?php field(false, $this->request, 'path', true, $this->locale->project_path); ?>
        <?php field(false, $this->request, 'icon', false, $this->locale->project_icon); ?>
        <input type="submit" value="<?php echo $this->locale->save ?>"/>
        </div>
    </form>

     <form action="?page=settings" method="post">
        <h2><?php echo $this->locale->folders ?></h2>
   <?php if ($this->folders): ?> 
        <table>
            <thead>
                <tr>
                    <td><?php echo $this->locale->folder_name ?></td>
                    <td><?php echo $this->locale->folder_path ?></td>
                    <td><?php echo $this->locale->folder_pattern ?></td>
                    <td>&#160;</td>
                </tr>
            </thead>
            <tbody>       
            <?php foreach ($this->folders as $id => $folder):  ?>
                <tr>
                <?php if ($this->request->type=='folders' && $this->request->action=='edit' && (string)$this->request->id == (string)$id):?>
                    <td>
                        <input type="hidden" name="type" value="folders" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <?php field($folder, $this->request, 'name', true) ?>
                    </td>
                    <td><?php field($folder, $this->request, 'path', true) ?></td>
                    <td><?php field($folder, $this->request, 'pattern') ?></td>
                    <td><input type="submit" value="<?php echo $this->locale->save ?>"/></td>
                <?php else: ?>            
                    <td><?php echo isset($folder['name']) ? $folder['name'] : '' ?></td>
                    <td><?php echo isset($folder['path']) ? $folder['path'] : '' ?></td>
                    <td><?php echo isset($folder['pattern']) ? $folder['pattern'] : ''  ?></td>
                    <td>
                        <a href="?page=settings&amp;type=folders&amp;action=edit&amp;id=<?php echo $id; ?>"><?php echo $this->locale->edit ?></a>
                        <a href="?page=settings&amp;type=folders&amp;action=delete&amp;id=<?php echo $id; ?>"><?php echo $this->locale->delete ?></a>
                    </td>
                <?php endif; ?>
                 </tr>
            <?php endforeach; ?> 
            </tbody>
        </table>
    <?php endif; ?>
    </form>
    
    <form action="?page=settings" method="post">
        <div>
        <h3><?php echo $this->locale->create_folder ?></h3>
        <input type="hidden" name="type" value="folders" />
        <input type="hidden" name="action" value="create" />
        <?php field(false, $this->request, 'name', true, $this->locale->folder_name); ?>
        <?php field(false, $this->request, 'path', true, $this->locale->folder_path); ?>
        <?php field(false, $this->request, 'pattern', false, $this->locale->folder_pattern); ?>
        <input type="submit" value="<?php echo $this->locale->save ?>"/>
        </div>
    </form>
</div>
<?php
    function field($data, $post, $key, $required=false, $label = null){
        $required = $required ? 'required' : '';
        $data[$key] = $post->$key ? $post->key : (isset($data[$key]) ? $data[$key] : '');
        $str = '<input type="text" name="'.$key.'" value="'. htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8') . '" '.$required.'/>';
        if ($label){
            $str = "<label>".$label.": ". $str. "</label>";
        }
        echo $str;
    }