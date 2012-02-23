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
        
            <?php foreach ($this->projects as $project):  ?>
         <form action="?page=settings" method="post">
        <div>
            <input type="hidden" name="type" value="projects" />
            <input type="hidden" name="action" value="update" />
            <?php project_field($this->data, 'name', $this->locale->project_name); ?>
            <?php project_field($this->data, 'path', $this->locale->project_path); ?>
            <?php project_field($this->data, 'icon', $this->locale->project_icon); ?>
            <input type="submit" value="<?php echo $this->locale->save ?>"/>
            
        </div>
            <?php endforeach; ?> 
    <?php endif; ?>
    <form action="?page=settings" method="post">
        <div>
        <h3><?php echo $this->locale->create ?></h3>
        <input type="hidden" name="type" value="projects" />
        <input type="hidden" name="action" value="create" />
        <?php project_field($this->data, 'name', $this->locale->project_name); ?>
        <?php project_field($this->data, 'path', $this->locale->project_path); ?>
        <?php project_field($this->data, 'icon', $this->locale->project_icon); ?>
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
    function project_field($data, $key, $label){
        $tmp = $data->$key ? htmlspecialchars($data->$key, ENT_QUOTES, 'UTF-8') : '';
        echo '<label>'. $label .':<input type="text" name="'.$key.'" value="'. $tmp . '"/></label>';
    }