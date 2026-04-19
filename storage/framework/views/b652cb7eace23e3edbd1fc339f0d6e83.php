<?php if($heroSecondary && count($heroSecondary)): ?>
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">

    <?php $__currentLoopData = $heroSecondary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="/news/<?php echo e($post->slug ?? $post->id); ?>">
        <div style="position:relative; border-radius:12px; overflow:hidden;">

            <img 
                src="<?php echo e($post->featured_image && file_exists(public_path('storage/'.$post->featured_image)) ? asset('storage/'.$post->featured_image) : asset('images/hero.jpg')); ?>"
                style="width:100%; height:200px; object-fit:cover; display:block;"
                onerror="this.onerror=null;this.src='<?php echo e(asset('images/hero.jpg')); ?>';">

            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.8), transparent);"></div>

            <div style="position:absolute; bottom:8px; left:8px; right:8px; color:#fff; font-size:12px; font-weight:600;">
                <?php echo e(\Illuminate\Support\Str::limit($post->title, 60)); ?>

            </div>

        </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>
<?php endif; ?>
<?php /**PATH /var/www/bhopal-admin-core/current/resources/views/themes/modern2026/sections/hero-secondary.blade.php ENDPATH**/ ?>