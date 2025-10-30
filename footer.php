
        </main>
    </div><!-- container -->
    <footer class="text-center py-3 mt-auto bg-primary-subtle">
        <p class="mb-0"><small>©2025 Ryo Miyashita</small></p>
    </footer>
    <?php
        if(isset($script)) :
            foreach($script as $path) :
    ?>
        <script src="<?= h($path) ?>"></script>
    <?php
            endforeach;
        endif;
    ?>
</body>
</html>