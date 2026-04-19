<?php if($heroPrimary): ?>
<a href="/news/<?php echo e($heroPrimary->slug ?? $heroPrimary->id); ?>" style="display:block; margin:20px 0;">
    <div style="position:relative; border-radius:12px; overflow:hidden;">

        <img 
            src="<?php echo e($heroPrimary->featured_image && file_exists(public_path('storage/'.$heroPrimary->featured_image)) ? asset('storage/'.$heroPrimary->featured_image) : asset('images/hero.jpg')); ?>"
            style="width:100%; height:420px; object-fit:cover; display:block;"
            onerror="this.onerror=null;this.src='<?php echo e(asset('images/hero.jpg')); ?>';">

        <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.9), transparent);"></div>

        <div style="position:absolute; bottom:20px; left:20px; color:#fff;">
            <h2 style="font-size:26px; font-weight:900;">
                <?php echo e($heroPrimary->title); ?>

            </h2>

            <p style="font-size:12px;">
                <?php echo e(optional($heroPrimary->published_at)->diffForHumans()); ?>

            </p>
        </div>

    </div>
</a>
<?php endif; ?>
<?php /**PATH /var/www/bhopal-admin-core/current/resources/views/themes/modern2026/sections/hero.blade.php ENDPATH**/ ?>