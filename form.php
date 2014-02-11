<div class="wrap">
    <?php echo "<h2>" . __( 'Virtual Pages and Templates Settings' ) . "</h2>";?>  
    <?php 
    $options = get_option('vpt_options');
    $virtualpageurl = '/shop/%postname%';
    $post_type = 'page';
    $page_template = null;
    $spinmethod = 'domainpage';
    $use_custom_permalink_structure = FALSE;
    $affect_search = TRUE;

    if (!empty($options)){
        $virtualpageurl = $options['virtualpageurl'];
        $post_type = $options['post_type'];
        $page_template = $options['page_template'];
        $spinmethod = $options['spinmethod'];
        $use_custom_permalink_structure = isset($options['use_custom_permalink_structure']) ? $options['use_custom_permalink_structure'] : FALSE;      
        $affect_search = isset($options['affect_search']) ? $options['affect_search'] : FALSE;
    }

    $spinmethods = array(
        'domainpage' => 'domain page (default)',
        'every second' => 'every second',
        'every minute' => 'every minute',
        'hourly' => 'hourly',
        'daily' => 'daily',
        'weekly' => 'weekly',
        'monthly' => 'monthly',
        'annually' => 'annually',
        'false' => 'always spin'
    );
    ?>
    <form id="vpt_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" class="validate">
        <input type="hidden" name="vpt_hidden" value="Y"/>  
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label for="virtualpageurl"><?php _e("Virtual Page URL: " ); ?></label></th>
                <td>
                    <?php if ($use_custom_permalink_structure) $checked = 'checked="checked"'; else $checked = '';?>
                    <label for="users_can_register"><input type="checkbox" value="1" id="use_custom_permalink_structure" <?php echo $checked;?> name="use_custom_permalink_structure">Use custom permalink structure</label>
                    <div id="use_permalink_label">
                        <p class="description">Virtual pages uses current permalink structure.</p>
                    </div>
                    <div id="use_custom_pageurl">
                    <input type="text" class="regular-text code" value="<?php echo $virtualpageurl?>" id="virtualpageurl" name="virtualpageurl"/>
                    <p class="description">Virtual pages will only be shown on this url.</p>
                    </div>
                </td>
            </tr>

            <tr>
            <th scope="row"><?php _e("Post or Post? " ); ?></th>
            <td>
                <fieldset><legend class="screen-reader-text"><span><?php _e("Post or Post? " ); ?></span></legend>
                <?php if ($post_type == 'page') $checked = 'checked="checked"'; else $checked = '';?>
                <label title="Page"><input type="radio" <?php echo $checked;?> value="page" name="post_type"> <span>Page</span></label><br>
                <?php if ($post_type == 'post') $checked = 'checked="checked"'; else $checked = '';?>
                <label title="Post"><input type="radio" <?php echo $checked;?>  value="post" name="post_type"> <span>Post</span></label><br>
                <p class="description">Specify if the virtual page should act as a page or as a post</p>
                </fieldset>
            </td>
           
            <tr valign="top">
            <th scope="row"><label for="default_role"><?php _e("Page Template: " ); ?></label></th>
                <td>
                <?php $posts = new WP_Query( array( 'post_status' => array('draft'), 'post_type' => array('post') ) );?>
                <?php $pages = new WP_Query( array( 'post_status' => array('draft'), 'post_type' => array('page') ) );?>

                <select id="page_template" name="page_template" style="width: 25em">
                    <?php if (!empty($posts->posts)) :?>
                        <optgroup label="Posts">
                        <?php foreach ($posts->posts as $post):?>
                            <?php if ($page_template == $post->ID) $selected = 'selected="selected"'; else $selected = '';?>
                            <option value="<?php echo $post->ID;?>" <?php echo $selected;?>><?php _e($post->post_title);?></option>
                        <?php endforeach;?>
                        </optgroup>
                    <?php endif;?>
                    <?php if (!empty($pages->posts)) :?>
                        <optgroup label="Pages">
                        <?php foreach ($pages->posts as $page):?>
                            <?php if ($page_template == $page->ID) $selected = 'selected="selected"'; else $selected = '';?>
                            <option value="<?php echo $page->ID;?>" <?php echo $selected;?>><?php _e($page->post_title);?></option>
                        <?php endforeach;?>
                        </optgroup>
                    <?php endif;?>
                </select>
                <p class="description">Specify an existing post or page (one that isn’t published) that will be used as a template.</p>
                </td>
            </tr>

            <th scope="row"><label for="spinmethod"><?php _e("Spin Method: " ); ?></label></th>
                <td>
                <select id="spinmethod" name="spinmethod"  style="width: 25em">
                    <?php foreach ($spinmethods as $spinid => $spinvalue) :?>
                        <?php if ($spinmethod == $spinid) $selected = 'selected="selected"'; else $selected = '';?>
                        <option value="<?php echo $spinid?>" <?php echo $selected;?>><?php echo $spinvalue;?></option>
                    <?php endforeach;?>
                </select>
                <p class="description">Specify the spin method, such as domainpage, always spin, every minute, etc..</p>
                </td>
            </tr>

            <th scope="row"><?php _e("Affect search result " ); ?></th>
            <td>
                <?php if ($affect_search) $checked = 'checked="checked"'; else $checked = '';?>
               <label for="affect_search"><input type="checkbox" value="1" id="affect_search" <?php echo $checked;?> name="affect_search"></label>
               <p class="description">Generate virtual page using the searched keyword if there are no pages found</p>
            </td>
            </tbody>
        </table>

        <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
    </form>
</div>