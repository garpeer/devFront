<form action="" method="post">
    <h2><?php echo $this->locale->settings ?></h2>
    <label><?php echo $this->locale->theme ?>:
    <select name="theme">
        <?php if ($this->themes):
            foreach($this->themes as $theme):
            ?>
                <option value="<?php echo $theme; ?>"><?php echo $theme; ?></option>
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
                <option value="<?php echo $locale; ?>"><?php echo $this->locale->$locale; ?></option>
        <?php
            endforeach;
            endif;
        ?>
    </select>
    </label>
    <input type="submit" value="<?php echo $this->locale->save ?>"/>
</form>
