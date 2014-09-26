<div class="poex_poll">
    <h2 class="poex_title"><?php echo stripslashes( $settings['title'] ); ?></h2>
    <p class="poex_question">
        <?php echo $settings['question']; ?>
    </p>
    <!-- our canvas element will be populated by our awesome charts.js -->
    <canvas id="poex_results" width="600px" height="200px">

    </canvas>
</div>