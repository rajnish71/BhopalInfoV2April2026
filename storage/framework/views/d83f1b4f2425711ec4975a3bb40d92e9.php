<?php $__env->startSection('content'); ?>

    <?php
        $sections = theme_sections('home');
    ?>

    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if ($__env->exists(
            'themes.' . active_theme() . '.sections.' . $section,
            [
                'heroPrimary' => $heroPrimary,
                'heroSecondary' => $heroSecondary,
                'news' => $news,
                'upcomingEvents' => $upcomingEvents,
            ]
        )) echo $__env->make(
            'themes.' . active_theme() . '.sections.' . $section,
            [
                'heroPrimary' => $heroPrimary,
                'heroSecondary' => $heroSecondary,
                'news' => $news,
                'upcomingEvents' => $upcomingEvents,
            ]
        , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(theme_view('layouts.app'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/bhopal-admin-core/current/resources/views/themes/modern2026/pages/home.blade.php ENDPATH**/ ?>