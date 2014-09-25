<div class="poex_poll">
    <h2 class="poex_title"><?php echo stripslashes( $settings['title'] ); ?></h2>
    <p class="poex_question">
        <?php echo $settings['question']; ?>
    </p>
    <form class="poex_vote_form" method="post">
        <input type="hidden" name="action" value="poex_vote" />
        <input type="hidden" name="poex_vote_nonce" value="<?php echo $nonce; ?>" />
        <?php foreach( $settings['answers'] as $a ) : ?>
        <p>
            <input type="<?php echo $settings['input_type']; ?>" name="poex_vote[]" value="<?php echo $a; ?>" />
            <?php echo stripslashes( $a ); ?>
        </p>
        <?php endforeach; ?>
        <p>
            <input type="submit" value="Cast your vote" />
        </p>
    </form>
</div>