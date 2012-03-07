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
        <div class="clearfix">
        <input type="hidden" name="type" value="basic" />
            <div class="block">
                <label><?php echo $this->locale->theme ?>
                <select name="theme">
                    <?php if ($this->themes):
                        foreach($this->themes as $theme):
                        ?>
                            <option value="<?php echo $this->clean( $theme ); ?>" <?php if ($this->config->theme == $theme){echo ' selected="selected"';}?>><?php echo $this->clean( $theme ); ?></option>
                    <?php
                        endforeach;
                        endif;
                    ?>
                </select>
                </label>
            </div>
            <div class="block">
                <label><?php echo $this->locale->locale ?>
                    <select name="locale">
                    <?php if ($this->locales):
                        foreach($this->locales as $locale):
                        ?>
                            <option value="<?php echo $this->clean( $locale ); ?>" <?php if ($this->config->locale == $locale){echo ' selected="selected"';}?>><?php echo $this->clean( $this->locale->$locale ); ?></option>
                    <?php
                        endforeach;
                        endif;
                    ?>
                </select>
                </label>
            </div>
            <div class="block">
                <label><?php echo $this->locale->allow_ips ?>
                    <input type="text" name="allow_ip" value="<?php echo $this->clean(implode(', ',$this->config->allow_ip)) ?>" />  
                    <?php echo tooltip($this->locale->tooltip_allow_ips); ?> 
                </label>
            </div>
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
            <?php $count = count($this->projects); ?>
            <?php foreach ($this->projects as $id => $project):  ?>
                <tr>
                <?php if ($this->request->type=='projects' && $this->request->action=='edit' && (string)$this->request->id == (string)$id):?>
                    <td>
                        <input type="hidden" name="type" value="projects" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <?php field($project, $this->request, 'name', true, null, $this->locale->tooltip_project_name) ?>
                    </td>
                    <td><?php field($project, $this->request, 'path', true, null, $this->locale->tooltip_project_path) ?></td>
                    <td>
                        <select name="icon">
                        <option value=""><?php echo $this->locale->default_image; ?></option>
                            <?php if ($this->images):
                                foreach($this->images as $image):
                                ?>
                                    <option value="<?php echo $this->clean( $image ); ?>" <?php if ($project['icon'] == $image){echo ' selected="selected"';}?>><?php echo $this->clean( $image ); ?></option>
                                <?php
                                endforeach;
                            endif;
                        ?>
                    </select>        
                    </td>
                    <td class="controls">
                        <input type="submit" value="<?php echo $this->locale->save ?>"/>                        
                        <a href="?page=settings"><?php echo $this->locale->cancel ?></a>
                    </td>
                <?php else: ?>
                    <td><?php echo $this->clean( isset($project['name']) ? $project['name'] : '') ?></td>
                    <td><?php echo $this->clean( isset($project['formatted_path']) ? $project['formatted_path'] : '') ?></td>
                    <td><?php echo $this->clean( isset($project['icon']) ? $project['icon'] : '')  ?></td>
                    <td class="controls">
                        <?php if ($id > 0): ?>
                        <a href="?page=settings&amp;type=projects&amp;action=promote&amp;id=<?php echo (int)$id; ?>" class="action-promote"><?php echo $this->locale->promote ?></a>
                        <?php else: ?>
                        <span class="action-promote action-inactive"><?php echo $this->locale->promote ?></span>
                        <?php endif; ?>
                        <?php if ( ( $id + 1 ) < $count ): ?>
                        <a href="?page=settings&amp;type=projects&amp;action=demote&amp;id=<?php echo (int)$id; ?>" class="action-demote"><?php echo $this->locale->demote ?></a>
                        <?php else: ?>
                        <span class="action-demote action-inactive"><?php echo $this->locale->demote ?></span>
                        <?php endif; ?>
                        
                        <a href="?page=settings&amp;type=projects&amp;action=edit&amp;id=<?php echo (int)$id; ?>" class="action-edit"><?php echo $this->locale->edit ?></a>
                        <a href="?page=settings&amp;type=projects&amp;action=delete&amp;id=<?php echo (int)$id; ?>" class="action-delete"><?php echo $this->locale->delete ?></a>
                    </td>
                <?php endif; ?>
                 </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </form>

    <form action="?page=settings" method="post">
        <div class="clearfix">
        <h3><?php echo $this->locale->create_project ?></h3>
        <input type="hidden" name="type" value="projects" />
        <input type="hidden" name="action" value="create" />        
            <div class="block">
                <?php field(false, $this->request, 'name', true, $this->locale->project_name, $this->locale->tooltip_project_name); ?>
            </div>    
            <div class="block">
                <?php field(false, $this->request, 'path', true, $this->locale->project_path, $this->locale->tooltip_project_path); ?>
            </div>   
            <div class="block">
                <label><?php echo $this->locale->project_icon; ?>
                    <select name="icon">
                        <option value=""><?php echo $this->locale->default_image; ?></option>
                            <?php if ($this->images):
                                foreach($this->images as $image):
                                ?>
                                    <option value="<?php echo $this->clean( $image ); ?>" <?php if ($this->request->icon == $image){echo ' selected="selected"';}?>><?php echo $this->clean( $image ); ?></option>
                                <?php
                                endforeach;
                            endif;
                        ?>
                    </select>
                </label>
                <?php //field(false, $this->request, 'icon', false, $this->locale->project_icon); ?>
            </div>
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
                    <td><?php echo $this->locale->folder_exclude ?></td>
                    <td>&#160;</td>
                </tr>
            </thead>
            <tbody>
            <?php $count = count($this->folders); ?>
            <?php foreach ($this->folders as $id => $folder):  ?>
                <tr>
                <?php if ($this->request->type=='folders' && $this->request->action=='edit' && (string)$this->request->id == (string)$id):?>
                    <td>
                        <input type="hidden" name="type" value="folders" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <?php field($folder, $this->request, 'name', true) ?>
                    </td>
                    <td><?php field($folder, $this->request, 'path', true, null, $this->locale->tooltip_folder_path) ?></td>
                    <td><?php field($folder, $this->request, 'pattern', false, null, $this->locale->tooltip_folder_pattern) ?></td>
                    <td><?php field($folder, $this->request, 'exclude', false, null, $this->locale->tooltip_folder_exclude) ?></td>
                    <td class="controls">
                        <input type="submit" value="<?php echo $this->locale->save ?>"/>
                        <a href="?page=settings"><?php echo $this->locale->cancel ?></a>
                    </td>
                <?php else: ?>
                    <td><?php echo $this->clean( isset($folder['name']) ? $folder['name'] : '') ?></td>
                    <td><?php echo $this->clean( isset($folder['path']) ? $folder['path'] : '') ?></td>
                    <td><?php echo $this->clean( isset($folder['formatted_pattern']) ? $folder['formatted_pattern'] : '')  ?></td>
                    <td><?php echo $this->clean( isset($folder['exclude']) ? implode(', ',$folder['exclude']) : '')  ?></td>
                    <td class="controls">
                        <?php if ($id > 0): ?>
                        <a href="?page=settings&amp;type=folders&amp;action=promote&amp;id=<?php echo (int)$id; ?>" class="action-promote"><?php echo $this->locale->promote ?></a>
                        <?php else: ?>
                        <span class="action-promote action-inactive"><?php echo $this->locale->promote ?></span>
                        <?php endif; ?>
                        <?php if ( ( $id + 1 ) < $count ): ?>
                        <a href="?page=settings&amp;type=folders&amp;action=demote&amp;id=<?php echo (int)$id; ?>" class="action-demote"><?php echo $this->locale->demote ?></a>
                        <?php else: ?>
                        <span class="action-demote action-inactive"><?php echo $this->locale->demote ?></span>
                        <?php endif; ?>
                        <a href="?page=settings&amp;type=folders&amp;action=edit&amp;id=<?php echo (int)$id; ?>"><?php echo $this->locale->edit ?></a>
                        <a href="?page=settings&amp;type=folders&amp;action=delete&amp;id=<?php echo (int)$id; ?>"><?php echo $this->locale->delete ?></a>
                    </td>
                <?php endif; ?>
                 </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </form>

    <form action="?page=settings" method="post">
        <div class="clearfix">
        <h3><?php echo $this->locale->create_folder ?></h3>
        <input type="hidden" name="type" value="folders" />
        <input type="hidden" name="action" value="create" />
        <div class="block">
            <?php field(false, $this->request, 'name', true, $this->locale->folder_name, $this->locale->tooltip_folder_name); ?>
        </div>
        <div class="block">
        <?php field(false, $this->request, 'path', true, $this->locale->folder_path, $this->locale->tooltip_folder_path); ?>
        </div>
        <div class="block">
        <?php field(false, $this->request, 'pattern', false, $this->locale->folder_pattern, $this->locale->tooltip_folder_pattern); ?>
        </div>
        <div class="block">
        <?php field(false, $this->request, 'exclude', false, $this->locale->folder_exclude, $this->locale->tooltip_folder_exclude); ?>
        </div>
        <input type="submit" value="<?php echo $this->locale->save ?>"/>
        </div>
    </form>
</div>
<?php
    function field($data, $post, $key, $required=false, $label = null, $tooltip = null){
        $required = $required ? 'required' : '';
        $str = $post->$key ? $post->key : (isset($data[$key]) ? $data[$key] : '');
        if (is_array($str)){
            $str = implode(', ',$str);
        }
        $str = '<input type="text" name="'.$key.'" value="'. htmlspecialchars(html_entity_decode($str, ENT_QUOTES, 'UTF-8'), ENT_QUOTES,'UTF-8') . '" '.$required.'/>';
        if ($tooltip){
            $str .= tooltip($tooltip);
        }
        if ($label){            
            $str = "<label>".$label. $str. "</label>";
        }
        echo $str;
    }
    function tooltip($text){
        return '<small class="tooltip-wrap"><span class="tooltip">'. $text. '</span></small>';
    }