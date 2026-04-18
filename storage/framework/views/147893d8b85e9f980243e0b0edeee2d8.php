<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title', 'Bhopal Info'); ?></title>

    <link rel="icon" type="image/png" href="/favicon.png?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #FAFAFA;
            font-size: 14px;
            color: #111;
        }
    </style>
</head>

<body class="antialiased">

<div style="max-width:1100px; margin:0 auto; padding:0 20px;">

    
    <?php echo $__env->make('themes.modern2026.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->yieldContent('content'); ?>

    
    <?php echo $__env->make('themes.modern2026.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>

</body>
</html>
<?php /**PATH /var/www/bhopal-admin-core/current/resources/views/themes/modern2026/layouts/app.blade.php ENDPATH**/ ?>