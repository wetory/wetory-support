<?php
/**
 * Template to render team member posts grid item.
 *
 * @since      1.2.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
// Get data passed to template if any
$idx = isset($data->idx) ? $data->idx : 0;
$columns = isset($data->columns) ? $data->columns : 2;

// Prepare some data
$col_class = 'col-md-' . (12 / $columns);
$img_src = wetory_get_post_thumbnail_url();

// Parse team member data
$team_member = array(
    'position' => get_post_meta(get_the_ID(), 'position', true),
    'email' => get_post_meta(get_the_ID(), 'email', true),
    'phone' => get_post_meta(get_the_ID(), 'phone', true),
    'availability-status' => get_post_meta(get_the_ID(), 'availability-status', true),
    'facebook' => get_post_meta(get_the_ID(), 'facebook', true),
    'linkedin' => get_post_meta(get_the_ID(), 'linkedin', true),
    'twitter' => get_post_meta(get_the_ID(), 'twitter', true),
);
?> 
<div class="wetory-grid-item <?php echo $col_class; ?>">
    <div class="card">
        <img class="card-img-top" src="<?php echo $img_src; ?>" alt="Card image">
        <div class="card-body">
            <div class="team-member-social">
                <div class="team-member-social-block">
                    <?php if (!empty($team_member['facebook'])) { ?>
                        <a href="<?php echo $team_member['facebook']; ?>"><i class="fa fa-facebook"></i></a>
                    <?php } ?>
                    <?php if (!empty($team_member['twitter'])) { ?>
                        <a href="<?php echo $team_member['twitter']; ?>"><i class="fa fa-twitter"></i></a>
                    <?php } ?>
                    <?php if (!empty($team_member['linkedin'])) { ?>
                        <a href="<?php echo esc_url($team_member['linkedin']); ?>"><i class="fa fa-linkedin"></i></a>
                    <?php } ?>
                </div>
            </div>
            <h4 class="card-title"><?php the_title(); ?></h4>
            <div class="card-text">
                <div class="team-member-position">
                    <?php echo $team_member['position']; ?>
                </div>
                <div class="team-member-contact">
                    <a href="mailto:<?php echo $team_member['email']; ?>"><?php echo $team_member['email']; ?></a>
                    <br>
                    <a href="callto:<?php echo $team_member['email']; ?>"><?php echo $team_member['phone']; ?></a>
                </div>
            </div>
        </div>        
        <div class="card-footer team-member-availability <?php echo $team_member['availability-status']; ?>">            
            <?php echo $team_member['availability-status']; ?>
        </div>
    </div>
</div>