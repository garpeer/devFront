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
    <?php if ($this->projects): ?>        
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
        <h3><?php echo $this->locale->create ?></h3>
        <input type="hidden" name="type" value="projects" />
        <input type="hidden" name="action" value="create" />
        <?php field(false, $this->request, 'name', true, $this->locale->project_name); ?>
        <?php field(false, $this->request, 'path', true, $this->locale->project_path); ?>
        <?php field(false, $this->request, 'icon', false, $this->locale->project_icon); ?>
        <input type="submit" value="<?php echo $this->locale->save ?>"/>
        </div>
    </form>

    <form action="?page=settings" method="post">
        <h2><?php echo $this->locale->folders ?>:</h2>
    <?php if ($this->folders): ?>
            <?php foreach ($this->folders as $folder):  ?>
            <?php endforeach; ?> 
    <?php endif; ?>       
        <input type="hidden" name="type" value="folders" />
        <input type="submit" value="<?php echo $this->locale->save ?>"/>
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