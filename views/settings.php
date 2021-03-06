<h2>Poller Express Settings</h2>

<form class="poex_settings_form" method="post">

    <!-- this hidden action will trigger our processing logic -->
    <input type="hidden" name="action" value="save_poex_settings" />

    <!-- this nonce will ensure that our submission is coming from the right place -->
    <input type="hidden" name="poex_settings_nonce" value="<?php echo $poex_settings_nonce; ?>" />

    <!-- we use an internal function clean_var to place santized text into our value="" attributes -->
    <p>
        <label for="poex[title]">Title:</label>
        <input type="text" name="poex[title]" value="<?php $this->clean_var( $settings['title'] ); ?>" />
    </p>
    <p>
        <label for="poex[question]">Question:</label>
        <input type="text" name="poex[question]" value="<?php  $this->clean_var( $settings['question'] ); ?>" />
    </p>
    <p>
        <label for="poex[input_type]">Allow multiple votes:</label>
        <input type="radio" name="poex[input_type]" value="radio" <?php echo $radio_type_checked; ?> />
        One vote only
    </p>
    <p>
        <label>&nbsp;</label>
        <input type="radio" name="poex[input_type]" value="checkbox" <?php echo $checkbox_type_checked; ?> />
        Multiple votes
    </p>
    <p>
        <label for="poex[answers]">Possible Answers:</label>
    </p>
    <div class="poex_answers">
        <p class="poex_answer">
            <label>&nbsp;</label>
            <input class="answer_input" type="text" name="poex[answers][]" value="" />
            <input type="button" value="X" class="poex_answer_remove" />
        </p>
    </div>
    <p>
        <label>&nbsp;</label>
        <input type="button" value="Add an Answer" class="poex_answer_add" />
    </p>
    <p>
        <input type="submit" value="Save Settings" />
    </p>
</form>
<script>
    var poexAnswers = <?php echo json_encode( $this->clean_var($settings['answers']) ); ?>;
    jQuery(document).ready(
        function() {
            poexSettingsInit();
        }
    );
</script>