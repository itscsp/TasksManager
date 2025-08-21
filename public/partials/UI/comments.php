<?php
/**
 * Task Comments Template
 */

// Don't allow direct access to this file
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="task-comments-section">  
    <?php if (comments_open()): ?>
        <div class="task-comment-form">
            <form class="comment-form ajax-comment-form" data-task-id="<?php echo get_the_ID(); ?>">
            <div class="comment-form-comment">
                <textarea id="comment-<?php echo get_the_ID(); ?>" name="comment" cols="45" rows="4" placeholder="Add a comment..." aria-required="true"></textarea>
    </div>
            <p class="form-submit">
                <button type="submit" class="tasks-btn">Post Comment</button>
            </p>
            <?php wp_nonce_field('tasks_ajax_nonce', 'comment_nonce'); ?>
        </form>
            
        </div>
    <?php endif; ?>

    <?php if (!empty($wp_query->comments)): ?>
        <div class="task-comments-list">
            <ul class="comment-list">
                <?php
                wp_list_comments(array(
                    'style'       => 'ul',
                    'short_ping'  => true,
                    'avatar_size' => 32,
                    'format'      => 'html5',
                    'callback'    => function($comment, $args, $depth) {
                        ?>
                        <li id="comment-<?php comment_ID(); ?>" <?php comment_class('task-comment'); ?>>
                            <article class="comment-body">
                                <div class="comment-meta">
                                    <div>

                                        <?php echo get_avatar($comment, 32); ?>
                                    </div>
                                    <div>

                                        <div class="comment-author">
                                            <?php echo get_comment_author_link(); ?>
                                        </div>
                                        <div class="comment-metadata">
                                            <time datetime="<?php echo get_comment_date('c'); ?>">
                                                <?php
                                                echo sprintf(
                                                    _x('%s ago', '%s = human-readable time difference', 'tasks'),
                                                    human_time_diff(get_comment_time('U'), current_time('timestamp'))
                                                );
                                                ?>
                                            </time>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-content">
                                    <?php comment_text(); ?>
                                </div>
                            </article>
                        </li>
                        <?php
                    }
                ));
                ?>
            </ul>

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
                <nav class="comment-navigation">
                    <div class="nav-previous"><?php previous_comments_link(__('Older Comments', 'tasks')); ?></div>
                    <div class="nav-next"><?php next_comments_link(__('Newer Comments', 'tasks')); ?></div>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
